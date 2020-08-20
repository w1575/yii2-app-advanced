<?php

namespace w1575\FastRbac\controllers;

use yii\console\Controller;
use w1575\ConsoleColorBehavior;

/**
 * Default controller for the `w1575` module
 */
class RoleController extends Controller
{

    public function behaviors()
    {
        return [
            'consoleColor' => ConsoleColorBehavior::class,
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->c->title("w!");
    }

    public function actionUp()
    {
        $this->c->title("Поднимаем роли");
    }
}
