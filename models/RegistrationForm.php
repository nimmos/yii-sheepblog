<?php
	namespace app\models;
	use Yii;
	use yii\base\Model;
	
	class RegistrationForm extends Model {
		public $username;
		public $password;
		public $email;
		public $subscriptions;
		public $photos;

		/**
		* @return array customized attribute labels
		*/
		public function attributeLabels() {
			return [
				'username' => 'Username',
				'password' => 'Password',
				'email' => 'Email',
				'subscriptions' => 'Subscriptions',
				'photos' => 'Photos',
			];
		}
	}
?>
