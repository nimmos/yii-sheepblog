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
     * Populates a new comment with the data
     * obtained with the corresponding model.
     * 
     * @param type $user_id
     * @param type $post_id
     * @return \app\models\TblComment
     */
    public function newComment($user_id, $post_id)
    {
        $comment = new TblComment();
        $comment->user_id = $user_id;
        $comment->post_id = $post_id;
        $comment->content = $this->content;
        return $comment;
    }
}
