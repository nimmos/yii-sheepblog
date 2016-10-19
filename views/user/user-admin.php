<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<style>
    #tblimage-imagefile {
        display:none;
    }
</style>

<div class="row">
    
    <!-- Profile image -->
    
    <div class="col-lg-2">
        
        <?php $imageform = ActiveForm::begin([
            'id' => 'user-image',
            'options' => ['class' => 'image-upload'],
        ]); ?>
        
            <label for="tblimage-imagefile">
                <img src="<?=$imagepath?>" width="200" height="200"
                data-toggle="tooltip" data-placement="bottom" title="Change image"/>
            </label>
            
            <?= $imageform->field($newimage, 'imageFile')->fileInput() ?>
            
        <?php ActiveForm::end(); ?>
    </div>
    
    <div class="col-lg-9" style="margin: 20px">
        
        <!-- User profile data -->
        
        <div class="user-data">
            
            <p><strong>Username: </strong><?=$user->username?></p>
            <p><strong>Role: </strong><?=  ucfirst($_SESSION["role"])?></p>
            <p>If you want to change something about your profile, click the next buttons.</p>
            
            <?= Html::button('Change user data', [
                'id' => 'show-update',
                'class' => 'btn btn-primary',
            ])?>
            <?= Html::button('Delete account', [
                'id' => 'show-delete',
                'class' => 'btn btn-link',
            ])?>
            
        </div>
        
        <!-- User account updating -->
        
        <div class="user-update">

            <?php $userform = ActiveForm::begin([
                'id' => 'user-update',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>

                <?= $userform->field($user, 'username') ?>

                <?= $userform->field($user, 'email')->input('email') ?>
            
                <?= $userform->field($user, 'password')->passwordInput() ?>

                <?= Html::submitButton('Save changes', [
                    'id' => 'confirm-update',
                    'class' => 'btn btn-warning',
                ])?>

                <?= Html::resetButton('Cancel', [
                    'id' => 'cancel-update',
                    'class' => 'btn btn-danger',
                ])?>

            <?php ActiveForm::end(); ?>

        </div>
        
        <!-- User account deleting -->
        
        <div class="user-delete well">
            
            <!-- In case you're an admin you can't delete your account -->
            
            <?php if($_SESSION['role']=='admin'): ?>
            
            <p>You're the admin.</p>
            <p><strong>You cannot delete your account.</strong></p>
            
            <?= Html::button('Ok, whatever', [
                'class' => 'btn btn-info cancel-delete',
            ])?>
            
            <?php else: ?>
            
            <p>The next button will delete your entire account.</p>
            <p><strong>Are you sure you want this?</strong></p>
            
            <?= Html::button('Nope, not sure, let my account STAY', [
                'class' => 'btn btn-warning cancel-delete',
            ])?>
            
            <!-- Modal window for delete confirmation -->
            
            <?php Modal::begin([
                'header' => '<h4>Really sure?</h4>',
                'toggleButton' => [
                    'id' => 'confirm-delete',
                    'label' => "I'm sure, get my account DELETED",
                    'class' => 'btn btn-link',
                    'style' => 'color: red;'
                ],
            ]);?>
            
                <div class="modal-body">
                    <p>
                        Dear <strong><?=$user->username?></strong>,
                        if you do this, all of your posts, images
                        and other data about you will be deleted.
                    </p>
                    <p><strong>This action is irreversible.</strong></p>
                </div>

                <div class="modal-footer">
                    
                    <button type="button" data-dismiss="modal"
                    class="btn cancel-delete">I'm having second thoughts...</button>
                    
                    <?= Html::a('DO IT ALREADY',
                        ['/user/delete-user'],
                        ['class' => 'btn btn-danger'])
                    ?>
                    
                </div>

            <?php Modal::end(); ?>
            
            <?php endif; ?>
            
        </div>
        
    </div>
</div>

<script>
    $(document).ready(function(){
        
        // Image bootstrap tooltip
        
        $('[data-toggle="tooltip"]').tooltip();
        $('.field-tblimage-imagefile>.control-label').html("");
        
        // Hide administration divs
        
        $('.user-update').hide();
        $('.user-delete').hide();
        
        // Store old password and retrieve it
        // if it is not updated by user when clicking 'save changes'
        
        var old_password = $('#tbluser-password').val();
        
        $('#confirm-update').click(function(){
            if( !$('#tbluser-password').val() ){
                $('#tbluser-password').val(old_password);
            }
        });
        
        // Buttons for administration
        
        $('#show-update').click(function(){
            $('#tbluser-password').val("");
            $('.user-data').toggle();
            $('.user-update').toggle();
        });
        
        $('#cancel-update').click(function(){
            $('#tbluser-password').val("");
            $('.user-data').toggle();
            $('.user-update').toggle();
        });
        
        $('#show-delete').click(function(){
            $('.user-data').toggle();
            $('.user-delete').toggle();
        });
        
        $('.cancel-delete').click(function(){
            $('.user-data').toggle();
            $('.user-delete').toggle();
        });
        
        // Auto-submit when a file is selected for profile image
        
        $('#tblimage-imagefile').change(function() {
            $('#user-image').submit();
        });
        
    });
</script>
