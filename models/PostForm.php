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
}
