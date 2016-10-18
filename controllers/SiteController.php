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
        $user->setScenario('signup');

        if ($user->load(Yii::$app->request->post()))
        {
            
            // Retrieves the uploaded image
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');
            
            // Save the image extension
            
            if (isset($image->imageFile)) {
                $user->userimage = "." . $image->imageFile->extension;
            }
            
            // Save user data (and profile image if everything went well)
            
            if (TblUser::saveUser($user, true) && isset($image->imageFile))
            {
                // Create user images directories
            
                $directory[] = TblImage::routeUserImageDir($user->user_id);
                $directory[] = TblImage::routeRFMImageDir($user->user_id);
                $directory[] = TblImage::routeRFMThumbDir($user->user_id);
                
                foreach( $directory as $current )
                {
                    if(!file_exists($current))
                    {
                        BaseFileHelper::createDirectory($current);
                    }
                }
                
                // Save user profile image
                $image->saveProfileImage($user);
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

        $user = new LoginForm();
        
        if ($user->load(Yii::$app->request->post()) && $user->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'user' => $user,
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
