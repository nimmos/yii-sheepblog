<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Sign up';
$this->params['breadcrumbs'][] = ['label' => 'Login', 'url' => ['login']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class = "row">
    <div class = "col-lg-10">
       
        <?php $form = ActiveForm::begin([
            'id' => 'signup',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

        <?= $form->field($user, 'username') ?>
        
        <?= $form->field($user, 'email')->input('email') ?>
        
        <?= $form->field($user, 'password')->passwordInput() ?>
                    
        <?= $form->field($image, 'imageFile')->fileInput() ?>
         
        <div class="col-lg-1 col-lg-offset-1">
            <div class = "form-group">
                <?= Html::submitButton('Sign up', [
                   'class' => 'btn btn-primary',
                   'name' => 'signup-button']) ?>
            </div>
        </div>
       
        <?php ActiveForm::end(); ?>
       
    </div>
</div>

