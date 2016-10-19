<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\BaseFileHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "tbl_user".
 *
 * @property integer $user_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $authkey
 * @property string $userimage
 *
 * @property Comment[] $comments
 * @property Post[] $posts
 */
class TblUser extends ActiveRecord implements IdentityInterface
{
    // Scenarios
    const SCN_SIGNUP = 'signup';
    const SCN_UPDATE = 'update';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_user';
    }
    
    public function scenarios()
    {
        return [
            self::SCN_SIGNUP => ['user_id', 'username', 'email', 'password', 'authkey', 'userimage'],
            self::SCN_UPDATE => ['username', 'email', 'password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // user_id rules
            ['user_id', 'integer'],
            // username rules
            ['username', 'unique'],
            ['username', 'required', 'message' => 'User MUST HAVE a name'],
            ['username', 'string', 'max' => 30],
            // email rules
            ['email', 'required', 'message' => 'You must enter an email'],
            ['email', 'email'],
            ['email', 'string', 'max' => 40],
            // password rules
            ['password', 'required', 'message' => 'This is also required', 'on' => 'signup'],
            ['password', 'string', 'length' => [8, 80]],
            // authkey rules
            ['authkey', 'string', 'max' => 50],
            // userimage rules (image extension)
            ['userimage', 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'userimage' => 'User image',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['user_id' => 'user_id']);
    }
    
    ////////////////////////////////////////////////
    // IdentityInterface implementation
    ////////////////////////////////////////////////

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException();
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getAuthKey()
    {
        return $this->authkey;
    }

    public function validateAuthKey($authkey)
    {
        return $this->authkey === $authkey;
    }
    
    ////////////////////////////////////////////////
    // Getters-setters
    ////////////////////////////////////////////////
    
    /**
     * Encrypts a password generating a hash string.
     * 
     * @param type $password
     */
    public function setPassword ($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * Sets an authorization key
     */
    public function setAuthkey ()
    {
        $this->authkey = Yii::$app->getSecurity()->generateRandomString();
    }
    
    ////////////////////////////////////////////////
    // These methods are required
    // for the login logic
    // @see models\LoginForm.php
    ////////////////////////////////////////////////

    /**
     * This will find a user in the db with the name $username
     * 
     * @param type $username
     * @return type
     */
    public static function findByUsername($username)
    {
        return self::findOne(['username' => $username]);
    }

    /**
     * Validates a password.
     * 
     * @param type $password
     * @return type
     */
    public function validatePassword ($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    
    ////////////////////////////////////////////////
    // These methods look up the database
    ////////////////////////////////////////////////
    
    /**
     * This will find a user in the db based on its id.
     * 
     * @param int $user_id
     * @return TblUser
     */
    public static function findById($user_id)
    {
        return self::findOne($user_id);
    }
    
    /**
     * It finds a username based on its id.
     * 
     * @param int $user_id
     * @return string
     */
    public static function findUsernameById ($user_id)
    {
        return self::findOne($user_id)->username;
    }
    
    /**
     * It finds an id based on the username.
     * 
     * @param string $username
     * @return int
     */
    public static function findIdByUsername ($username)
    {
        return self::findOne(['username' => $username])->user_id;
    }
    
    ////////////////////////////////////////////////
    // Saving to the database
    ////////////////////////////////////////////////
    
    /**
     * Saves a user into the db.
     * 
     * @param type $user
     * @param type $isNew
     */
    public static function saveUser ($user, $isNew)
    {
        // Set security properties before performing save()
        
        if ($user->isAttributeChanged('password')) {
            $user->setPassword($user->password);
        }
        if ($isNew) { $user->setAuthkey(); }
        
        // Save into the database
        
        if ($user->validate() && $user->save())
        {
            
            // Role assignment
            
            if ($isNew) {
                $auth = Yii::$app->authManager;
                $role = $auth->getRole('author');
                $auth->assign($role, $user->user_id);
            }

            return true;
        } else { return false; }
    }
}
