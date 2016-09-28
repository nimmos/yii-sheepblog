<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionDeleteInit()
    {
        $auth = Yii::$app->authManager;
        
        ////////////////////////////////////////////////
        // Retrieve rules and roles
        ////////////////////////////////////////////////
        $authorRule = $auth->getRule('isAuthor');
        $author = $auth->getRole('author');
        $admin = $auth->getRole('admin');
        
        ////////////////////////////////////////////////
        // Building authorization data
        ////////////////////////////////////////////////
        
        $deleteOwnPost = $auth->createPermission('deleteOwnPost');
        $deleteOwnPost->description = 'Update own post';
        $deleteOwnPost->ruleName = $authorRule->name;
        $auth->add($deleteOwnPost);
        
        $deleteOwnComment = $auth->createPermission('deleteOwnComment');
        $deleteOwnComment->description = 'Delete own comment';
        $deleteOwnComment->ruleName = $authorRule->name;
        $auth->add($deleteOwnComment);
        
        $deletePost = $auth->createPermission('deletePost');
        $deletePost->description = 'Delete all posts';
        $auth->add($deletePost);
        
        $deleteComment = $auth->createPermission('deleteComment');
        $deleteComment->description = 'Delete all comments';
        $auth->add($deleteComment);
        
        ////////////////////////////////////////////////
        // Update roles
        ////////////////////////////////////////////////
        
        $auth->addChild($author, $deleteOwnPost);
        $auth->addChild($author, $deleteOwnComment);
        
        $auth->addChild($admin, $deletePost);
        $auth->addChild($admin, $deleteComment);
    }
    
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        
        ////////////////////////////////////////////////
        // Adding rules
        ////////////////////////////////////////////////
        
        $authorRule = new \app\rbac\AuthorRule();
        $auth->add($authorRule);
        
        ////////////////////////////////////////////////
        // Building authorization data
        ////////////////////////////////////////////////
        
        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);
        
        $comment = $auth->createPermission('comment');
        $comment->description = 'Comment on a post';
        $auth->add($comment);
        
        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update own post';
        $updateOwnPost->ruleName = $authorRule->name;
        $auth->add($updateOwnPost);
        
        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update all posts';
        $auth->add($updatePost);
        
        ////////////////////////////////////////////////
        // Creating roles
        ////////////////////////////////////////////////
        
        $author = $auth->createRole('author');
        $author->description = 'Author user';
        $auth->add($author);
        $auth->addChild($author, $createPost);
        $auth->addChild($author, $comment);
        $auth->addChild($author, $updateOwnPost);
        
        $admin = $auth->createRole('admin');
        $author->description = 'Administrator';
        $auth->add($admin);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);
        
        ////////////////////////////////////////////////
        // assign 'admin' role to the first user
        ////////////////////////////////////////////////
        
        $auth->assign($admin, 1);
    }
}
