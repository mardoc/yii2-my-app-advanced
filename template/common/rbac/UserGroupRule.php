<?php
	/*Пример группы правил*/
	namespace common\rbac;
	
	use yii\rbac\Rule;
	
	class UserGroupRule extends Rule
	{
		public $name = 'userGroup';
		
		public function execute($user, $item, $params)
		{
			if (!\Yii::$app->user->isGuest) {
				$group = \Yii::$app->user->identity->group;
				if ($item->name === 'admin') {
					return $group == 'admin';
				} elseif ($item->name === 'moder') {
					return $group == 'user' || $group == 'admin';
				}
			}
			return false;
		}
	}