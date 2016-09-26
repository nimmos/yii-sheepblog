<?php
   use yii\bootstrap\ActiveForm;
   use yii\bootstrap\Html;
   $this->title = 'Post Compose';
?>
<div>
	<h2>Post composing</h2>
	<p>
		[[Info about composing posts in this blog]]
	</p>
</div>
<div class = "row">
   <div class = "col-lg-5">
      <?php $form = ActiveForm::begin(['id' => 'post-compose-form']); ?>
         <?= $form->field($model, 'title') ?>
         <?= $form->field($model, 'content')->textarea() ?>
         
         <div class = "form-group">
            <?= Html::submitButton('Publish post', [
               'class' => 'btn btn-primary',
               'name' => 'publish-button']) ?>
         </div>
      <?php ActiveForm::end(); ?>
   </div>
</div>

