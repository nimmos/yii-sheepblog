<?php

namespace app\controllers;

// Models for the blog forms

use app\models\ContactForm;
use app\models\LoginForm;
use app\models\TblImage;
use app\models\TblUser;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\BaseFileHelper;
use yii\web\Controller;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['post/index']);
    }

    /**
     * Displays post composing page.
     *
     * @return string
     */
    public function actionSignup ()
    {
        $user = new TblUser();
        $image = new TblImage();

        if ($user->load(Yii::$app->request->post()))
        {
            // Set security properties before performing save()
            $user->setPassword($user->password);
            $user->setAuthkey();
            
            // Retrieves the uploaded image
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');

            if (isset($image->imageFile)) {
                // Save the extension
                $user->userimage = "." . $image->imageFile->extension;
            }
            
            if ($user->validate() && $user->save())
            {
                // Role assignment
                $auth = Yii::$app->authManager;
                $role = $auth->getRole('author');
                $auth->assign($role, $user->user_id);
                
                // Create user post images folder
                $directory = TblImage::UPLOADSROOT
                        . $user->user_id
                        . TblImage::POSTROOT;
                if(!file_exists($directory))
                {
                    BaseFileHelper::createDirectory($directory);
                }
                
                // Sets success flash
                Yii::$app->session->setFlash('signupSuccess');
                
                if (isset($image->imageFile)) {

                    // Image name
                    $imagename = TblImage::PROFILE . TblImage::ORIGINAL . $user->userimage;

                    // Image directory
                    $directory = TblImage::routeUserImageDir($user->user_id);
                    if(!file_exists($directory))
                    {
                        BaseFileHelper::createDirectory($directory);
                    }

                    // Image path
                    $image->imageRoute = $directory . $imagename;

                    // Save image in directory
                    $image->saveImage();
                }
            }
            
            return $this->redirect(['site/login']);
        }
        return $this->render('signup', [
            'user' => $user,
            'image' => $image,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
