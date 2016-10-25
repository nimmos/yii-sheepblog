<?php

use app\models\TblTag;
use dosamigos\tinymce\TinyMce;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

    $this->title = 'Post Compose';

    // This is for Responsive Filemanager:
    // Checks if the user is an admin and establishes root folder for images
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if($_SESSION["role"]=='admin')
    {
        $_SESSION["RF"]["subfolder"] = "";
    } else {
        $_SESSION["RF"]["subfolder"] = Yii::$app->user->id;
    }

?>

<style>
    
    /////////////////////////////////
    // HOW DO I DO THIS
    /////////////////////////////////
    .field-tag-input>.control-label {
        clear: left;
    }
    /////////////////////////////////
    
    .bootstrap-tagsinput > input {
        
    }
    
    .tag {
        float: left;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-right: 15px;
        padding-left: 15px;
        margin: 5px 5px;
        border-radius: 5px;
    }
    
</style>

<!-- Post composing info -->

<div>
    <h2>Post composing</h2>
    <p>State your business, your doubts, your relevant info, your word.</p>
</div>

<!-- Post composing form -->

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
                    
                    // File type for the filemanager
                    'file_browser_callback_types' => 'image',
                    'file_picker_types' => 'image',
                                        
                    // Responsive Filemanager
                    'external_filemanager_path' => '/extensions/filemanager/',
                    'external_plugins' => ['filemanager' => '/extensions/filemanager/plugin.min.js'],
                    'filemanager_title' => 'Responsive Filemanager',
                ]
            ]) ?>
            
            <!-- Tag input -->
            
            <?= $form->field($post, 'tags')->textInput([
                'id' => 'tag-input',
                'data-role' => 'tagsinput',
                'value' => TblTag::turnString($post->tags),
            ]);?>
            
            <!-- Upload an image -->
            
            <?= $form->field($image, 'imageFile')->fileInput() ?>
            
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

<script>
    
    // Prevent enter key from submitting form
    // affects the ENTIRE form
    $('#post-compose-form').on('keyup keypress', function(event) {
        var keyCode = event.keyCode || event.which;
        if (keyCode === 13) { 
            event.preventDefault();
            return false;
        }
    });
    
    $(document).ready(function(){
        
        // CSS of tags input
        
        //$('.bootstrap-tagsinput').css("clear","left");
        $('.bootstrap-tagsinput > input').css({
            "font-size" : "medium",
            "margin-top" : "10px",
            "margin-bottom" : "10px"
        });
    });
    
</script>
