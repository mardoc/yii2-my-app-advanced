<?php
	namespace common\services;

	use common\models\Profile;
	use Yii;
	use common\models\Auth;
	use common\models\User;
	use yii\authclient\ClientInterface;
	use yii\helpers\ArrayHelper;
	
	/**
	 * AuthHandler handles successful authentication via Yii auth component
	 */
	class AuthHandler
	{
		
		/**
		 * @var ClientInterface
		 */
		private $client;
		
		public function __construct(ClientInterface $client)
		{
			$this->client = $client;
		}
		
		public function handle()
		{
			if (!Yii::$app->user->isGuest) {
				return;
			}
			
			$attributes = $this->client->getUserAttributes();
			
			$auth = $this->findAuth($attributes);
			if ($auth) {
				/* @var User $user */
				$user = $auth->user;
				return Yii::$app->user->login($user);
			}
			if ($user = $this->createAccount($attributes)) {
				return Yii::$app->user->login($user);
			}
		}
		
		/**
		 * @param array $attributes
		 * @return Auth
		 */
		private function findAuth($attributes)
		{
			$id = ArrayHelper::getValue($attributes, 'id');
			$params = [
				'source_id' => $id,
				'source' => $this->client->getId(),
			];
			return Auth::find()->where($params)->one();
		}
		
		/**
		 *
		 * @param type $attributes
		 * @return User|null
		 */
		private function createAccount($attributes)
		{
			$email = ArrayHelper::getValue($attributes, 'email');
			$id = ArrayHelper::getValue($attributes, 'id');
			$name = ArrayHelper::getValue($attributes, 'name');
			
			if ($email !== null && User::find()->where(['email' => $email])->exists()) {
				return;
			}
			if (empty($email)){
				$email = time();
			}
			$user = $this->createUser($email, $name);
			
			
			$transaction = User::getDb()->beginTransaction();
			if ($user->save()) {
				$auth = $this->createAuth($user->id, $id);
				$this->createProfile($user->id, '','');
				if ($auth->save()) {
					$transaction->commit();
					return $user;
				}
			}
			$transaction->rollBack();
		}
		
		private function createUser($email, $name)
		{
			return new User([
				'username' => $name,
				'email' => $email,
				'auth_key' => Yii::$app->security->generateRandomString(),
				'password_hash' => Yii::$app->security->generatePasswordHash(Yii::$app->security->generateRandomString()),
			]);
		}
		
		private function createProfile($user_id, $first_name, $last_name)
		{
			return new Profile([
				'user_id' => $user_id,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'about' => '',
			]);
		}
		
		private function createAuth($userId, $sourceId)
		{
			return new Auth([
				'user_id' => $userId,
				'source' => $this->client->getId(),
				'source_id' => (string) $sourceId,
			]);
		}
		
	}