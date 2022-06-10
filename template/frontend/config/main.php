<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
	'language'=>'en',
	'sourceLanguage'=>'en',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'multiLanguage'],
    'controllerNamespace' => 'frontend\controllers',
	'modules' => [
		'user' => [
			'class' => 'frontend\modules\user\Module',
		],
	],
    'components' => [
        'request' => [
			'baseUrl' => '',
			'class' => \common\components\MultiLanguage\MultiLangRequest::class,
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
			'loginUrl' => ['user/login/login'],
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
// https://www.yiiframework.com/extension/yiisoft/yii2-authclient/doc/guide/2.0/ru/installation
		'authClientCollection' => [
			// помощь по настройке и концепции https://www.youtube.com/watch?v=tIZsfqhKSDM&t=1264s
			'class' => 'yii\authclient\Collection',
			'clients' => [
				'google' => [
//получал тут https://console.developers.google.com/apis/credentials?
//помощь https://joomline.ru/docs/slogin/405-nastrojka-avtorizatsii-sotsialnykh-setej.html#google
					'class' => 'yii\authclient\clients\Google',
					'clientId' => '030206050960-j1104ouf26ppms1hhjt3r7vp6uhmpd4f.apps.googleusercontent.com',
					'clientSecret' => 'lXqa1TWy-_dpUx0537I2YTrO',
				],
				'facebook' => [
//получал тут https://developers.facebook.com/apps/-----------------/fb-login/settings/
//помощь тут https://www.facebook.com/help/community/question/?id=----------------
					'class' => 'yii\authclient\clients\Facebook',
					'clientId' => '000401896112832',
					'clientSecret' => '1f7a693e6f532b6046a1647ea4fadf20',
				],
				// и т.д.
			],
		],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'urlManager' => [
			'class'=> \common\components\MultiLanguage\MultiLangUrlManager::class,
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
				'' => 'site/index',
				'<action>'=>'site/<action>',
			],
		],
		'multiLanguage' => [
			'class' => \common\components\MultiLanguage\MultiLangComponent::class,
			'langs' => ['en','uk'],
			'default_lang' => 'en',   //Language to which no language settings are added.
			'lang_param_name' => 'lang',
		],
		'i18n' => [
			'translations' => [
				'app*' => [
					'class' => \yii\i18n\PhpMessageSource::class,
					'basePath' => '@frontend/translation', //папка сперводами
					'sourceLanguage' => 'en',
				],
			],
		],
	
    ],
    'params' => $params,
];
