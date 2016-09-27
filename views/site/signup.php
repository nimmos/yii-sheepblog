<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Sign up';
$this->params['breadcrumbs'][] = ['label' => 'Login', 'url' => ['login']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class = "row">
    <div class = "col-lg-5">
       
      <?php $form = ActiveForm::begin(['id' => 'signup']); ?>

         <?= $form->field($model, 'username') ?>
       
         <?= $form->field($model, 'email')->input('email') ?>
       
         <?= $form->field($model, 'password')->passwordInput() ?>
         
         <div class = "form-group">
            <?= Html::submitButton('Sign up', [
               'class' => 'btn btn-primary',
               'name' => 'signup-button']) ?>
         </div>
       
      <?php ActiveForm::end(); ?>
       
    </div>
</div>

