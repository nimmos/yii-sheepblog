<?php
use app\models\TblUser;

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

        <ul class="list-group">
            <?php foreach ($posts as $post){ ?>
                <li class="list-group-item">
                    <?= $this->render('post-resume', [
                        'post' => $post,
                        'author' => TblUser::getUsernameById($post->user_id),
                    ]) ?>
                </li>
            <?php } ?>
        </ul>

    </div>
</div>
