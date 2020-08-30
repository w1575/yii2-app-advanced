<?php


namespace backend\rbac\rules\article;


use yii\rbac\Rule;

class IsAuthorRule extends Rule
{
    public $name = "isArticleAuthor";

    public function execute($user, $item, $params)
    {
        if (!isset($params['Article'])) {
            return false;
        }

        return $params['Article']->creator_id === $user;
    }
}