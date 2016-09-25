<?php

use yii\helpers\Html;

$this->title = $post->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
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
            Words by: <?= app\models\TblUser::getUsernameById($post->user_id) ?>
        </p>
    </div>
    
    <?php if (!Yii::$app->user->isGuest): ?>
        <?= $this->render('comment-compose', ['model' => new app\models\CommentForm()]) ?>
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
    
    <p>[[Below this point, the comment section]]</p>
</div>
