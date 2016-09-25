<?php

use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'Sheepblog';
?>
<div class="site-index">

    <div class="jumbotron">

        <h1><b>Sheepblog</b></h1>
        <p>Why 'Sheepblog'? Cause I like sheeps, that's all.</p>

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>[[Title]]</h2>

                <p>[[Post content]]</p>

                <?= Html::a('See post example', ['/site/post', 'post_id' => 1], ['class' => 'btn btn-lg btn-default']) ?>
            </div>
        </div>

    </div>
</div>
