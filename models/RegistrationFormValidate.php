<?php
	namespace app\models;
	use Yii;
	use yii\base\Model;
	
	class RegistrationFormValidate extends Model {
		public $username;
		public $password;
		public $email;
		public $country;
		public $city;
		public $phone;

		public function rules() {
			return [
				// These attributes are required
				[['password', 'email', 'country', 'phone'], 'required'],
				// Customizing error message for username
				['username', 'required', 'message' => 'Username is HIGHLY RECOMMENDED, ergo: required'],
				// Trims spaces around country
				['country', 'trim'],
				// Turns empty city into null
				['city', 'default'],
				// The email attribute should be a valid email address
				['email', 'email'],
				// Dynamic validation
				//['city', \app\components\CityValidator::className()],
			];
		}
	}
?>