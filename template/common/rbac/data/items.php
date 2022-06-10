<?php

return [
    'permAdminPanel' => [
        'type' => 2,
        'description' => 'Админ панель',
    ],
    'user' => [
        'type' => 1,
        'description' => 'Пользователь',
    ],
    'permModifyUserName' => [
        'type' => 2,
        'description' => 'Доступ Админа к редактированию пользоватлей',
    ],
    'moder' => [
        'type' => 1,
        'description' => 'Модератор',
        'children' => [
            'user',
            'permAdminPanel',
        ],
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Администратор',
        'children' => [
            'moder',
            'permModifyUserName',
        ],
    ],
];
