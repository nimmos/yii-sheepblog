<?php

namespace app\controllers;

use Yii;

// Models for the blog
use app\models\TblUser;
use app\models\TblPost;
use app\models\TblComment;

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
        // Obtain the required post by its id
        $post = TblPost::getPostById($p);
        
        // Publish new comments
        $comment = new TblComment();
        if ($comment->load(Yii::$app->request->post()) && $comment->validate())
        {
            $comment->post_id = $p;
            $comment->user_id = Yii::$app->user->id;
            if ($comment->save())
            {
                return $this->refresh();
            }
        }
        
        return $this->render('post', [
            'post' => $post, 'comment' => $comment,
            'author' => TblUser::findUsernameById($post->user_id),
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
        $model = new TblPost();

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->user_id = Yii::$app->user->id;
            if($model->save()) { return $this->goBack(); }
        }
        return $this->render('post-compose', [
            'model' => $model,
            'edit' => false
        ]);
    }

    /**
     * Edits a specified post
     * 
     * @param type $p
     * @return type
     */
    public function actionEditPost ($p)
    {
        // Obtain the post to edit
        $model = TblPost::getPostById($p);
                
        if ($model->load(Yii::$app->request->post())
                && $model->validate()
                && $model->save())
        {            
            return $this->redirect(['post/post', 'p' => $p]);
        }
        
        return $this->render('post-compose', [
            'model' => $model,
            'edit' => true
        ]);
    }
}
