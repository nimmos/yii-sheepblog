<?php

use dosamigos\tinymce\TinyMce;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
?>

<!-- Comment composing form -->

<div class = "row">
   <div class = "col-lg-5">
        <?php $form = ActiveForm::begin(['id' => 'comment-compose-form']); ?>

            <!-- Comment editing (using TinyMCE extension) -->
       
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

            <div class = "form-group">
                <?= Html::submitButton('Comment', [
                    'class' => 'btn btn-primary',
                    'name' => 'publish-button']) ?>
            </div>
        <?php ActiveForm::end(); ?>
   </div>
</div>

