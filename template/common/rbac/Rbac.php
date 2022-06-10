<?php
	/**
	 * Created by Mardoc.
	 * Web : http://mardoc.net
	 * Mail: works@mardoc.net
	 * DateTime: 04.06.2022 20:49
	 *
	 * класс константов разрешений  для удобства использования
	 * в контроллерах к примеру разрешить доступ к панели
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
						[
						'allow' => true,
						'roles' => [Rbac::PERMISSION_ADMIN_PANEL],
						],
					],
				],
			];
	}
	 *  или использовать в коде где-то вот так
	Yii::$app->user->can(Rbac::PERMISSION_ADMIN_PANEL)
	 *
	 *
	 *
	 */
	
	namespace common\rbac;
	
	
	class Rbac
	{
		const PERMISSION_ADMIN_PANEL = 'permAdminPanel';
		const PERMISSION_MODIFY_USER_NAME = 'permModifyUserName';
		
		static function getConstants() {
			$oClass = new \ReflectionClass(__CLASS__);
			return $oClass->getConstants();
		}
	}