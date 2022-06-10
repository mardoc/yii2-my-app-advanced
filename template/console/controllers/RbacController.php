<?php
	/**
	 * Created by Mardoc.
	 * Web : http://mardoc.net
	 * Mail: works@mardoc.net
	 * DateTime: 30.05.2022 19:53
	 */
	namespace console\controllers;
	
	use common\models\User;
	use common\rbac\Rbac;
	use yii\console\Controller;
	use yii\helpers\Console;
	use yii\console\Exception;
	use yii\helpers\ArrayHelper;
	
	
	class RbacController  extends Controller
	{
		/**
		 * {@inheritdoc}
		 */
		/*public function behaviors()
		{

		}*/
		/** создаем настройку ролей
		 * php yii rbac/init
		 */
		public function actionInit()
		{
			$this->stdout('Создаем настройку ролей ', Console::FG_CYAN);
			$auth = \Yii::$app->authManager;
			$auth->removeAll(); //удаляем старые данные
			
			//Создадим для примера права для доступа к админке
			$adminPanel = $auth->createPermission(Rbac::PERMISSION_ADMIN_PANEL);
			$adminPanel->description = 'Админ панель';
			$auth->add($adminPanel);
			
			//Добавляем роли
			$user = $auth->createRole(User::ROLE_USER);
			$user->description = 'Пользователь';
			$auth->add($user);
			
			$admin_modify_user_name  = $auth->createPermission(Rbac::PERMISSION_MODIFY_USER_NAME);
			$admin_modify_user_name->description = 'Доступ Админа к редактированию пользоватлей';
			$auth->add($admin_modify_user_name);
			
			$moder = $auth->createRole(User::ROLE_MODER);
			$moder->description = 'Модератор';
			$auth->add($moder);
			
			// пример создания правила
			$rule = new \common\rbac\UserGroupRule();
			$auth->add($rule);
			
			//Добавляем потомков
			$auth->addChild($moder, $user);
			$auth->addChild($moder, $adminPanel);
			
			$admin = $auth->createRole(User::ROLE_ADMIN);
			$admin->description = 'Администратор';
			$auth->add($admin);
			$auth->addChild($admin, $moder);
			$auth->addChild($admin, $admin_modify_user_name);
			$this->stdout('Done!' . PHP_EOL);
		}
		
		/**
		 * Привязка Роли к пользователю через консоль
		 * php yii rbac/assign-role
		 */
		public function actionAssignRole()
		{
			$this->stdout('Привязка Роли к пользователю ', Console::FG_CYAN);
			$username = $this->prompt('Username:', ['required' => true]);
			$user = $this->findModel($username);
			$roleName = $this->select('Role:', ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'description'));
			
			$authManager = \Yii::$app->getAuthManager();
			$role = $authManager->getRole($roleName);
			
			$authManager->assign($role, $user->id);
			
			$this->stdout('Done!' . PHP_EOL);
		}
		
		
		/**
		 *  Отвязка Роли от пользователя
		 *  php yii rbac/revoke-role
		 */
		public function actionRevokeRole()
		{
			$this->stdout('Отвязка Роли от пользователя ', Console::FG_CYAN);
			$username = $this->prompt('Username:', ['required' => true]);
			$user = $this->findModel($username);
			$roleName = $this->select('Role:', ArrayHelper::merge(
				['all' => 'All Roles'],
				ArrayHelper::map(\Yii::$app->authManager->getRolesByUser($user->id), 'name', 'description'))
			);
			$authManager = \Yii::$app->getAuthManager();
			
			if ($roleName == 'all') {
				$authManager->revokeAll($user->id);
			} else {
				$role = $authManager->getRole($roleName);
				$authManager->revoke($role, $user->id);
			}
			$this->stdout('Done!' . PHP_EOL);
		}
		
		
		/**
		 *
		 * @param $username
		 * @return User|null
		 */
		private function findModel($username)
		{

			if (!$model = User::findOne(['username' => $username])) {
				throw new Exception('User is not found');
			}
			return $model;
		}
		
		/**
		 * Проверка доступа пользователя
		 * php yii rbac/check
		 */
		public function actionCheck(){
			
			$this->stdout('Проверка доступа пользователя ', Console::FG_CYAN);
			\Yii::$app->set('request', new \console\services\Request() );

			$username = $this->prompt('Username:', ['required' => true]);
			$user = $this->findModel($username);

			$auth = \Yii::$app->getAuthManager();
			\Yii::$app->user->login($user);
			
			$this->stdout('Check access for :'. $user->username, Console::FG_CYAN);
			
			foreach (Rbac::getConstants() as $permission){
				$this->stdout($permission.' : ', Console::FG_YELLOW);
				if (\Yii::$app->user->can($permission)) {
					$this->stdout('can', Console::FG_GREEN);
				}else{
					$this->stdout('disable', Console::FG_RED);
				}
				$this->stdout("\r\n");
			}
		

			$this->stdout("\r\n");
		}
		
		/**
		 * Тесты
		 * php yii rbac/test
		 */
		public function actionTest(){
			
			$this->stdout('Tests ', Console::FG_CYAN);
			$auth = \Yii::$app->getAuthManager();
			$user = User::findOne(['id'=>1]);
			
			\Yii::$app->user->login($user);

			$auth->revokeAll($user->id);

			$auth->assign( $auth->getRole(User::ROLE_MODER) ,$user->id );

			$this->stdout('Done!' . PHP_EOL);
		}
	}