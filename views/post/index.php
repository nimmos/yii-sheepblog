<?php

use app\models\TblUser;
use app\models\TblImage;
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'Sheepblog';
?>

<style>
    
    .jumbotron {
        background: url(<?=TblImage::TEMP_ORIG?>) no-repeat;
        background-size: cover;
        color: white;
        text-align: right;
    }
    
    .alert {
        text-align: center;
    }
    
    li {
        border-radius: 6px;
    }
    
</style>

<div class="site-index">

    <!-- Jumbotron for the blog title -->
    
    <div class="jumbotron">

        <div class="jumbotron-title">
            <h1><b>Sheepblog</b></h1>
            <p>Why 'Sheepblog'? Cause I like sheeps, that's all.</p>
            
            <!-- Feedback messages -->
            
            <?php if (Yii::$app->session->hasFlash('userDeleteSuccess')): ?>
                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <p><strong>Your account has been deleted succesfully.</strong></p>
                </div>
            <?php endif; ?>
            
        </div>
        
        <!-- If the user is authenticated, this makes a button for posting -->
        
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Why don\'t we start by posting something',
                ['/post/post-compose'],
                ['class' => 'btn btn-primary btn-block'])
            ?>
        <?php endif; ?>
        
    </div>
    
    <!-- Show recent entries -->

    <div class="body-content">
        
        <?= ListView::widget([
            'dataProvider' => $posts,
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
		return $this->render('post-resume',[
                    'post' => $model,
                    'author' => TblUser::findUsernameById($model->user_id),
                ]);
            },
            'pager' => [
                'firstPageLabel' => 'More recent',
                'lastPageLabel' => 'First ones',
                'nextPageLabel' => 'Older',
                'prevPageLabel' => 'Newer',
                'maxButtonCount' => 5,
            ],
            'summary' => 'Showing {begin}-{end} from {totalCount} posts<br/>',
        ]) ?>

    </div>
</div>

