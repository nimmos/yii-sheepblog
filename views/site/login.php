<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $user app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    
    <?php if (Yii::$app->session->hasFlash('signupSuccess')): ?>
    
        <h1>Done!</h1>
        
        <p><strong>You have successfully signed up to the blog. Try login now.</strong></p>
    
    <?php else: ?>

        <h1>Sign up...</h1>

        <p>If you aren't a user yet, please go sign up to our blog.</p>
        <p><?= Html::a('Sign up', ['/site/signup'], ['class' => 'btn btn-primary']) ?></p>

        <h1>...or Login</h1>
        <p>If you are already a member of this subtle community.</p>
    
    <?php endif; ?>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($user, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($user, 'password')->passwordInput() ?>

        <?= $form->field($user, 'rememberMe')->checkbox([
            'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ]) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
