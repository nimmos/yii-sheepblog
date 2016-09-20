<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;

    // Scenarios for different users
    const SCENARIO_EMAIL_FROM_GUEST = 'EMAIL_FROM_GUEST';
    const SCENARIO_EMAIL_FROM_USER = 'EMAIL_FROM_USER';

    /**
    * Overriden
    * @return array with the possible scenarios.
    */
    public function scenarios () {
        return [
            self::SCENARIO_EMAIL_FROM_GUEST => ['name', 'email', 'subject', 'body', 'verifyCode'],
            self::SCENARIO_EMAIL_FROM_USER => ['subject', 'body', 'verifyCode'],
        ];
    }


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name' => 'The name of our respected customer',
            'email' => 'The electronic address',
            'subject' => 'The "too long; didn\'t read" field',
            'body' => 'Whatever our respected customer has to say',
            'verifyCode' => 'The security means to avoid robots',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return boolean whether the model passes validation
     */
    public function contact($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();

            return true;
        }
        return false;
    }
}
