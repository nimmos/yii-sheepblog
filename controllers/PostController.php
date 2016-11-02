<?php

namespace app\controllers;

use app\models\TblComment;
use app\models\TblImage;
use app\models\TblPost;
use app\models\TblTag;
use app\models\TblUser;
use DateInterval;
use DateTime;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\BaseFileHelper;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\UploadedFile;

class PostController extends Controller
{

    /**
     * Yii2 executes this function before Controller initialization
     */
    public function init() {

        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

        // Store the user role in a $_SESSION variable

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($role)) {
            $_SESSION["role"] = current($role)->name;
        } else {
            $_SESSION["role"] = 'guest';
        }

        // Layout assignment depending on user role

        switch ($_SESSION["role"]) {
            case 'admin':
            case 'author':
                $this->layout = 'user';
                break;
            case 'guest':
            default:
                $this->layout = 'guest';
        }

        parent::init();
    }

    /**
     * This function is called before every action
     *
     * @param type $action
     * @return boolean
     */
    public function beforeAction($action) {

        if(parent::beforeAction($action)) {

            switch($this->action->id) {
                case 'index':
                case 'set-cookie':
                case 'post':
					 case 'search':
                    return true;
                default:
                    // If user is guest, lead it back to home
                    if (Yii::$app->user->isGuest) {
                        return $this->goHome();
                    } else {
                        return true;
                    }
            }
        }
    }

    /**
     * Default index.
     *
     * @return type
     */
    public function actionIndex($s=null)
    {

        // Establish query for retrieving posts

        if(!isset($s)||empty($s))
        {
            $s = array();

            // If tagstring not specified, retrieve all posts
            $query = TblPost::find()->orderBy('time DESC');

        } else {

            $s = TblTag::cleanStringToArray($s);

            // Retrieve posts with selected tags
            $query = TblPost::find()
                    ->where([
                        'post_id' => TblTag::getPostsByTags($s)
                    ])->orderBy('time DESC');
        }

        // Retrieve all posts in ActiveDataProvider
        // sorted from recent posts to older posts
        $posts = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 3 ],
        ]);

        // Retrieve all tags in ActiveDataProvider
        // sorted alphabetically
        $tags = (new ActiveDataProvider([
            'query' => TblTag::find()->orderBy('tagname ASC'),
            'pagination' => false,
        ]))->getModels();

        return $this->render('index', [
            'posts' => $posts,
            'tags' => $tags,
        ]);
    }

    /**
     * Performs a tag search and echoes back to
     * the view that called this action
     */
    public function actionSearch()
    {
       if(Yii::$app->request->isPost) {

            $searchstring = TblTag::cleanStringToArray(
                Yii::$app->request->post()["searchstring"]
            );

            $result = TblTag::searchTags($searchstring);
            $tags = array();

            // Store in an array

				if(!empty($result)){
					foreach($result as $tag) {
						$tags[] = $tag["tagname"];
					}
				}
            echo TblTag::turnString($tags);
        }
    }

    /**
     * Sets a cookie for accepting cookies
     *
     * @return type
     */
    public function actionSetCookie()
    {
        // Get cookies
        $cookies = Yii::$app->response->cookies;

        // Add a new one
        $cookies->add(new Cookie([
            'name' => 'cookie-accept',
            'value' => true,
            'expire' => (new DateTime())->add(new DateInterval('P2Y'))->getTimestamp(),
        ]));

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

        // Load its tags
        $post->tags = TblTag::getTags($post->post_id);

        // Obtain the comments of the post
        $comments = new ActiveDataProvider([
            'query' => TblComment::find()
                ->where(['post_id' => $p]),
        ]);

        // Obtain author of the post
        $author = TblUser::findById($post->user_id);

        // Publish new comment if POST retrieves one

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
            'post' => $post,
            'author' => $author,
            'comment' => $comment,
            'comments' => $comments,
        ]);
    }

    /**
     * Displays post composing page.
     *
     * @return string
     */
    public function actionPostCompose ()
    {

        $post = new TblPost();
        $image = new TblImage();

        if ($post->load(Yii::$app->request->post()) && $post->validate())
        {
            $post->user_id = Yii::$app->user->id;

            // Retrieves the uploaded image

            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');

            // If there's an image, save it

            if (isset($image->imageFile)) {

                TblPost::savePost($post, $image);

            } else {
                $post->save();
            }

            return $this->redirect(['post/post', 'p' => $post->post_id]);
        }

        return $this->render('post-compose', [
            'post' => $post,
            'image' => $image,
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

        // Security (checks if the user is admin or post author)
        if (!Yii::$app->user->can('updatePost') &&
        !Yii::$app->user->can('updateOwnPost',
            ['user_id' => TblPost::getAuthorId($p)]))
        {
            $this->goHome();
        }

        // Obtain the post to edit
        $post = TblPost::getPostById($p);

        // Load its tags
        $post->tags = TblTag::getTags($post->post_id);

        // This is for header image update
        $image = new TblImage();

        // Update the post

        if ($post->load(Yii::$app->request->post()) && $post->validate())
        {
            // Organize tags (update post tags and create them if new)
            TblTag::organizeTags(
                $post->post_id,
                $post->tags,
                TblTag::turnArray(Yii::$app->request->post()["TblPost"]["tags"])
            );

            // Retrieve uploaded image
            $image->imageFile = UploadedFile::getInstance($image, 'imageFile');

            // If image exists, save post and image
            if (isset($image->imageFile)) {

                TblPost::savePost($post, $image);

            // If image doesn't exist, only save post
            } else {
                $post->save();
            }

            return $this->goBack();
        }

        // Obtain the image of the post
        // THIS DOESN'T WORK YET

//        if (isset($post->headerimage)) {
//
//            $path = TblImage::routePostHeaderDir($post->user_id, $post->post_id)
//                    . TblImage::HEADER . TblImage::ORIGINAL . $post->headerimage;
//
//            $imagine = new Imagine();
//            $image->imageFile = $imagine->open($path);
//        }

        return $this->render('post-compose', [
            'post' => $post,
            'image' => $image,
            'edit' => true
        ]);
    }

    /**
     * Deletes a specified post
     *
     * @param type $p
     * @return type
     */
    public function actionDeletePost ($p)
    {

        // Security (checks if the user is admin or post author)
        if (!Yii::$app->user->can('deletePost') &&
        !Yii::$app->user->can('deleteOwnPost',
            ['user_id' => TblPost::getAuthorId($p)]))
        {
            $this->goHome();
        }

        // Obtain post to delete
        $post = TblPost::findOne($p);

        if (isset($post)) {

            // Delete folder for header and thumbnail of the post

            $path = TblImage::pathGenerator(
                    $post->user_id,
                    TblImage::PATH_POST,
                    null,
                    null,
                    $post->post_id);

            if(file_exists($path))
            {
                BaseFileHelper::removeDirectory($path);
            }

            // Delete images of the post

            $images = TblImage::getImagePathsFromContent($post->content, true);

            if(!empty($images))
            {
                foreach($images as $imagelink) {
                    $path = str_replace('\\', '/', getcwd()) . '/' . $imagelink;
                    if(file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            // Delete comments

            $comments = TblComment::findAll(['post_id' => $p]);

            foreach($comments as $comment) {
                Yii::$app->runAction('/post/delete-comment',
                    ['c' => $comment->comment_id]);
            }

            // Delete post

            $post->delete();

            return $this->goBack();
        }
        return $this->goBack();
    }

    /**
     * Deletes a specified comment
     *
     * @param type $c
     * @param type $p
     * @return type
     */
    public function actionDeleteComment ($c)
    {

        $comment = TblComment::findOne($c);

        if (isset($comment))
        {

            // Delete images of the comment

            $images = TblImage::getImagePathsFromContent($comment->content, true);
            if(!empty($images))
            {
                foreach($images as $imagelink) {
                    $path = str_replace('\\', '/', getcwd()) . '/' . $imagelink;
                    if(file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            // Delete comment

            $comment->delete();

            return $this->goBack();
        }
    }
}
