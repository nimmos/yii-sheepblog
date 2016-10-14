<?php

use app\models\TblImage;
use app\models\TblUser;
use yii\helpers\Html;

    $this->title = "User profile";
    $this->params['breadcrumbs'][] = $this->title;
    // Set return URL
    Yii::$app->user->setReturnUrl(['/user/profile']);

    // Retrieve user profile image
    
    $user = TblUser::findById(Yii::$app->user->id);
    
    if(isset($user->userimage))
    {
        $directory = TblImage::routeUserImageDir($user->user_id);
        $image = $directory . TblImage::PROFILE . TblImage::ORIGINAL . $user->userimage;
    } else {
        $image = "/blogheader.thumbnail.jpg";
    }
        
?>

<div class="user-profile">

    <h1><?= Html::encode($this->title) ?></h1>

    <img src="<?=$image?>" width="200" height="200"/>
    
    <p>[[Here be user profile configuration]]</p>
    
    <?= $this->render('../post/post-list', ['dataProvider' => $dataProvider])?>
    
</div>

