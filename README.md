# yii2-my-app-advanced
Модифицированный шаблон yii2-app-advanced
отличаеться от стандартной тем
1. модульная структура backend и frontend  
```
    /modules/user/controllers/LoginController.php
    /modules/user/models/login/LoginForm.php
    /modules/user/views/login/login.php
```
    настройка в конфиге
```php
    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\Module',
        ],
    ],
 ```
 перенесен код авторизации и аутофикации в /modules/user/
 
 2. добавлен компонент аутофикации через социальные сети google, facebook и пр.
 в composer.json добавить
   ```
   "yiisoft/yii2-authclient": "*"
   ```
   и обновить: composer update
миграция
     ```
   migrations/m220608_193819_auth.php
     ``` 
в конфиге
 ```php
 		'authClientCollection' => [
 			'class' => 'yii\authclient\Collection',
 			'clients' => [
 				'google' => [
 					'class' => 'yii\authclient\clients\Google',
 					'clientId' => '030206050960-j1104ouf26ppms1hhjt3r7vp6uhmpd4f.apps.googleusercontent.com',
 					'clientSecret' => 'lXqa1TWy-_dpUx0537I2YTrO',
 				],
 				'facebook' => [
 					'class' => 'yii\authclient\clients\Facebook',
 					'clientId' => '000401896112832',
 					'clientSecret' => '1f7a693e6f532b6046a1647ea4fadf20',
 				],
 			],
 		],
  ```
 3.настроена многоязычность через Url /uk/about.html
   за это отвечает серия компонентов и виджет выбора языка , лежат тут:
 ```
    common/components/MultiLanguage
 ```
 настроен показан как делать перевод через i18n
 ```
    frontend/translation/uk/app.php
 ```
 + виджет выбора языка
 
 4. Гибридный RBAC
  ```
     common/rbac/HybridAuthManager.php
  ```
 у одного пользователя может быть одна роль и она указываеться в базе 
 посавить миграцию
   ```
migrations/m220602_134010_add_role_column_to_user_table.php
  ```
проинициализировать RBAC через консоль
   ```
php yii rbac/init
   ```
 дктальнее об RBAC (https://github.com/mardoc/rbac)
  