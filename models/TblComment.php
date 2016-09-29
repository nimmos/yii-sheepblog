<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_comment".
 *
 * @property integer $comment_id
 * @property integer $user_id
 * @property integer $post_id
 * @property string $time
 * @property string $content
 *
 * @property Post $post
 * @property User $user
 */
class TblComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // user_id, post_id rules
            [['user_id', 'post_id'], 'integer'],
            ['user_id', 'exist', 'skipOnError' => true, 'targetClass' => TblUser::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            ['post_id', 'exist', 'skipOnError' => true, 'targetClass' => TblPost::className(), 'targetAttribute' => ['post_id' => 'post_id']],
            // time rules
            ['time', 'safe'],
            // content rules
            ['content', 'required', 'message' => 'You can\'t comment nothing'],
            ['content', 'string'],            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => 'Comment ID',
            'user_id' => 'User ID',
            'post_id' => 'Post ID',
            'time' => 'Time',
            'content' => 'Say something',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(TblPost::className(), ['post_id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(TblUser::className(), ['user_id' => 'user_id']);
    }
}
