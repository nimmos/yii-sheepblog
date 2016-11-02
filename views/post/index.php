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

    .dropdown-menu {
        width: 100%;
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
            'timeout' => 60000,
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

            <?= Html::a('', ['post/index'], [
                'id' => 'post-filter',
                'style' => 'height: 0px; visibility: hidden;',
            ]) ?>

        <?php Pjax::end(); ?>

        </div>

        <!-- Tags section -->

        <div id="tags" class="body-content col-lg-3">

            <h3>Search</h3>

            <div class="searchbox-container inner-addon">
                <span class="glyphicon glyphicon-search"></span>
                <input type="text" id="searchbox" class="form-control"/>
                <ul class="dropdown-menu">
                </ul>
            </div>

        </div>

    </div>

</div>

<script>

    // Global variable for storing searchbox string
    var search_string = "";
    var tag_retrieve = "";

    /**
     * Filter the posts
     *
     * @returns {undefined}
     */
    function filterPosts () {

        search_string = $("#searchbox").val();

        $("#post-filter").attr("href",
            "<?=Yii::$app->urlManager->createUrl(["post/index"])?>"
            + "&s=" + search_string);
        $("#post-filter").trigger("click");
    }

    /**
     * Perform a POST to actionSearch(), sending whatever
     * string is in the searchbox text input.
     *
     * @returns {undefined}
     */
    function searchTags () {

        // If the searchbox is focused
        if($("#searchbox").is(":focus")) {

            if($("#searchbox").val()) {
                search_string = $("#searchbox").val();
            } else {
                search_string = "";
            }

            $.post(
                "<?=Yii::$app->urlManager->createUrl(["post/search"])?>",
                { searchstring : search_string },
                function(data) {
                    tag_retrieve = data;
                    showTags();
                }
            );
        }
    }

    /**
     * Adds a tag to the dropdown list
     *
     * @param {type} item
     * @returns {undefined}
     */
    function addTagToDropdown (item) {
        $(".dropdown-menu").append(
                $("<li>").append($("<a>").text(item))
        );
    }

    /**
     * Adds all the tags to the dropdown list
     * in a recursive way
     *
     * @returns {undefined}
     */
    function addTags () {

        $(".dropdown-menu").empty();

        if(tag_retrieve) {
            var tag_array = tag_retrieve.split(",");
            tag_array.forEach(addTagToDropdown);
            tag_retrieve = "";
        }
    }

    /**
     * Show the tags with slideUp-Down animations
     *
     * @returns {undefined}
     */
    function showTags () {

        addTags();

        if($(".dropdown-menu").children().length > 0) {
            $(".dropdown-menu").slideDown(500);
        } else {
            $(".dropdown-menu").slideUp(500);
        }
    }

    // Search when ENTER key is pressed

    $("#searchbox").keypress(function (event) {
        if(event.which === 13){
            filterPosts();
        }
    });

    // Show tag suggestions when there's something in the box

    $("#searchbox").on("input", function(){
        searchTags();
    });

    $("#searchbox").focus(function(){
        searchTags();
    });
    $("#searchbox").focusout(function(){
        $(".dropdown-menu").slideUp(500);
    });

    // TODO: Dropdown tag suggestion logic

//    $("li > a").click(function(){
//        $("#searchbox").html($(this).html());
//    });

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

        cssInit();

        // Set interval for automatic searchTags() every 2 seconds

        //setInterval( function(){ searchTags(); }, 2000 );

        // Re-apply jQuery before and after pjax

        $("#index-pjax")
            .on('pjax:start', function(){ cssInit(); })
            .on('pjax:end', function(){ cssInit(); });

    });
</script>
