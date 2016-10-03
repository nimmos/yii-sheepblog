<?php

namespace app\controllers;

// Models for the blog

use app\models\TblComment;
use app\models\TblPost;
use app\models\TblUser;
use app\models\TblImage;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseFileHelper;
use yii\web\Controller;
use yii\web\UploadedFile;

class PostController extends Controller
{
    /**
     * Default index.
     * 
     * @return type
     */
    public function actionIndex()
    {
        // Retrieve all posts in ActiveDataProvider
        // sorted from recent posts to older posts
        $dataProvider = new ActiveDataProvider([
            'query' => TblPost::find()->orderBy('time DESC'),
            'pagination' => [ 'pageSize' => 3 ],
        ]);
                        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays post view.
     *
     * @param type $p post_id
     * @return type
     */
    public function actionPost ($p)
    {
        
        // Obtain the required post by its id
        $post = TblPost::getPostById($p);
        
        // Publish new comments
        $comment = new TblComment();
        if ($comment->load(Yii::$app->request->post()) && $comment->validate())
        {
            $comment->post_id = $p;
            $comment->user_id = Yii::$app->user->id;
            if ($comment->save())
            {
                return $this->refresh();
            }
        }
        
        return $this->render('post', [
            'post' => $post, 'comment' => $comment,
            'author' => TblUser::findUsernameById($post->user_id),
            'comments' => TblComment::findAll(['post_id' => $p]),
        ]);
    }

    /**
     * Displays post composing page.
     *
     * @return string
     */
    public function actionPostCompose ()
    {
        $post = new TblPost();
        $image = new TblImage();

        if ($post->load(Yii::$app->request->post()) && $post->validate())
        {
            // Retrieves the uploaded image
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
                    
            if (isset($image)) {
                
                $post->headerimage = "header." . TblImage::ORIGINAL
                        . "." . $image->imageFile->extension;
                
                // Create the folder in which the image is going to be saved
                // It uses the next id to the last post because post isn't saved yet
                $post_id = TblPost::getNextPostId();
                BaseFileHelper::createDirectory(TblImage::getFolderRoute($post_id));
                // Set the complete route
                $image->setRoute($post_id, $post->headerimage);
                $image->saveImage();
            }
            
            $post->user_id = Yii::$app->user->id;
                        
            if ($post->save())
            {
                return $this->goBack();
            }
        }
        
        return $this->render('post-compose', [
            'post' => $post,
            'image' => $image,
            'edit' => false
        ]);
    }

    /**
     * Edits a specified post
     * 
     * @param type $p
     * @return type
     */
    public function actionEditPost ($p)
    {
        // Obtain the post to edit
        $post = TblPost::getPostById($p);
                
        if ($post->load(Yii::$app->request->post())
                && $post->validate()
                && $post->save())
        {            
            return $this->redirect(['post/post', 'p' => $p]);
        }
        
        return $this->render('post-compose', [
            'post' => $post,
            'edit' => true
        ]);
    }
    
    /**
     * Deletes a specified post
     * 
     * @param type $p
     * @return type
     */
    public function actionDeletePost ($p)
    {
        $post = TblPost::findOne($p);
        if (isset($post)) {
            
            // Deleting images
            BaseFileHelper::removeDirectory(TblImage::getFolderRoute($p));
            
            // Deleting comments
            $comments = TblComment::findAll(['post_id' => $p]);
            foreach($comments as $comment) {
                $comment->delete();
            }
            
            // Deleting post
            $post->delete();
            return $this->goHome();
        }
        return $this->goBack();
    }
    
    /**
     * Deletes a specified comment
     * 
     * @param type $c
     * @param type $p
     * @return type
     */
    public function actionDeleteComment ($c, $p)
    {
        $comment = TblComment::findOne($c);
        if (isset($comment)) {
            $comment->delete();
            return $this->redirect(['post/post', 'p' => $p]);
        }
        return $this->goBack();
    }
}
