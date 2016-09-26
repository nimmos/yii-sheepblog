<?php

namespace app\controllers;

use Yii;

// Helpers
use yii\helpers\ArrayHelper;

// Models for the blog
use app\models\TblUser;
use app\models\TblPost;
use app\models\TblComment;

// Models for the blog forms
use app\models\PostForm;
use app\models\CommentForm;

class PostController extends \yii\web\Controller
{
    public function actionIndex()
    {
        // Retrieve all posts
        $posts = TblPost::find()->all();
        
        // Sort from recent posts to older posts
        ArrayHelper::multisort($posts, ['time'], [SORT_DESC]);
        
        return $this->render('index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Displays post view.
     *
     * @param type $p post_id
     * @return type
     */
    public function actionPost ($p = 1)
    {
        $model = new CommentForm();
        
        // Publish new comments
        if ($model->load(Yii::$app->request->post())
                && $model->validate()
                && $model->newComment( Yii::$app->user->getId(), $p)
                ->save())
        {
            return $this->refresh();
        }
        
        // Obtain the required post by its id
        $post = TblPost::getPostById($p);
        
        return $this->render('post', [
            'post' => $post,
            'author' => TblUser::getUsernameById($post->user_id),
            'comments' => TblComment::findAll(['post_id' => $p]),
        ]);
    }

    /**
     * Displays post composing page.
     *
     * @return string
     */
    public function actionPostCompose ()
    {
        $model = new PostForm();

        if ($model->load(Yii::$app->request->post())
                && $model->validate()
                && $model->newPost(Yii::$app->user->getId())->save())
        {
            return $this->goBack();
        }
        return $this->render('post-compose', ['model' => $model]);
    }

}
