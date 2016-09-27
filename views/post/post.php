<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

use app\models\TblUser;

$this->title = $post->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    
    <!-- Post section -->
    
    <div>
        <h1>
            <?= $this->title ?>
        </h1>
        <p>
            <?= HtmlPurifier::process($post->content) ?>
        </p>
    </div>
    
    <div class="well well-sm" style="color:#ababab;">
        <p>
            <strong>Posted on: </strong><?= $post->time ?><br>
            <strong>Words by: </strong><?= $author ?>
        </p>
    </div>
    
    <!-- If the user is an authenticated user -->
    
    <?php if (!Yii::$app->user->isGuest): ?>
    
        <!-- Let the user edit the post -->
        <?php if ($isAuthor): ?>
            <?= Html::a('Edit the post <span class="glyphicon glyphicon-pencil"/>',
                ['/post/edit-post', 'p' => $post->post_id],
                ['class' => 'btn btn-md btn-warning']
            )?>
            <hr>
        <?php endif; ?>
    
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
