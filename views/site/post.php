<?php

$this->title = 'post-page';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <p>[[This will display a post]]</p>
    <p>
    	[[Here goes the comment composing]]
    	<?= $this->render('comment-compose', ['model' => new app\models\CommentForm()]) ?>
    </p>
    <p>[[Below this point, the comment section]]</p>
</div>
