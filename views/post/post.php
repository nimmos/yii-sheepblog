<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

use app\models\TblUser;
use app\models\TblImage;

$this->title = $post->title;
$this->params['p'] = $post->post_id;
$this->params['breadcrumbs'][] = $this->title;

$image = TblImage::getRoute($post->post_id, $post->headerimage);
$color = isset($post->headerimage) ? "white" : "black";
?>
<style>
    .jumbotron {
        background: url(<?=$image?>) no-repeat center center;
        background-size: cover;
        color: <?=$color?>;
    }    
</style>
<div>
    
    <!-- Post section -->
    
    <div class="jumbotron">
        <h2><?= Html::encode($this->title) ?></h2>
    </div>
    <div>
        <p>
            <?= HtmlPurifier::process($post->content) ?>
        </p>
    </div>
    
    <div class="well well-sm" style="color:#ababab;">
        <p>
            <strong>Posted on: </strong><?= $post->time ?><br>
            <strong>Words by: </strong><?= Html::encode($author) ?>
        </p>
    </div>
    
    <!-- If the user is an authenticated user -->
    
    <?php if (!Yii::$app->user->isGuest): ?>
    
        <!-- Display post administration -->
        <?= $this->render('post-admin', ['user_id' => $post->user_id])?>
    
        <!-- Display comment form -->
        <?= $this->render('comment-compose', ['model' => $comment]) ?>
    
    <!-- If the user is a guest, it can't comment and is encouraged to sign up -->
    
    <?php else: ?>
        <div style="color:#d60000;">
            <p>
                Only <strong>blog users</strong> are able to comment.<br>
                What are you waiting for? Sign up now!<br>
            </p>
        </div>
        <div>
            <p>
                <?= Html::a('Sign up', ['/site/signup'], ['class' => 'btn btn-primary']) ?>
            </p>
        </div>
    <?php endif; ?>
    
    <!-- Comment section-->
    
    <div>
        <h3><hr>Here's what other users said...<hr></h3>
    </div>
    
    <?php foreach ($comments as $comment){ ?>
        <?= $this->render('comment', [
            'username' => TblUser::findUsernameById($comment->user_id),
            'comment' => $comment,
        ]) ?>
    <?php } ?>
</div>
