<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
?>

<!-- Comment composing form -->

<div class = "row">
   <div class = "col-lg-5">
      <?php $form = ActiveForm::begin(['id' => 'comment-compose-form']); ?>
         <?= $form->field($model, 'content')->textarea() ?>
         
         <div class = "form-group">
            <?= Html::submitButton('Comment', [
               'class' => 'btn btn-primary',
               'name' => 'publish-button']) ?>
         </div>
      <?php ActiveForm::end(); ?>
   </div>
</div>

