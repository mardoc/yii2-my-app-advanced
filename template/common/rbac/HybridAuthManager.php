<?php
	/**
	 * Created by Mardoc.
	 * Web : http://mardoc.net
	 * Mail: works@mardoc.net
	 * DateTime: 02.06.2022 21:11
	 *
	 * Гибридный класс для rbac берет лучшее из \yii\rbac\PhpManager и \yii\rbac\DbManager
	 * в модели  common\models\User должна быть колонка role строка из миграции вида:
	 * $this->execute("ALTER TABLE `user` ADD COLUMN `role` ENUM ( 'user','moder','admin') DEFAULT 'user'");
	 *
	 */
	
	namespace common\rbac;
	
	
	use common\models\User;
	use yii\rbac\Assignment;
	use yii\rbac\Permission;
	use yii\rbac\PhpManager;
	use yii\rbac\Role;
	
	class HybridAuthManager extends PhpManager
	{
		/**
		 * @var string Значение роли по умолчанию устанавливаеться при сбросе роли
		 * @see revoke()
		 * @see revokeAll()
		 */
		public $defaultRole = 'user';
		
		/**
		 * {@inheritdoc}
		 */
		public function getAssignments($userId)
		{
			if ($userId && $user = $this->getUser($userId)){
				return [
					$user->role => new Assignment([
						'userId' => $user->id,
						'roleName' => $user->role,
						'createdAt' => $user->created_at,
					])
				];
			}
			return [];
		}
		/**
		 * {@inheritdoc}
		 */
		public function getAssignment($roleName, $userId)
		{
			if ($userId && $user = $this->getUser($userId)) {
				return null;
			}
			if ($user->role != $roleName) {
				return null;
			}
			return new Assignment([
				'userId' => $user->id,
				'roleName' => $user->role,
				'createdAt' => $user->created_at,
			]);
		}
		
		/**
		 * @param Role|Permission $role
		 * @param string|int $userId the user ID (see [[\yii\web\User::id]])
		 * @return void|Assignment
		 */
		public function assign($role, $userId)
		{
			
			if ($userId && $user = $this->getUser($userId)) {
				$this->setRole($user, $role->name);
			}
		}
		/**
		 * @param Role|Permission $role
		 * @param string|int $userId the user ID (see [[\yii\web\User::id]])
		 */
		public function revoke($role, $userId)
		{
			if ($userId && $user = $this->getUser($userId)) {
				if ($user->role == $role->name) {
					$this->setRole($user, $this->defaultRole);
				}
			}
		}
		/**
		 * {@inheritdoc}
		 */
		public function revokeAll($userId)
		{
			if ($userId && $user = $this->getUser($userId)) {
				$this->setRole($user, $this->defaultRole);
			}
		}
		
		
		/**
		 * @param $userId
		 * @return User|\yii\web\IdentityInterface|null
		 */
		public function getUser($userId){
		
			if ( \Yii::$app->id ==='app-frontend'
				&& !\Yii::$app->user->isGuest && \Yii::$app->user->id == $userId ){
				return \Yii::$app->user->identity;
			}else{
				return User::findOne(['id'=>$userId]);
			}
		}
		
		/**
		 * @param User $user
		 * @param $roleName
		 */
		public function setRole($user, $roleName){
			$user->role = $roleName;
			$user->updateAttributes(['role'=>$roleName]);
		}
	}