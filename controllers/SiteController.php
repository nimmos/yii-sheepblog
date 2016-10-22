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
                $this->layout = 'guest';
        }
        
        parent::init();
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
            
            if (TblUser::saveUser($user, true))
            {
                // Sets success flash
                Yii::$app->session->setFlash('signupSuccess');
                
                // Create user images directories
            
                $paths[] = TblImage::pathGenerator($user->user_id, TblImage::PATH_USER);
                $paths[] = TblImage::pathGenerator($user->user_id, TblImage::ROOT_RFM_IMG);
                $paths[] = TblImage::pathGenerator($user->user_id, TblImage::ROOT_RFM_THUMB);
                
                foreach( $paths as $path )
                {
                    if(!file_exists($path))
                    {
                        BaseFileHelper::createDirectory($path);
                    }
                }
                
                // Save user profile image
                if (isset($image->imageFile)) {
                    $image->saveProfileImage($user);
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
