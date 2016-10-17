<?php

use app\models\TblUser;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

    $this->title = "User profile";
    $this->params['breadcrumbs'][] = $this->title;
    // Set return URL
    Yii::$app->user->setReturnUrl(['/user/profile']);

?>

<div class="user-profile">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="row">
        <div class="col-lg-2">
            <img src="<?=$image?>" width="200" height="200"/>
        </div>
        <div class="col-lg-9" style="margin: 20px">
            <div class="user-update-off">
                <p><strong>Username: </strong><?=$user->username?></p>
                <p><strong>Role: </strong><?=  ucfirst($_SESSION["role"])?></p>
                <p>If you want to change something about your profile, click the next button.</p>
                <?= Html::button('Change user data', [
                    'id' => 'user-update-button',
                    'class' => 'btn btn-primary',
                ])?>
            </div>
            <div class="user-update-on">
                
                <?php $form = ActiveForm::begin([
                    'id' => 'user-update',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-1 control-label'],
                    ],
                ]); ?>
                
                    <?= $form->field($user, 'username') ?>

                    <?= $form->field($user, 'email')->input('email') ?>

                    <?= Html::submitButton('Save changes', [
                        'id' => 'user-update-confirm',
                        'class' => 'btn btn-warning',
                    ])?>
                
                    <?= Html::resetButton('Cancel', [
                        'id' => 'user-update-cancel',
                        'class' => 'btn btn-danger',
                    ])?>
                
                <?php ActiveForm::end(); ?>
                
            </div>
        </div>
    </div>

    <hr>
    
    <?= $this->render('../post/post-list', ['dataProvider' => $dataProvider])?>
    
</div>

<script>
    $(document).ready(function(){
        
        $('.user-update-on').hide();
        
        $('#user-update-button').click(function(){
            $('#tbluser-password').val("");
            $('.user-update-on').toggle();
            $('.user-update-off').toggle();
        });
        
        $('#user-update-cancel').click(function(){
            $('#tbluser-password').val("");
            $('.user-update-on').toggle();
            $('.user-update-off').toggle();
        });
    });
</script>
