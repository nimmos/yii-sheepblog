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

    /**
     * Saves a new comment to the database.
     * 
     * @param type $user_id this is the author of the post
     * @return boolean
     */
    public function publishComment($user_id, $post_id)
    {
        if($this->validate()) {
            Yii::$app->db->createCommand()->insert('tbl_comment', [
                'user_id' => $user_id,
                'post_id' => $post_id,
                'content' => $this->content,
            ])->execute();
        }
        return true;
    }
}
