<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

    $cookies = Yii::$app->request->cookies;

?>

<style>
    
    #message {
        background-color: rgba(50, 50, 50, 0.75);
        text-align: left;
        position: fixed;
        top: 50px;
        left: 0;
        width: 100%;
        border-radius: 0;
        z-index: 10;
    }
    
    #inner-message {
        margin: 0 auto;
        color: white;
    }
    
</style>

<!-- Cookies message -->
    
<?php if(!$cookies->getValue('cookie-accept', false)): ?>
    
    <div id="message">
        <div id="inner-message" class="alert fade in">
            <p>
                This web uses cookies. If you agree with this website's cookies
                policy, click on the next button:
                <?= Html::button('Accept',
                    [
                        'id' => 'cookie-accept',
                        'class' => 'btn btn-info btn-sm',
                    ])
                ?>
            </p>
        </div>
    </div>
    
<?php endif; ?>

<!-- Breadcrumbs -->

<?= Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
]) ?>

<!-- Content -->

<?= $content ?>

<script>
    $("#cookie-accept").click(function(event){
        $.post(
            "<?=Yii::$app->urlManager->createUrl(["post/set-cookie"])?>",
            function() {
                $("#message").hide();
            }
        );
    });
</script>
