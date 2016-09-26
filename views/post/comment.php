<div>
    <h4><strong><?= $username ?></strong> says:</h4>
    <div>
        <p>
            <?= $comment->content ?>
        </p>
    </div>
    <div style="color:#ababab;">
        <p>
            Said on: <?= $comment->time ?><br><br>
        </p>
    </div>
</div>