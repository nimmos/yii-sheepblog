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
    
    #index-jumbo {
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
    
    .tag {
        float: left;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-right: 15px;
        padding-left: 15px;
        margin: 5px 5px;
        border-radius: 5px;
        color: white;
    }
    
    .tag.btn:hover, .btn:focus, .btn.focus {
        color: white;
    }
    
    #tag-buttons {
        margin-left: 5px;
        margin-bottom: 10px;
    }
    
    .tag.inactive {
        background-color: #337ab7;
        border-color: #337ab7;
    }
    
    .tag.active {
        color: #4f4428;
        background-color: #f5bc1b;
        border-color: #f5bc1b;
    }
    
</style>

<div class="site-index">
    
    <!-- Jumbotron for the blog title -->
    
    <div id="index-jumbo" class="jumbotron">
        
        <div id="index-jumbo-title">
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
    
    <div class="row">
        
        <!-- Show recent entries -->
        
        <?php \yii\widgets\Pjax::begin([
            'id' => 'index-pjax',
            'timeout' => 30000,
        ]); ?>
        
        <div id="recent-posts" class="body-content col-lg-9">
            
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
                    'hideOnSinglePage' => true,
                    'maxButtonCount' => 5,
                ],
                'layout' => '{items}{pager}',
                'summary' => 'Showing {begin}-{end} from {totalCount} posts<br/>',
            ]) ?>
            
        </div>
        
        <!-- Tags section -->
        
        <div id="tags" class="body-content col-lg-3">
            <h3>Tags</h3>
            
            <div id="tag-buttons">
                <?= Html::a("Search", ['post/index'], [
                    'id' => 'search-tags',
                    'class' => 'btn btn-info',
                ]) ?>
                <button id="clean-tags" class="btn btn-profile">Clean</button>
            </div>
            
            <form id="send"></form>
            
            <?php if(!empty($tags)): ?>
            <div class="tag-list">
            <?php foreach($tags as $tag): ?>
                <button type="button" class="tag btn <?= (in_array($tag->tagname, $tagstring))?
                            "active" : "inactive" ?>">
                    <?=Html::encode($tag->tagname)?>
                </button>
            <?php endforeach; ?>
            </div>
            
            <?php else: ?>
                <p>
                    No tags found.
                </p>
            <?php endif; ?>
        </div>
        
        <?php \yii\widgets\Pjax::end(); ?>
        
    </div>
    <p id="test"></p>
</div>

<script>
    
    function taglistBehaviour () {
        
        var tagstring = "";
        var tagsearch_link = $("#search-tags").attr("href");

        // Toggle active-inactive state for tag buttons

        $(".tag").click(function(){
            $(this).toggleClass("active");
            $(this).toggleClass("inactive");
        });

        // Toggle visibility of tag buttons
        // depending on whether there is active tags or not

        $(".tag-list").click(function(){

            // Obtain the string with te active tags

            $(".tag.active").each(function(index){
                var str = $(this).html();
                tagstring += $.trim(str) + ",";
            });

            // Change link of "search" button

            $("#search-tags").attr("href",
                tagsearch_link + "&tagstring=" + tagstring);    
            tagstring = "";

        });

        // Clean tags from being activated

        $("#clean-tags").click(function(){

            $(".tag").removeClass("active").addClass("inactive");
        });
        
        // Remove unnecessary borders from post list
        
        $(".list-group-item:first").css("border-top", "0");
        $(".list-group-item:last").css("border-bottom", "0");
    }
    
    $(document).ready(function(){
        
        taglistBehaviour();
        
        $("#index-pjax")
                .on('pjax:start', function(){ taglistBehaviour(); })
                .on('pjax:end', function(){ taglistBehaviour(); });
        
    });
</script>
