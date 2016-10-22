<?php

use app\models\TblImage;
use yii\bootstrap\Modal;
use yii\helpers\Html;

    // Establish author profile thumbnail image
    
    if (isset($author->userimage))
    {
        $profilepath = TblImage::pathGenerator(
                $author->user_id,
                TblImage::PROFILE,
                $author->userimage,
                false
        );
    } else {
        $profilepath = TblImage::TEMP_THUMB;
    }

?>

<!-- Comment view, to be embedded into a post -->


<div class="row">
    <div class="col-lg-9">
        <div id="author-thumbnail" style="float:left; margin-top:5px;">
            <img class="profile-image" src="<?=$profilepath?>" width="50" height="50"/>
        </div>

        <div id="author-data" style="float:left; margin-left:10px; margin-top:25px;">
            <p>
                <h4><strong><?= Html::encode($author->username) ?></strong> says:</h4>

            </p>
        </div>
    </div>
</div>

<div>
    <p><?= $comment->content ?></p>
    <p style="color:#ababab;">
        Said on: <?= $comment->time ?>

        <!-- Checks if the user can delete comments -->

        <?php if (Yii::$app->user->can('deleteComment') ||
            Yii::$app->user->can('deleteOwnComment', ['user_id' => $comment->user_id])
        ): ?>

        <!-- Modal window for delete confirmation -->

        <?php Modal::begin([
            'header' => '<h4>Delete comment</h4>',
            'toggleButton' => [
                'label' => 'Delete',
                'class' => 'btn btn-link',
            ],
        ]);?>

            <div class="modal-body">
                <p><strong>Are you sure you want to delete your comment?</strong></p>
                <div class="well"><?= $comment->content ?></div>
            </div>

            <div class="modal-footer">
                <?= Html::a('Delete', [
                        '/post/delete-comment',
                        'c' => $comment->comment_id,
                    ],
                    ['class' => 'btn btn-danger'])
                ?>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>

        <?php Modal::end(); ?>

        <?php endif; ?>
    </p>
</div>
