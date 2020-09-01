<?php

use backend\rbac\rules\article\IsAuthorRule;

// это файл-пример, можно называть из исходя из имени котроллера
// можно будет переинициализировать данные для каждого из этих файлов, не трогая все остальные
// ВНИМАНИЕ! Перед инициализацией доступов необходимо, чтобы была произведена инициализация ролей
// ЗАМЕЧАНИЕ! По моей логике parents это всегда роль, к которой относится правило, в children всегда указывается имя
// дочернего правила

return [
    'newsUpdate' => [ // будет использована как имя
        // 'type' => 2, // можно не указывать, ибо по моей логике роли это немного другая сущность,
        // поэтому будет использоваться yii\rbac\Item::TYPE_PERMISSION
        // но если Вы вдруг захотите определять роли здесь, то я Вас не виню (:
        'description' => 'Новость. Обновление.',
        'parents' => ['admin', 'chiefEditor'], // роли, к которым относится правило
        'updated_at' => '123232' // можно не указывать, будет использовано значение из php функции time()
    ],
    'newsUpdateWhereAuthor' => [   // обновление статьи, в которой пользователь явялется автором
        'description' => 'Новость. Обновление своих.',
        'ruleClass' => IsAuthorRule::class, // класс правила, который применяется к данному разрешению
        'children' => ['articleUpdate'], // правило-потомок ну думаю тут все должно быть и так понятно исходя из
        // статьи https://www.yiiframework.com/doc/guide/2.0/en/security-authorization
        'parents' => ['editor'],
    ],

];


