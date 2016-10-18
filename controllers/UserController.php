<?php

namespace app\controllers;

use app\models\TblComment;
use app\models\TblImage;
use app\models\TblPost;
use app\models\TblUser;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseFileHelper;
use yii\web\Controller;
use yii\web\UploadedFile;

class UserController extends Controller
{
    
    /**
     * Yii2 executes this function before Controller initialization.
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
     * This will be called before every action
     * 
     * @return type
     */
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        return true;
    }
    
    /**
     * Displays the user profile page.
     * 
     * @return type
     */
    public function actionProfile ()
    {
        
        // Retrieve user data
        
        $user = TblUser::findById(Yii::$app->user->id);
        $newimage = new TblImage();
        $user->setScenario('update');
        
        // Retrieve user profile image
        
        if(isset($user->userimage))
        {
            $directory = TblImage::routeUserImageDir($user->user_id);
            $image = $directory . TblImage::PROFILE . TblImage::ORIGINAL . $user->userimage;
        } else {
            $image = "/blogheader.thumbnail.jpg";
        }
        
        // Retrieve user post list
        
        $dataProvider = new ActiveDataProvider([
            'query' => ($_SESSION["role"]=='admin') ? 
                TblPost::find()
                :
                TblPost::find()
                ->where(['user_id' => Yii::$app->user->id]),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [ 'defaultOrder' => [ 'time' => SORT_DESC ] ],
        ]);
        
        // If POST, check if it loads a user form or an image form
        
        if ($user->load(Yii::$app->request->post())
            ||
            $newimage->load(Yii::$app->request->post()))
        {
            
            // Retrieves the uploaded image
            $newimage->imageFile = UploadedFile::getInstance($newimage, 'imageFile');
            
            // Save the image extension
            if (isset($newimage->imageFile)) {
                $user->userimage = "." . $newimage->imageFile->extension;
            }
            
            // Save user data (and profile image if everything went well)
            if (TblUser::saveUser($user, false) && isset($newimage->imageFile))
            {
                $newimage->saveProfileImage($user);
            }
            
            return $this->refresh();
        }
        
        return $this->render('profile', [
            'user' => $user,
            'image' => $image,
            'newimage' => $newimage,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * It completely deletes an user
     * 
     * @return type
     */
    public function actionDeleteUser ()
    {
        
        $user_id = Yii::$app->user->id;
        
        // Delete user commentaries
        
        $comments = TblComment::findAll(['user_id' => $user_id]);
        
        if (isset($comments)) {
            foreach($comments as $comment) {
                $comment->delete();
            }
        }
        
        // Delete user posts (with its images)
        
        $posts = TblPost::findAll(['user_id' => $user_id]);
        
        if (isset($posts)) {
            foreach($posts as $post) {
                Yii::$app->runAction('/post/delete-post', ['p' => $post->post_id]);
            }
        }
        
        // Delete image folders
        
        $directory[] = TblImage::UPLOADSROOT . $user_id . '/';
        $directory[] = TblImage::THUMBSROOT . $user_id . '/';
        $directory[] = TblImage::IMAGESROOT . $user_id . '/';
        
        foreach( $directory as $current )
        {
            if(file_exists($current))
            {
                BaseFileHelper::removeDirectory($current);
            }
        }
        
        // Revoke role
        
        $auth = Yii::$app->authManager;
        $auth->revokeAll($user_id);
        
        // Delete user from the database
        TblUser::findById($user_id)->delete();
        
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
