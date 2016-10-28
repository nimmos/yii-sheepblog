<?php

use app\models\TblImage;
use app\models\TblUser;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

    $this->title = 'Sheepblog';
    // Set return URL
    Yii::$app->user->setReturnUrl(['/post/index']);

?>

<style>
    
    /* Jumbotron */
    
    #index-jumbo {
        background: url(<?=TblImage::TEMP_ORIG?>) no-repeat;
        background-size: cover;
        color: white;
        text-align: right;
    }
    
    /* Feedback messages */
    
    #delete-message {
        background-color: rgba(217, 83, 79, 0.60);
        margin-top: 20px;
        margin-bottom: 0px;
        text-align: center;
    }
    
    #delete-message > p {
        font-size: medium;
    }
    
    /* Searchbox */
    
    .inner-addon { 
        position: relative; 
    }

    .inner-addon .glyphicon {
        position: absolute;
        left: 0px;
        padding: 10px;
        pointer-events: none; 
    }
    
    .inner-addon input {
        padding-left: 30px;
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
        
        <?php Pjax::begin([
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
                'layout' => '{summary}{items}{pager}',
                'summary' => 'Found {totalCount} post(s)<br/>',
            ]) ?>
            
            <?= Html::a("", ['post/index'], [
                'id' => 'post-filter',
                'style' => 'height: 0px; visibility: visible;',
            ]) ?>
            
        <?php Pjax::end(); ?>
            
        </div>
        
        <!-- Tags section -->
        
        <div id="tags" class="body-content col-lg-3">
            
            <h3>Search</h3>
            
            <div class="inner-addon">
                <span class="glyphicon glyphicon-search"></span>
                <input type="text" id="searchbox" class="form-control" />
            </div>
            
        </div>
        
    </div>
    
    <p id="test"></p>
    
</div>

<script>
    
    /**
     * Perform a POST to actionSearch(), sending whatever
     * string is in the searchbox text input.
     * 
     * @returns {undefined}
     */
    function search () {
        
        // If the searchbox is focused and contains something
        if($("#searchbox").is(":focus") && $("#searchbox").val()) {

            searchstring = $("#searchbox").val();
            
            $.post(
                "<?=Yii::$app->urlManager->createUrl(["post/search"])?>",
                { "searchstring" : searchstring },
                function(data,status) {
                    $("#test").html(data);
                    
                    // This will trigger the post list filtering
                    //$("#post-filter").trigger("click");
                }
            );
        }
    }
    
    /**
     * All the tag list behaviour
     * 
     * @returns {undefined}
     */
    function generalBehaviour () {
        
        // Store anchor link for post filtering
        var search_link = $("#post-filter").attr("href");
        
        // Search when ENTER key is pressed
        
        $("#searchbox").on('keypress', function (event) {
            if(event.which === 13){
                search();
            }
        });
        
        $('#searchbox').on('input', function() {
            if($("#searchbox").val()==="") {
                $("#post-filter").attr("href", search_link + "&tagstring=");
                $("#post-filter").trigger("click");
            }
        });
        
        // Set interval for automatic search() every 2 seconds
        
        setInterval( function(){ search(); }, 2000 );
    }
    
    /**
     * Do some CSS adjustments
     * 
     * @returns {undefined}
     */
    function cssInit () {
        
        // Remove unnecessary borders from post list
        
        $(".list-group-item:first").css("border-top", "0");
        $(".list-group-item:last").css("border-bottom", "0");
    }
    
    ////////////////////////////////////////////////
    // Page first load
    ////////////////////////////////////////////////
    
    $(document).ready(function(){
        
        // Initial jQuery configuration
        
        generalBehaviour();
        cssInit();
        
        // Re-apply jQuery before and after pjax
        
        $("#index-pjax")
            .on('pjax:start', function(){ generalBehaviour(); cssInit(); })
            .on('pjax:end', function(){ generalBehaviour(); cssInit(); });
        
    });
</script>
