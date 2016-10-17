<?php

use app\models\TblUser;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = new TblUser();

?>

<?php $form = ActiveForm::begin([
    'id' => 'signup',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<div class="col-lg-1 col-lg-offset-1">
    <div class = "form-group">
        <?= Html::submitButton('Sign up', [
           'class' => 'btn btn-primary',
           'name' => 'signup-button']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
