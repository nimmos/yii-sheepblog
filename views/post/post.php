<?php

use yii\helpers\Html;

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
            <?= $post->content ?>
        </p>
    </div>
    
    <div style="color:#ababab;">
        <p>
            Posted on: <?= $post->time ?><br>
            Words by: <?= $author ?>
        </p>
    </div>
    
    <!-- If the user is an authenticated user -->
    
    <?php if (!Yii::$app->user->isGuest): ?>
        <?= $this->render('comment-compose', ['model' => new app\models\CommentForm()]) ?>
    
    <!-- If the user is a guest, it can't comment and is encouraged to sign up -->
    
    <?php else: ?>
        <div class="col-lg-offset-1" style="color:#d60000;">
            <p>
                Only <strong>blog users</strong> are able to comment.<br>
                What are you waiting for? Sign up now!<br>
            </p>
        </div>
        <div class="col-lg-offset-1">
            <p>
                <?= Html::a('Sign up', ['/site/signup'], ['class' => 'btn btn-primary']) ?>
            </p>
        </div>
    <?php endif; ?>
    
    <!-- Comment section-->
    
    <?php foreach ($comments as $comment){ ?>
        <?= $this->render('comment', [
            'username' => TblUser::getUsernameById($comment->user_id),
            'comment' => $comment,
        ]) ?>
    <?php } ?>
</div>
