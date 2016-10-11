<?php

use app\models\TblImage;
use app\models\TblUser;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\widgets\ListView;

    $this->title = $post->title;
    $this->params['p'] = $post->post_id;
    $this->params['breadcrumbs'][] = $this->title;

    // Establish jumbotron image
    
    if (isset($post->headerimage)) {
        $path = TblImage::routePostHeaderDir($post->user_id, $post->post_id)
                . TblImage::HEADER . TblImage::ORIGINAL . $post->headerimage;
        $image = "background: url($path) no-repeat center center;";
        $color = "color: white;";
    } else {
        $image = "";
        $color = "color: black;";
    }

?>

<!-- View for post -->

<style>
    .jumbotron {
        <?=$color?>
        <?=$image?>
        background-size: cover;
    }
    .jumbo-title {
        filter: drop-shadow(2px 2px 1px black);
    }
</style>
<div>
    
    <!-- Post section -->
    
    <div class="jumbotron">
        <h2 class="jumbo-title"><?= Html::encode($this->title) ?></h2>
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
    
    <!-- If user is authenticated: display post administration -->
    
    <?php if (!Yii::$app->user->isGuest): ?>
    
        <?= $this->render('post-admin', ['user_id' => $post->user_id])?>
    
    <?php endif; ?>
    
    <!-- Gallery section -->
    
    <?= $this->render('slick-post', [
            'images' => TblImage::routesImageFromContent($post->content),
    ]) ?>
        
    <!-- If user is authenticated: display comment form -->
    
    <?php if (!Yii::$app->user->isGuest): ?>
    
        <?= $this->render('comment-compose', ['model' => $comment]) ?>
    
    <!-- If user is gues: it can't comment and is encouraged to sign up -->
    
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
        <h3><hr>Here's what other users said...</h3>
    </div>
    
    <?= ListView::widget([
            'dataProvider' => $comments,
            'options' => [
                'tag' => 'ul',
                'class' => 'list-group',
                'id' => 'list-wrapper',
            ],
            'itemOptions' => [
                'tag' => 'li',
                'class' => 'list-group-item',
            ],
            'itemView' => function ($model, $key, $index, $widget) {
		return $this->render('comment', [
                    'username' => TblUser::findUsernameById($model->user_id),
                    'comment' => $model,
                ]);
            },
            'summary' => '',
        ]) ?>
    
</div>
