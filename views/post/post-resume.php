<?php
use yii\helpers\Html;
?>

<!-- View for each post resume -->

<div>
    <h3>
        <?= $post->title ?>
    </h3>
    <p style="color:#ababab;">
        <strong>Posted by: </strong><?= $author ?>
    </p>
    <p>
        <!-- Limits the entry to 160 characters -->
        <?= (strlen($post->content)>=160) ?
        substr($post->content, 0, 160) . "..."
        : $post->content ?>
    </p>
        <?= Html::a('Read this post',
            ['/post/post', 'p' => $post->post_id],
            ['class' => 'btn btn-sm btn-default']
        )?>
</div>
