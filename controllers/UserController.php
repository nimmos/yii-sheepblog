<?php

namespace app\controllers;

use app\models\TblPost;

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
     * Displays the user profile page.
     * 
     * @return type
     */
    public function actionProfile()
    {
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
        
        return $this->render('profile', [
            'dataProvider' => $dataProvider
        ]);
    }

}
