<?php

namespace app\controllers;

use app\models\TblComment;
use app\models\TblImage;
use app\models\TblPost;
use app\models\TblUser;
use Imagine\Gd\Imagine;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseFileHelper;
use yii\web\Controller;
use yii\web\UploadedFile;

class PostController extends Controller
{
    
    /**
     * Yii2 executes this function before Controller initialization
     */
    public function init() {
        
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        
        // Store the user role in a $_SESSION variable
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!empty($role)) {
            $_SESSION["role"] = current($role)->name;
        } else {
            $_SESSION["role"] = 'guest';
        }
        
        // Layout assignment depending on user role
        
        switch ($_SESSION["role"]) {
            case 'admin':
            case 'author':
                $this->layout = 'user';
                break;
            case 'guest':
            default:
                $this->layout = 'main';
        }
        
        parent::init();
    }
    
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
        
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $post = new TblPost();
        $image = new TblImage();

        if ($post->load(Yii::$app->request->post()) && $post->validate())
        {
            $post->user_id = Yii::$app->user->id;
            
            // Retrieves the uploaded image
            
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
            
            // If there's an image, save it
            
            if (isset($image->imageFile)) {
                
                TblPost::savePost($post, $image);
                
            } else {
                $post->save();
            }
            
            return $this->redirect(['post/post', 'p' => $post->post_id]);
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
        
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        // Obtain the post to edit
        
        $post = TblPost::getPostById($p);
        $image = new TblImage();

        // Update the post
        
        if ($post->load(Yii::$app->request->post()) && $post->validate())
        {
            
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
                    
            if (isset($image->imageFile)) {
                
                TblPost::savePost($post, $image);
                
            } else {
                $post->save();
            }
            
            return $this->goBack();
        }
        
        // Obtain the image of the post
        // THIS DOESN'T WORK YET
        
//        if (isset($post->headerimage)) {
//            
//            $path = TblImage::routePostHeaderDir($post->user_id, $post->post_id)
//                    . TblImage::HEADER . TblImage::ORIGINAL . $post->headerimage;
//            
//            $imagine = new Imagine();
//            $image->imageFile = $imagine->open($path);
//        }
        
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
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $post = TblPost::findOne($p);
        
        if (isset($post)) {
            
            // Delete folder for header and thumbnail of the post
            
            $path = TblImage::pathGenerator(
                    $post->user_id,
                    TblImage::PATH_POST,
                    null,
                    null,
                    $post->post_id);
            
            if(file_exists($path))
            {
                BaseFileHelper::removeDirectory($path);
            }
            
            // Delete images of the post
            
            $images = TblImage::getImagePathsFromContent($post->content, true);
            
            if(!empty($images))
            {
                foreach($images as $imagelink) {
                    $path = str_replace('\\', '/', getcwd()) . '/' . $imagelink;
                    if(file_exists($path)) {
                        unlink($path);
                    }
                }
            }
            
            // Delete comments
            
            $comments = TblComment::findAll(['post_id' => $p]);
            
            foreach($comments as $comment) {
                Yii::$app->runAction('/post/delete-comment',
                    ['c' => $comment->comment_id]);
            }
            
            // Delete post
            
            $post->delete();
            
            return $this->goBack();
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
    public function actionDeleteComment ($c)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $comment = TblComment::findOne($c);
        
        if (isset($comment))
        {
            
            // Delete images of the comment
        
            $images = TblImage::getImagePathsFromContent($comment->content, true);
            if(!empty($images))
            {
                foreach($images as $imagelink) {
                    $path = str_replace('\\', '/', getcwd()) . '/' . $imagelink;
                    if(file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            // Delete comment

            $comment->delete();
            
            return $this->goBack();
        }
    }
}
