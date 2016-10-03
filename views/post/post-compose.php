<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

use dosamigos\tinymce\TinyMce;

$this->title = 'Post Compose';
?>

<script>
tinymce.activeEditor.uploadImages(function(success) {
    document.forms[0].submit();
});
</script>

<!-- Post composing view -->

<div>
    <h2>Post composing</h2>
    <p>State your business, your doubts, your relevant info, your word.</p>
</div>
<div class = "row">
   <div class = "col-lg-8">
       
        <?php $form = ActiveForm::begin(['id' => 'post-compose-form']); ?>
       
            <!-- Title -->
       
            <?= $form->field($post, 'title') ?>
       
            <!-- Content editing (using TinyMCE extension) -->
       
            <?= $form->field($post, 'content')->widget(TinyMce::className(), [
                'options' => ['rows' => 12],
                'clientOptions' => [
                    'plugins' => [
                        "advlist autolink lists link charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime table contextmenu paste"
                    ],
                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                    'paste_data_images' => true,
                    'file_browser_callback_types' => 'image',
                    'file_picker_types' => 'image',
                    'images_upload_url' => '../../controllers/PostAcceptor.php',
                    'images_upload_base_path' => '../../uploads/images/post/example/',
                ]
            ]);?>
            
            <?php if (!$edit): ?>
            
            <!-- Upload an image -->
            
            <?= $form->field($image, 'imageFile')->fileInput() ?>

            <!-- Displays a 'publish' or 'edit + cancel' button
            depending on 'edit' mode -->
       
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
