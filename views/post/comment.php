<?php
use yii\helpers\Html;
?>

<!-- Comment view, to be embedded into a post -->

<div>
    
    <div>
        <h4><strong><?= $username ?></strong> says:</h4>
        <p>
            <?= Html::encode($comment->content) ?>
        </p>
    </div>
    
    <div style="color:#ababab;">
        <p>
            Said on: <?= $comment->time ?>
            <br><br>
        </p>
    </div>
    
</div>