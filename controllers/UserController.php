<?php

namespace app\controllers;

use app\models\TblImage;
use app\models\TblPost;
use app\models\TblUser;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

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
    public function actionProfile()
    {
        // Retrieve user data
        $user = TblUser::findById(Yii::$app->user->id);
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
        
        // For updating user data
        $newimage = new TblImage();
        if ($user->load(Yii::$app->request->post()))
        {
            TblUser::saveUser($user, $newimage, false);
            return $this->goBack();
        }
        
        return $this->render('profile', [
            'user' => $user,
            'image' => $image,
            'dataProvider' => $dataProvider,
        ]);
    }

}
