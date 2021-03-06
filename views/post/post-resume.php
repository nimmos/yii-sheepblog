<?php

use app\models\TblImage;
use app\models\TblTag;
use yii\helpers\Html;

    // Content unformatting

    $data = strip_tags($post->content);
    $content = html_entity_decode($data, ENT_QUOTES, 'UTF-8');

    // Limits the entry to 160 characters
    
    if (strlen($content)>=160)
    {
        $content = mb_substr($content, 0, 160, 'UTF-8') . '...';
    }

    // Establish thumbnail image path
    
    if (isset($post->headerimage)) {
        $thumbpath = TblImage::pathGenerator(
                $post->user_id,
                TblImage::HEADER,
                $post->headerimage,
                true,
                $post->post_id
        );
    } else {
        $thumbpath = TblImage::TEMP_THUMB;
    }
    
    // Load tag string
    $tagstring = TblTag::turnString(TblTag::getTags($post->post_id),", ");
?>

<!-- View for each post resume -->

<style>
    #container {
        height: 100%; width: 100%;
    }
    
    #thumbnail, #content {
        display: inline-block;
        *display: inline;
        vertical-align: top;
    }
    
    #thumbnail {
        margin-top: 20px;
        margin-bottom: 20px;
        width: 15%;
    }
    
    #thumbnail img {
        width: 80%;
        border-radius: 100%;
    }
    
    #content {
        width: 75%;
    }
</style>

<div id="container">

    <div id="thumbnail">
        <img src="<?=$thumbpath?>"/>
    </div>
    
    <div id="content">
        <h3>
            <?= Html::a(Html::encode($post->title),
                ['/post/post', 'p' => $post->post_id])
            ?>
        </h3>
        <p style="color:#ababab;">
            <strong>Posted by: </strong><?=$author?>
        </p>
        <p>
            <?=$content?><br>
        </p>
        <p style="color:#ababab;">
            Posted on: 
            <span style="color:#0d88c1;"><?=Html::encode($tagstring)?></span>
        </p>
    </div>
</div>
