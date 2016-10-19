<?php

use yii\helpers\Html;

    $this->title = "User profile";
    $this->params['breadcrumbs'][] = $this->title;
    // Set return URL
    Yii::$app->user->setReturnUrl(['/user/profile']);

?>

<div class="user-profile">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <!-- User profile display and administration sections -->
    
    <?= $this->render('user-admin', [
        'user' => $user,
        'imagepath' => $imagepath,
        'newimage' => $newimage,
    ]) ?>

    <hr>
    
    <!-- Post list section -->
    
    <?= $this->render('../post/post-list', ['dataProvider' => $dataProvider])?>
    
</div>
