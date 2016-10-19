<?php

use dosamigos\tinymce\TinyMce;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

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

<!-- Comment composing form -->

<hr>

<div class = "row">
   <div class = "col-lg-12">
        <?php $form = ActiveForm::begin(['id' => 'comment-compose-form']); ?>

            <!-- Comment editing (using TinyMCE extension) -->
       
            <?= $form->field($model, 'content')->widget(TinyMce::className(), [
                'options' => ['rows' => 4],
                'clientOptions' => [
                    'plugins' => [
                        "advlist autolink lists link charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste image imagetools"
                    ],
                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                    
                    // File type for the filemanager
                    'file_browser_callback_types' => 'image',
                    'file_picker_types' => 'image',
                                        
                    // Responsive Filemanager
                    'external_filemanager_path' => '/extensions/filemanager/',
                    'external_plugins' => ['filemanager' => '/extensions/filemanager/plugin.min.js'],
                    'filemanager_title' => 'Responsive Filemanager',
                ]
            ]);?>

            <div class = "form-group">
                <?= Html::submitButton('Comment', [
                    'class' => 'btn btn-primary',
                    'name' => 'publish-button']) ?>
            </div>
        <?php ActiveForm::end(); ?>
   </div>
</div>

