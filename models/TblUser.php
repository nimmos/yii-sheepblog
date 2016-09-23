<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_user".
 *
 * @property integer $user_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $authkey
 *
 * @property TblComment[] $tblComments
 * @property TblPost[] $tblPosts
 */
class TblUser extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'email'], 'required'],
            [['user_id'], 'integer'],
            [['username', 'password'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 40],
            [['authkey'], 'string', 'max' => 50],
            [['username'], 'unique'],
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
            'authkey' => 'Authkey',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblComments()
    {
        return $this->hasMany(TblComment::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblPosts()
    {
        return $this->hasMany(TblPost::className(), ['user_id' => 'user_id']);
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
        throw new \yii\base\NotSupportedException();
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
    // These methods are required
    // for the login logic
    // @see models\LoginForm.php
    ////////////////////////////////////////////////

    /**
    * This will find a user in the db with the name $username
    */
    public static function findByUsername($username)
    {
        return self::findOne(['username' => $username]);
    }

    /**
    * This will validate a password
    */
    public function validatePassword ($password)
    {
        return $this->password === $password;
    }
    
    // TODO: consider creating a method that
    // returns the name of the user based on the $id
}
