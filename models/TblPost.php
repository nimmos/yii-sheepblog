<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_post".
 *
 * @property integer $post_id
 * @property integer $user_id
 * @property string $time
 * @property string $title
 * @property string $content
 *
 * @property Comment[] $comments
 * @property User $user
 */
class TblPost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['time'], 'safe'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 160],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'post_id' => 'Post ID',
            'user_id' => 'User ID',
            'time' => 'Time',
            'title' => 'Title',
            'content' => 'Content',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
    
    /**
     * Gets a post from the database by looking for its id.
     * 
     * @param type $post_id
     * @return type
     */
    public static function getPostById($post_id = 1) {
        $post = TblPost::findOne($post_id);
        return $post;
    }
}
