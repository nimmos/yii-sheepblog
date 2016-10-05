<?php

use app\models\TblImage;
use dosamigos\tinymce\TinyMce;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

    $this->title = 'Post Compose';

    // This is for Responsive Filemanager
    
    // Checks if the user is an admin and establishes root folder
    session_start();
    if(Yii::$app->user->can('updatePost'))
    {
        $_SESSION["RF"]["subfolder"] = "";
    } else {
        $_SESSION["RF"]["subfolder"] = Yii::$app->user->id . "/images/post";
    }

?>

<!-- Post composing view -->

<div>
    <h2>Post composing</h2>
    <p>State your business, your doubts, your relevant info, your word.</p>
</div>
<div class = "row">
   <div class = "col-lg-12">
       
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
                        "insertdatetime table contextmenu paste image imagetools"
                    ],
                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image responsivefilemanager",
                    
                    'file_browser_callback_types' => 'image',
                    'file_picker_types' => 'image',
                    
                    // IMAGE UPLOAD
                    'images_upload_url' => '/web/index.php?r=post/upload-image',
                    'images_upload_base_path' => TblImage::UPLOADSROOT,
                    
                    // RESPONSIVE FILEMANAGER
                    'external_filemanager_path' => '/vendor/filemanager/',
                    'external_plugins' => ['filemanager' => '/vendor/filemanager/plugin.min.js'],
                    'filemanager_title' => 'Responsive Filemanager',
                ]
            ]) ?>
            
            <!-- Upload an image -->
            
            <?= $form->field($image, 'imageFile')->fileInput() ?>
            
            <?php if (!$edit): ?>
            
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
