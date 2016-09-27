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
    /**
     * Default index.
     * 
     * @return type
     */
    public function actionIndex()
    {
        // Retrieve all posts
        // sorted from recent posts to older posts
        $posts = TblPost::find()
                ->orderBy('time DESC')
                ->all();
                        
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
    public function actionPost ($p)
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
        
        // Identify who the current user is,
        // and if it's the author of the post
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest)
        {
            $isAuthor = Yii::$app->user->id == $post->user_id;
        }
        
        return $this->render('post', [
            'isGuest' => $isGuest,
            'isAuthor' => $isAuthor,
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
        return $this->render('post-compose', [
            'model' => $model,
            'edit' => false
        ]);
    }

    
    public function actionEditPost ($p)
    {
        // Obtain the post to edit
        $post = TblPost::getPostById($p);
        
        $model = new PostForm();
        $model->populateForm($post);
        
        if ($model->load(Yii::$app->request->post())
                && $model->validate())
        {
            $post->updateFromModel($model);
            $post->save();
            
            return $this->redirect(['post/post',
                'p' => $p]);
        }
        
        return $this->render('post-compose', [
            'model' => $model,
            'edit' => true
        ]);
    }
}
