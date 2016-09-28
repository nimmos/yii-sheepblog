<?php
use yii\helpers\Html;
$content = strip_tags($post->content);
?>

<!-- View for each post resume -->

<div>
    <h3>
        <?= Html::a(Html::encode($post->title),
            ['/post/post', 'p' => $post->post_id])
        ?>
    </h3>
    <p style="color:#ababab;">
        <strong>Posted by: </strong><?= $author ?>
    </p>
    <p>
        <!-- Limits the entry to 160 characters -->
        <?= (strlen($content)>=160) ?
        (substr($content, 0, 160)) . '...'
        : $post->content ?>
        <br>
    </p>
</div>
