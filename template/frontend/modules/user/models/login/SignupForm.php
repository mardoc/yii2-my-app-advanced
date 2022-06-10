<?php

namespace  frontend\modules\user\models\login;

use common\models\Profile;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    
    private $user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User',
				'message' => Yii::t('app','This username has already been taken.')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User',
				'message' => Yii::t('app','This email address has already been taken.')],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }
	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'username' => Yii::t('app','User Name'),
			'password' => Yii::t('app','Password'),
			'email' => Yii::t('app','Email'),
		];
	}
    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $this->user = new User();
		$this->user->username = $this->username;
		$this->user->email = $this->email;
		$this->user->setPassword($this->password);
		$this->user->generateAuthKey();
		$this->user->generateEmailVerificationToken();
		
		if ($this->user->save()){
			$profile =  new Profile();
			$profile->user_id = $this->user->id;

			return $profile->save() && $this->sendEmail($this->user);
		}
		return null;
    }
	
    public function getUser(){
    	return $this->user;
	}
    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
