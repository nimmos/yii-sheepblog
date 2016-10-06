<?php
/**
 * Let the user edit / delete the post if:
 * 1: It's an admin user.
 * 2: It's the author of the post.
 */

use yii\bootstrap\Modal;
use yii\helpers\Html;
?>

<hr>

<?php if (
    Yii::$app->user->can('updateOwnPost', ['user_id' => $user_id])
    || Yii::$app->user->can('updatePost')
): ?>

    <!-- Button for post editing -->

    <?= Html::a('Edit the post <span class="glyphicon glyphicon-pencil"/>',
        ['/post/edit-post', 'p' => $this->params['p']],
        ['class' => 'btn btn-md btn-warning']
    )?>            
<?php endif; ?>

<?php if (
    Yii::$app->user->can('deleteOwnPost', ['user_id' => $user_id])
    || Yii::$app->user->can('deletePost')
): ?>

    <!-- Modal window for delete confirmation -->
            
    <?php Modal::begin([
        'header' => '<h4>Delete comment</h4>',
        'toggleButton' => [
            'label' => 'Delete the post <span class="glyphicon glyphicon-remove"/>',
            'class' => 'btn btn-md btn-danger',
        ],
    ]);?>

        <div class="modal-body">
            <p><strong>Are you sure you want to delete this post?</strong></p>
        </div>

        <div class="modal-footer">
            <?= Html::a('Delete', [
                    '/post/delete-post',
                    'p' => $this->params['p']
                ],
                ['class' => 'btn btn-danger'])
            ?>
            <button type="button" data-dismiss="modal" class="btn">Cancel</button>
        </div>

    <?php Modal::end(); ?>

<?php endif; ?>
