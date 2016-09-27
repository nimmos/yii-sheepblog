<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PostForm extends Model
{
    public $title;
    public $content;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // Everything is required, customized error messages
            ['title', 'required', 'message' => 'You have to write a title'],
            ['content', 'required', 'message' => 'Don\'t you have anything to say? Write down something'],
            // title has to be maximum 160 characters
            ['title', 'string', 'max' => 160],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Post title',
            'content' => 'Post content',
        ];
    }
    
    /**
     * Populates a new post with the data
     * obtained with the corresponding model.
     * 
     * @param type $user_id
     * @return \app\models\TblPost
     */
    public function newPost($user_id, $post_id = null)
    {
        $post = new TblPost();
        $post->post_id = $post_id;
        $post->user_id = $user_id;
        $post->title = $this->title;
        $post->content = $this->content;
        return $post;
    }
    
    /**
     * Populates form model with post data,
     * used for editing posts.
     * 
     * @param type $post
     */
    public function populateForm($post)
    {
        $this->title = $post->title;
        $this->content = $post->content;
    }
}
