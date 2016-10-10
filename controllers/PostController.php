<?php

namespace app\controllers;

// Models for the blog
use app\models\TblComment;
use app\models\TblImage;
use app\models\TblPost;
use app\models\TblUser;

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
        $posts = new ActiveDataProvider([
            'query' => TblPost::find()->orderBy('time DESC'),
            'pagination' => [ 'pageSize' => 3 ],
        ]);
                        
        return $this->render('index', [
            'posts' => $posts,
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
        
        // Obtain the comments of the post
        $comments = new ActiveDataProvider([
            'query' => TblComment::find()
                ->where(['post_id' => $p]),
        ]);
        
        // Obtain author of the post
        $author = TblUser::findUsernameById($post->user_id);
        
        // Publish new comment if POST retrieves one
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
        
        return $this->render('post', [ 'post' => $post, 'author' => $author,
            'comment' => $comment, 'comments' => $comments,
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
            $post->user_id = Yii::$app->user->id;
            
            // Retrieves the uploaded image
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
            
            if (isset($image->imageFile)) {
                
                TblPost::savePost($post, $image);
                
            } else {
                $post->save();
            }
            
            return $this->goBack();
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
        // Obtain the post to edit (and its header image)
        $post = TblPost::getPostById($p);
        $image = new TblImage();

        if ($post->load(Yii::$app->request->post()) && $post->validate())
        {            
            // Retrieves the uploaded image
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
                    
            if (isset($image->imageFile)) {
                
                TblPost::savePost($post, $image);
                
            } else {
                $post->save();
            }
            
            return $this->redirect(['post/post', 'p' => $p]);
        }
        
        return $this->render('post-compose', [
            'post' => $post,
            'image' => $image,
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
            
            // Delete images of the post
            $directory = TblImage::getRoutePostImageFolder($post->user_id, $p);
            if(file_exists($directory))
            {
                BaseFileHelper::removeDirectory($directory);
            }
            
            // Delete thumbnails of Responsive Filemanager
            $directory = TblImage::getRoutePostRFMThumbFolder($post->user_id, $p);
            if(file_exists($directory))
            {
                BaseFileHelper::removeDirectory($directory);
            }
            
            // Delete comments
            $comments = TblComment::findAll(['post_id' => $p]);
            foreach($comments as $comment) {
                $comment->delete();
            }
            
            // Delete post
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
