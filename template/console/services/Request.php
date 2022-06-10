<?php
	/**
	 * Created by Mardoc.
	 * Web : http://mardoc.net
	 * Mail: works@mardoc.net
	 * DateTime: 04.06.2022 20:23
	 *
	 * Нужна для работы с классом Request из консоли
	 */
	
	namespace console\services;
	
	
	class Request extends \yii\console\Request
	{
		public $enableCsrfCookie =false;
		
		public function getUserIP(){
			return '127.0.0.1';
		}
	}