<?php
use yii\helpers\Html;

// Content unformatting
$data = strip_tags($post->content);
$content = html_entity_decode($data, ENT_QUOTES, 'UTF-8');

// Limits the entry to 160 characters
if (strlen($content)>=160)
{
    $content = mb_substr($content, 0, 160, 'UTF-8') . '...';
}
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
    <p><?= $content ?><br></p>
</div>
