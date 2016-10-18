<?php

namespace app\models;

/**
 * This is the model class for table "tbl_post".
 *
 * @property integer $post_id
 * @property integer $user_id
 * @property string $time
 * @property string $title
 * @property string $content
 * @property string $headerimage
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
            // user_id rules
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblUser::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            // time rules
            [['time'], 'safe'],
            // title rules
            ['title', 'required', 'message' => 'You have to write a title'],
            [['title'], 'string', 'max' => 160],
            // content rules
            ['content', 'required', 'message' => 'Don\'t you have anything to say? Write down something'],
            [['content'], 'string'],
            // header rules
            [['headerimage'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'post_id' => 'Post ID',
            'user_id' => 'Author',
            'time' => 'Created on',
            'title' => 'Post title',
            'content' => 'Post content',
            'headerimage' => 'Header Image',
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
    public static function getPostById($post_id = 1)
    {
        $post = TblPost::findOne($post_id);
        return $post;
    }
    
    /**
     * Saves a post and its header image in the database
     * 
     * @param type $post
     * @param type $image
     */
    public static function savePost($post, $image)
    {
        // Save the extension
        $post->headerimage = "." . $image->imageFile->extension;

        // Save post in db
        $post->save();

        // Save image in db
        $image->saveHeaderImage($post);
    }
}
