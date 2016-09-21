<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

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
        return $this->render('index');
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

        // Example: we introduce here a scenario for a GUEST user
        $model->scenario = contactForm::SCENARIO_EMAIL_FROM_GUEST;

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
        // Example: we pass some data to the view in the second parameter
        $email = "example@domain.com";
        $phone = "+666232323";
        return $this->render('about', [
            'email' => $email,
            'phone' => $phone,
            ]);
    }

    //////////////////////////////////////////////////////
    // BEYOND THIS POINT:
    // EXAMPLE ACTIONS FOR PRACTISING
    //////////////////////////////////////////////////////

    /**
    * Displays a message
    */
    public function actionSpeak ($message = "default message")
    {
        return $this->render("speak", ['message' => $message]);
    }

    /**
    * Defines the ContactForm model, set attributes
    * and display the model on the screen with var_dump.
    */
    public function actionShowContactModel () {
        $mContactForm = new \app\models\ContactForm();
        $mContactForm->name = "contactForm";
        $mContactForm->email = "user@gmail.com";
        $mContactForm->subject = "subject";
        $mContactForm->body = "body";
        var_dump($mContactForm->attributes);
        // Next line converts the model to JSON format
        // return \yii\helpers\Json::encode($mContactForm);
    }

    /**
    * Renders a test widget.
    */
    public function actionTestWidget () {
        return $this->render('testwidget');
    }

    /**
    * Displays a view with a registration form.
    */
    public function actionRegistration () {
        $mRegistration = new \app\models\RegistrationForm();
        return $this->render('registration', ['model' => $mRegistration]);
    }

    /**
    * Displays another view with a registration form,
    * this time for testing form validation
    */
    public function actionRegistrationValidate () {
        $mRegistration = new \app\models\RegistrationFormValidate();
        return $this->render('registration-validate', ['model' => $mRegistration]);
    }

    /**
    * This validates data dynamically
    */
    public function actionAdHocValidation () {
        $model = \yii\base\DynamicModel::validateData([
            'username' => 'John',
            'email' => 'jo@mail.com'
        ], [
            [['username', 'email'], 'string', 'max' => 12],
            ['email', 'email'],
        ]);
    
        if ($model->hasErrors()) {
            var_dump($model->errors);
        } else {
            echo "success";
        }
    } 
}
