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
use yii\imagine\Image;

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
            $post->user_id = Yii::$app->user->id;
            
            // Retrieves the uploaded image
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
                    
            if (isset($image->imageFile)) {
                
                // Save the extension
                $post->headerimage = "." . $image->imageFile->extension;
                
                // Save post in db
                $post->save();
                
                // Image name
                $imagename = TblImage::HEADER . TblImage::ORIGINAL . $post->headerimage;
                
                // Create the folder in which the image will be saved, and set route
                $directory = '../' . TblImage::getRoutePostImageFolder($post->post_id);
                BaseFileHelper::createDirectory($directory);
                $image->imageRoute = $directory . $imagename;
                                
                // Save image in directory
                $image->saveImage();
                
                // Thumbnail image name
                $imagename = TblImage::HEADER . TblImage::THUMBNAIL . $post->headerimage;
                
                // Create and save the thumbnail
                Image::thumbnail($image->imageRoute, 90, 90)
                    ->save(($directory . $imagename), ['quality' => 50]);
                
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
     * Image upload handler
     * NOTE: IS THIS NECESSARY? REVIEW LATER
     * 
     * @return type
     */
    public function actionUploadImage () {
        
        $accepted_origins = array("http://localhost", "http://192.168.1.1", "http://example.com");
        
        $imageFolder = TblImage::UPLOADSROOT . '/' . Yii::$app->user->identity->user_id . '/';
        
        reset ($_FILES);
        $temp = current($_FILES);
        
        if (is_uploaded_file($temp['tmp_name'])){
            
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // same-origin requests won't set an origin. If the origin is set, it must be valid.
                if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                  header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
                } else {
                  header("HTTP/1.0 403 Origin Denied");
                  return;
                }
            }

            // Cookies
            // header('Access-Control-Allow-Credentials: true');
            // header('P3P: CP="There is no P3P policy."');

            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                header("HTTP/1.0 500 Invalid file name.");
                return;
            }

            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("jpg", "png"))) {
                header("HTTP/1.0 500 Invalid extension.");
                return;
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $filetowrite = $imageFolder . $temp['name'];
            move_uploaded_file($temp['tmp_name'], $filetowrite);

            // Respond to the successful upload with JSON
            echo json_encode(array('location' => $filetowrite));
          
        } else {
            // Notify editor that the upload failed
            header("HTTP/1.0 500 Server Error");
        }
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
                
                // Save the extension
                $post->headerimage = "." . $image->imageFile->extension;
                
                // Save post in db
                $post->save();
                
                // Image name
                $imagename = TblImage::HEADER . TblImage::ORIGINAL . $post->headerimage;
                
                // Create the folder in which the image will be saved, and set route
                $directory = '../' . TblImage::getRoutePostImageFolder($post->post_id);
                BaseFileHelper::createDirectory($directory);
                $image->imageRoute = $directory . $imagename;
                                
                // Save image in directory
                $image->saveImage();
                
                // Thumbnail image name
                $imagename = TblImage::HEADER . TblImage::THUMBNAIL . $post->headerimage;
                
                // Create and save the thumbnail
                Image::thumbnail($image->imageRoute, 90, 90)
                    ->save(($directory . $imagename), ['quality' => 50]);
                
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
            
            // Deleting images of the post
            BaseFileHelper::removeDirectory(
                '../' . TblImage::getRoutePostImageFolder($p));
            
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
