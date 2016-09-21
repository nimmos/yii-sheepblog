<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        This is the About page. You may modify the following file to customize its content:
    </p>

    <code><?= __FILE__ ?></code>

    <!--///////////////////////////////////////////////////
    // BEYOND THIS POINT:
    // EXAMPLE TAGS
    ////////////////////////////////////////////////////-->

    <p>
		<?= Html::encode("<script>alert('alert!');</script><h1>ENCODE EXAMPLE</h1>>") ?>
	</p>
	<p>
		<?= HtmlPurifier::process("<script>alert('alert!');</script><h1> HtmlPurifier EXAMPLE</h1>") ?>
	</p>
	<?= $this->render("_part1") ?>
	<?= $this->render("_part2") ?>

	<p>
		<b>Email:</b> <?= $email ?>
	</p>
	<p>
		<b>Phone:</b> <?= $phone ?>
	</p>

</div>
