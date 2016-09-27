<?php
   use yii\bootstrap\ActiveForm;
   use yii\bootstrap\Html;
   $this->title = 'Post Compose';
?>

<!-- Post composing view -->

<div>
    <h2>Post composing</h2>
    <p>
        State your business, your doubts, your relevant info, your word.
        This is just a <code>textarea</code> without editing or post-editing capabilites.
    </p>
</div>
<div class = "row">
   <div class = "col-lg-5">
       
        <?php $form = ActiveForm::begin(['id' => 'post-compose-form']); ?>
            <?= $form->field($model, 'title') ?>
            <?= $form->field($model, 'content')->textarea(['rows' => 12]) ?>

            <!-- Displays a 'publish' or 'edit + cancel' button
            depending on 'edit' mode -->
       
            <?php if (!$edit): ?>
            <div class = "form-group">
                <?= Html::submitButton('Publish post', [
                   'class' => 'btn btn-primary',
                   'name' => 'publish-button']) ?>
            </div>
            <?php else: ?>
            <div class = "form-group">
                <?= Html::submitButton('Edit post', [
                'class' => 'btn btn-warning',
                'name' => 'edit-button']) ?>
            </div>
                
            <?php endif; ?>
        <?php ActiveForm::end(); ?>
       
   </div>
</div>

