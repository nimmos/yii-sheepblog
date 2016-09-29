<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

use dosamigos\tinymce\TinyMce;

$this->title = 'Post Compose';
?>

<!-- Post composing view -->

<div>
    <h2>Post composing</h2>
    <p>State your business, your doubts, your relevant info, your word.</p>
</div>
<div class = "row">
   <div class = "col-lg-5">
       
        <?php $form = ActiveForm::begin(['id' => 'post-compose-form']); ?>
       
            <?= $form->field($model, 'title') ?>
       
            <!-- Content editing (using TinyMCE extension) -->
       
            <?= $form->field($model, 'content')->widget(TinyMce::className(), [
                'options' => ['rows' => 12],
                //'language' => 'es',
                'clientOptions' => [
                    'plugins' => [
                        "advlist autolink lists link charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                    ],
                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                ]
            ]);?>

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

