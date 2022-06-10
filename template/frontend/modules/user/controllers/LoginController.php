<?php

namespace frontend\modules\user\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\services\AuthHandler;


/**
 * Default controller for the `user` module
 */
class LoginController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                        [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                        [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
		
        return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }
	
	
	/**
	 * @param $client
	 */
	public function onAuthSuccess($client)
    {
		(new AuthHandler($client))->handle();
    }

	/*ошибка action для https://developers.facebook.com/apps/776491896102837/fb-login/settings/
	Отменить авторизацию
	*/
    public function actionFbfail(){
    	Yii::warning('actionFbfail');
    	return 'ok';
	}
	/*удаление пользователя action для https://developers.facebook.com/apps/776491896102837/fb-login/settings/
	Запросы на удаление данных
	 */
	public function actionFbdel(){
    	Yii::warning('actionFbdel');
    	return 'ok';
	}

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signup user up. Регистрация
     *
     * @return mixed
     */
    public function actionSignup()
    {
		$model = new \frontend\modules\user\models\login\SignupForm();
		if ($model->load(Yii::$app->request->post()) && $model->signup()) {
			Yii::$app->session->setFlash('success', Yii::t('app','Thank you for registration. Please check your inbox for verification email.'));
			if (Yii::$app->getUser()->login( $model->getUser() )) {
				return $this->goHome();
			}
		}
	
		return $this->render('signup', [
			'model' => $model,
		]);
    }
	
	/**
	 * Login a user.
	 * @return mixed
	 */
	public function actionLogin()
	{
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}
		
		$model = new \frontend\modules\user\models\login\LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			return $this->goBack();
		}
		
		$model->password = '';
		
		return $this->render('login', [
			'model' => $model,
		]);

	}
	
    /**
     * Requests password reset.
	 * при нажатии "забыл пароль"
	 * @return mixed
     */
    public function actionRequestPasswordReset()
    {

        $model = new \frontend\modules\user\models\login\PasswordResetRequestForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail()) {
				Yii::$app->session->setFlash('success', Yii::t('app','Check your email for further instructions.'));
			
				return $this->goHome();
			}
		
			Yii::$app->session->setFlash('error', Yii::t('app','Sorry, we are unable to reset password for the provided email address.'));
		}
	
		return $this->render('requestPasswordResetToken', [
			'model' => $model,
		]);
    }

    /**
     * Resets password. After Email links.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
		try {
			$model = new \frontend\modules\user\models\login\ResetPasswordForm($token);
		} catch (InvalidArgumentException $e) {
			throw new BadRequestHttpException($e->getMessage());
		}
	
		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
			Yii::$app->session->setFlash('success', Yii::t('app','New password saved.'));
		
			return $this->goHome();
		}
	
		return $this->render('resetPassword', [
			'model' => $model,
		]);
    }
	
	/**
	 * Verify email address
	 *
	 * @param string $token
	 * @throws BadRequestHttpException
	 * @return yii\web\Response
	 */
	public function actionVerifyEmail($token)
	{
		try {
			$model = new \frontend\modules\user\models\login\VerifyEmailForm($token);
		} catch (InvalidArgumentException $e) {
			throw new BadRequestHttpException($e->getMessage());
		}
		if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
			Yii::$app->session->setFlash('success', Yii::t('app','Your email has been confirmed!'));
			return $this->goHome();
		}
		
		Yii::$app->session->setFlash('error', Yii::t('app','Sorry, we are unable to verify your account with provided token.'));
		return $this->goHome();
	}
	
	/**
	 * Resend verification email
	 *
	 * @return mixed
	 */
	public function actionResendVerificationEmail()
	{
		$model = new \frontend\modules\user\models\login\ResendVerificationEmailForm();
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			if ($model->sendEmail()) {
				Yii::$app->session->setFlash('success', Yii::t('app','Check your email for further instructions.'));
				return $this->goHome();
			}
			Yii::$app->session->setFlash('error', Yii::t('app','Sorry, we are unable to resend verification email for the provided email address.'));
		}
		
		return $this->render('resendVerificationEmail', [
			'model' => $model
		]);
	}

}
