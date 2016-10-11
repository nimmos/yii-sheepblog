<?php

use app\models\TblImage;
use app\models\TblUser;
use yii\helpers\Html;

    $this->title = "User profile";
    $this->params['breadcrumbs'][] = $this->title;

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
    
</div>

