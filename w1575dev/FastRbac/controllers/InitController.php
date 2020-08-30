<?php

namespace w1575\FastRbac\controllers;

use w1575\FastRbac\models\Folder;
use w1575\FastRbac\models\Permission;
use yii\console\Controller;
use w1575\ConsoleColorBehavior;
use yii\rbac\Item;

/**
 * Default controller for the `w1575` module
 */
class InitController extends Controller
{
    /**
     * @return array|string[] подключаем поведение, которое я скепал на скорую руку
     */
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
        $console->line();
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

    /**
     *
     */
    public function actionPermissionsUp($entity = null)
    {
        $console = $this->c;
        $console->title("Инициализация разрешений" . ($entity === null ? "." : " для сущности {$entity} ") );
        $model = new Permission();
        $isSaved = $model
            ->loadData("{$this->module->rbacFolder}/permissions", $entity)
            ->savePermissions()
        ;
        if ($isSaved->hasErrors() === false) {
          $console->success("Разршения успешно добавлены в базу данных");
        } else {
            pred($isSaved->errors);
        }
    }

    public function actionClear()
    {
        $console = $this->c;
        $console->title("Полная очистка таблиц {{%auth_rule}}, {{%auth_item}}, {{%auth_assignment}}, {{%auth_item_child}}");
        $console->warning("Вы уверены, что хотите произвести данное действие? (Y/N)");
        $input = \readline();
        if (mb_strtoupper($input) === "Y") {
            \yii::$app->authManager->removeAll();
            $console->success("Все данные успешно удалены");
        }
    }

    /**
     * @return false
     */
    public function actionRolesUp()
    {
        $this->c->title("Добавление  ролей в базу данных");

        try {
            $rolesList = require "{$this->module->rbacFolder}//roles.php";
        } catch (\Exception $e) {
            $this->c->danger("При загрузки ролей из файла произошла ошибка: {$e->getMessage()}");
            return false;
        }


        if (empty($rolesList) === true) {
            $this->c->warning("Список ролей пуст");
            return false;
        }

        foreach ($rolesList as $name => $item) {
            $auth = \yii::$app->authManager;
            $item['name'] = $name;
            $role = $auth->createRole($name);
            $role->description = $item['description'];
            try {
                $auth->add($role);
            } catch (\Exception $e) {
                $this->c->danger("При добавлении роли {$name} произошла ошибка: {$e->getMessage()}");
                return false;
            }
        }

        $this->c->success("Все роли успешно добавлены в систему!");

    }


}
