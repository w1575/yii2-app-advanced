<?php
return [
    'admin' => [
        'description' => 'Администратор',
        'created_at' => time(),
        'updated_at' => time()
    ],
    'editor' => [
        'description' => 'Автор статей',
        'created_at' => time(),
        'updated_at' => time(),
        'parents' => ['admin'],
    ],
    'chiefEditor' => [
        'description' => 'Шеф-редактор',
        'created_at' => time(),
        'updated_at' => time(),
        'children' => ['editor'],
    ]
];