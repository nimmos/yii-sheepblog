<?php

use app\models\TblUser;
use app\models\TblImage;
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'Sheepblog';
?>
<style>
    .jumbotron {
        background: url(20160831_065557.original.jpg) no-repeat;
        background-size: cover;
        color: white;
        text-align: right;
    }
</style>
<div class="site-index">

    <!-- Jumbotron for the blog title -->
    
    <div class="jumbotron">

        <div class="jumbotron-title">
            <h1><b>Sheepblog</b></h1>
            <p>Why 'Sheepblog'? Cause I like sheeps, that's all.</p>
            <br><br>
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
            'dataProvider' => $dataProvider,
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
                    'imagepath' => TblImage::getRoutePostImageFolder($model->post_id),
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
