<?php

use app\models\TblImage;
use app\models\TblUser;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Html;

?>

<!-- GridView with the post list -->

<h2><?= ($_SESSION["role"]=='admin') ? "All the posts" : "Your posts" ?></h2>

<?= GridView::widget([
        'options' => [ 'class'=>'grid-view' ],
        'tableOptions' => [ 'class' => 'table table-striped table-bordered' ],
        'dataProvider' => $dataProvider,
        'layout' => '{summary}{items}{pager}',
        'summary' => '',
        'showOnEmpty' => true,
        'columns' => [
            [ // Creation time of each post
                'attribute' => 'time',
                'format' => ['date', 'php:Y-m-d \(\a\t G:i:s\)'],
                'headerOptions' => ['width' => '20%'],
            ],
            [ // Author for each post
                'attribute' => 'user_id',
                'format' => 'raw',
                'visible' => $_SESSION["role"]=='admin',
                'headerOptions' => ['width' => '10%'],
                'value' => function($data){
                    return TblUser::findUsernameById($data->user_id);
                },
            ],
            [ // Title for each post
                'attribute' => 'title',
                'format' => 'raw',
                'headerOptions' => ['width' => '50%'],
                'value' => function($data, $key){
                    return Html::a(
                        $data->title,
                        ['/post/post', 'p' => $key]);
                },
            ],
            [ // Image for each post
                'header' => 'Image',
                'format' => 'raw',
                'headerOptions' => ['width' => '10%'],
                'value' => function($data){
                    $url = TblImage::routePostHeaderDir($data->user_id, $data->post_id)
                            . TblImage::HEADER
                            . TblImage::THUMBNAIL
                            . $data->headerimage;
                    return Html::img($url,[
                        'width' => 40,
                        'height' => 40,
                    ]);
                },
            ],
            [ // Action buttons
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Post actions',
                'headerOptions' => ['width' => '10%'],
                'template' => '{view} {update} {delete} {link}',
                'buttons' => [
                    'view' => function($url, $data, $key){
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            ['/post/post', 'p' => $key]);
                    },
                    'update' => function ($url, $data, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            ['/post/edit-post', 'p' => $key]);
                    },
                    'delete' => function ($url, $data, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            '#',
                            [
                                'class' => 'delete-button',
                                'post_id' => $key,
                                'title' => $data->title,
                            ]);
                    },
                ],
            ],
        ],
]) ?>

<!-- Modal window for delete confirm -->

<?php Modal::begin([
    'id' => 'delete-modal',
    'header' => '<h4>Delete comment</h4>',
]);?>

    <div class="modal-body">
        <p><strong>Are you sure you want to delete this post?</strong></p>
        <p class="well"></p>
    </div>

    <div class="modal-footer">
        <?= Html::a('Delete',
            ['/post/delete-post'],
            [
                'class' => 'btn btn-danger delete-confirm'
            ])
        ?>
        <button type="button" data-dismiss="modal" class="btn">Cancel</button>
    </div>

<?php Modal::end(); ?>

<script>
    $(document).ready(function(){
        
        var link = $('.delete-confirm').attr('href');
        
        $('.delete-button').click(function(event){
            
            // Prevent the default link behaviour
            event.preventDefault();
            
            // Obtain data from the selected post
            var post_id = $(this).attr('post_id');
            var title = $(this).attr('title');
            
            // Change the modal
            $('.delete-confirm').attr('href', function(){
                return link + "&p=" + post_id;
            });
            $('.well').html(title);
            
            $('#delete-modal').modal('show');
        });
    });
</script>
