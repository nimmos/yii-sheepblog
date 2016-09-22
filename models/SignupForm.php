<?php
	namespace app\models;
	use Yii;
	use yii\base\Model;
	
	class SignupForm extends Model {
		public $username;
		public $email;
		public $password;
		
		public function rules() {
			return [
				// Everything is required
				['username', 'required', 'message' => 'User MUST HAVE a name'],
				['email', 'required', 'message' => 'You must enter an email'],
				['password', 'required', 'message' => 'This is also required'],
				// The email attribute should be a valid email address
				['email', 'email'],
				// Pasword has to be minimum 8 characters
				['password', 'string', 'min' => 8],
			];
		}
	}
?>