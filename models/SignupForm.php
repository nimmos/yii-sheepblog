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
    
    /**
     * Saves a new user to the database.
     * 
     * @return boolean
     */
    public function signup () {
        
        if($this->validate()) {
            Yii::$app->db->createCommand()->insert('tbl_user', [
                'username' => $this->username,
                'email' => $this->email,
                'password' => Yii::$app->getSecurity()->generatePasswordHash($this->password),
                'authkey' => Yii::$app->getSecurity()->generateRandomString(),
            ])->execute();
        }
        return true;
    }
}
?>
