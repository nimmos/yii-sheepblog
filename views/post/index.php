<?php

use app\models\TblUser;
use app\models\TblImage;
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'Sheepblog';
// Set return URL
Yii::$app->user->setReturnUrl(['/post/index']);

?>

<style>
    
    .jumbotron {
        background: url(<?=TblImage::TEMP_ORIG?>) no-repeat;
        background-size: cover;
        color: white;
        text-align: right;
    }
    
    #delete-message {
        background-color: rgba(217, 83, 79, 0.60);
        margin-top: 20px;
        margin-bottom: 0px;
        text-align: center;
    }
    
    #delete-message > p {
        font-size: medium;
    }
    
</style>

<div class="site-index">
    
    <!-- Jumbotron for the blog title -->
    
    <div class="jumbotron">
        
        <div class="jumbotron-title">
            <h1><b>Sheepblog</b></h1>
            <p>Why 'Sheepblog'? Cause I like sheeps, that's all.</p>
        </div>
        
        <!-- If the user is authenticated, this makes a button for posting -->
        
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Why don\'t we start by posting something',
                ['/post/post-compose'],
                ['class' => 'btn btn-primary btn-block'])
            ?>
        <?php endif; ?>
        
        <!-- Feedback messages -->
            
        <?php if (Yii::$app->session->hasFlash('userDeleteSuccess')): ?>
            <div id="delete-message" class="alert fade in" data-dismiss="alert" aria-label="close">
                <p>Your account has been deleted succesfully.</p>
            </div>
        <?php endif; ?>
        
    </div>
    
    <div id="recent-posts" class="row">
        
        <!-- Show recent entries -->
        
        <div class="body-content col-lg-9">
            
            <h3>What was posted lately...</h3>
            
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
                    'style' => 'border-right:0; border-left:0;',
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
                'layout' => '{items}{pager}',
                'summary' => 'Showing {begin}-{end} from {totalCount} posts<br/>',
            ]) ?>

        </div>
        
        <!-- Tags section -->
        
        <div class="body-content col-lg-3">
            <h3>Tags</h3>
            
            <?php if(!empty($tags)): ?>
            <?php foreach($tags as $tag): ?>
                <div class="tag btn-primary">
                    <?=Html::encode($tag->tagname)?>
                </div>
            <?php endforeach; ?>
            <?php else: ?>
                <p>
                    No tags found.
                </p>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        
        // Remove unnecessary borders from post list
        $(".list-group-item:first").css("border-top", "0");
        $(".list-group-item:last").css("border-bottom", "0");
    });
</script>
