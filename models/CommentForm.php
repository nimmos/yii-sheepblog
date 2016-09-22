<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class CommentForm extends Model
{
    public $content;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // Everything is required, customized error messages
            ['content', 'required', 'message' => 'You can\'t comment nothing'],            
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'content' => 'Do you have something to say? Say it now!',
        ];
    }
}
