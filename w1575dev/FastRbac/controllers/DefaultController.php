<?php

namespace w1575\FastRbac\controllers;

use w1575\FastRbac\models\Folder;
use yii\console\Controller;
use w1575\ConsoleColorBehavior;

/**
 * Default controller for the `w1575` module
 */
class DefaultController extends Controller
{

    public function behaviors()
    {
        return [
            'consoleColor' => ConsoleColorBehavior::class,
        ];
    }

    /**
     * Подготовка папки в которой будут хранится данные по RBAC
     */
    public function actionPrepareFolder()
    {
        $console = $this->c;
        $console->title("Подготовка дириктории с данными RBAC");
        $path = $this->module->rbacFolder;
        $console->info("Необходимые папки будут созданы по адресу {$path}");
        $model = new Folder();
        $model->basePath = $path;
        $res = $model->createFolders();

        foreach ($res as $key => $oneMessage) {
            foreach ($oneMessage as $type => $text) {
                $console->{$type}($text);
            }
        }

        $console->line();


    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $console = $this->c;

        $console->title("Вас приветствует мастер инициализации RBAC для yii2! Инициализацию чего хотите провести: ");
        $console->info("0. Всего");
        $console->info("1. Ролей;");
        $console->info("2. Правил;");
        $console->info("3. Разрешений;");
        $console->success("Введите цифру (для выхода нажмите другой символ): ");
        $a = readline();

        switch ($a) {
            case 0 :
                $console->warning("Производится инициализация Всего");
                break;
            case 1 :
                $console->warning("Производится инициализация Ролей");
                break;
            case 2 :
                $console->warning("Производится инициализация Правил");
                break;
            case 3 :
                $console->warning("Производится инициализация Разрешений");
                break;
            default:
                $console->danger("Выход");
        }



    }

    public function actionUp()
    {

    }
}
