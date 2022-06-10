<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
		'authManager' => [
			'class' => 'common\rbac\HybridAuthManager',
			'itemFile' => '@common/rbac/data/items.php',
			'assignmentFile' => '@common/rbac/data/assignments.php',
			'ruleFile' => '@common/rbac/data/rules.php'
		],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
