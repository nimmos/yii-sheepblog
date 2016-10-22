<?php

use app\models\TblImage;
use app\models\TblUser;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\widgets\ListView;

    $this->title = $post->title;
    $this->params['p'] = $post->post_id;
    $this->params['breadcrumbs'][] = $this->title;
    // Set return URL
    Yii::$app->user->setReturnUrl(['/post/post', 'p' => $post->post_id]);

    // Establish jumbotron image
    
    if (isset($post->headerimage))
    {    
        $headerpath = TblImage::pathGenerator(
                $post->user_id,
                TblImage::HEADER,
                $post->headerimage,
                false,
                $post->post_id
        );
        
        $jumbotron_css = "color: white;"
                . "background: url($headerpath) no-repeat center center; background-size: cover;";
        $jumbotron_title_css = "filter: drop-shadow(2px 2px 1px black);";
    } else {
        $jumbotron_css = "color: black;";
        $jumbotron_title_css = "";
    }
    
    // Establish author profile thumbnail image
    
    if (isset($author->userimage))
    {
        $profilepath = TblImage::pathGenerator(
                $author->user_id,
                TblImage::PROFILE,
                $author->userimage,
                false
        );
    } else {
        $profilepath = TblImage::TEMP_THUMB;
    }
    
?>

<!-- View for post -->

<style>
    .jumbotron {
        <?=$jumbotron_css?>
    }
    .jumbotron-title {
        <?=$jumbotron_title_css?>
    }
    .profile-image {
        border-radius: 100%;
    }
</style>

<div>
    
    <!-- Post content section -->
    
    <div class="jumbotron">
        <h2 class="jumbotron-title"><?= Html::encode($this->title) ?></h2>
    </div>
    <div>
        <p>
            <?= HtmlPurifier::process($post->content) ?>
        </p>
        <br>
    </div>
    
    <!-- Post author section -->
    
    <div class="well well-sm" style="color:#3d3d3d;">
        <div class="row">
            <div id="author-thumbnail" class="col-sm-1">
                <img class="profile-image" src="<?=$profilepath?>" width="80" height="80"/>
            </div>
            
            <div id="author-data" class="col-sm-5">
                <p>
                    <strong>Posted on: </strong><?= $post->time ?><br>
                    <strong>Words by: </strong><?= Html::encode($author->username) ?>
                </p>
                
                <!-- If user is authenticated: display post administration -->
    
                <?php if (!Yii::$app->user->isGuest): ?>

                    <?= $this->render('post-admin', ['user_id' => $post->user_id])?>

                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Gallery section -->
    
    <?= $this->render('slick-post', [
            'images' => TblImage::getImagePathsFromContent($post->content),
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
                    'author' => TblUser::findById($model->user_id),
                    'comment' => $model,
                ]);
            },
            'summary' => '',
        ]) ?>
    
</div>
