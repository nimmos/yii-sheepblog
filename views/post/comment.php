<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

?>

<!-- Comment view, to be embedded into a post -->

<div>
    
    <div>
        <h4><strong><?= Html::encode($username) ?></strong> says:</h4>
        <p><?= $comment->content ?></p>
    </div>
    
    <div style="color:#ababab;">
        <p>
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
</div>
