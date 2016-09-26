<?php

use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'Sheepblog';
?>
<div class="site-index">

    <!-- Jumbotron for the blog title -->
    
    <div class="jumbotron">

        <h1><b>Sheepblog</b></h1>
        <p>Why 'Sheepblog'? Cause I like sheeps, that's all.</p>

    </div>
    
    <!-- Show recent entries -->

    <div class="body-content">

        <?php foreach ($posts as $post){ ?>
    
        <div class="row">
            <div class="col-lg-4">
                <h3>
                    <?= $post->title ?>
                </h3>
                <p>
                    <!-- Limits the entry to 160 characters -->
                    <?= (strlen($post->content)>=160) ?
                    substr($post->content, 0, 160) . "..."
                    : $post->content ?>
                </p>
                <?= Html::a('Read this post',
                    ['/post/post', 'p' => $post->post_id],
                    ['class' => 'btn btn-lg btn-default']
                )?>
                <hr>
            </div>
        </div>

        <?php } ?>

    </div>
</div>
