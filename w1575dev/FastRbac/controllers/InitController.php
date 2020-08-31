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

                'consoleColor' => [
                    'class' => ConsoleColorBehavior::class,
                    'theme' => 'invert',
                ],
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
        // TODO: слишком раздутый метод. мне не нравится
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

        $auth = \yii::$app->authManager;

        $transaction = \yii::$app->db->beginTransaction();

        foreach ($rolesList as $name => $item) {
            $item['name'] = $name;
            $role = $auth->createRole($name);
            $role->description = $item['description'];
            try {
                $auth->add($role);
            } catch (\Exception $e) {
                $this->c->danger("При добавлении роли {$name} произошла ошибка: {$e->getMessage()}");
                $transaction->rollBack();
                return false;
            }
        }

        foreach ($rolesList as $name => $item) {
            if (isset($item['children'])) {
                $parent = $auth->getRole($name);
                foreach ($item['children'] as $index => $child) {
                    $child = $auth->getRole($child);
                    try {
                        $auth->addChild($parent, $child);
                    } catch (\Exception $e) {
                        $this->c->danger("При добавление к роли {$name} дочернего правила {$child->name} произошла ошибка: {$e->getMessage()}");
                        $transaction->rollBack();
                        return false;
                    }
                }
            }
        }

        // TODO: по факту дубликат кода. нужно изменить

        foreach ($rolesList as $name => $item) {
            if (isset($item['parents'])) {
                $child = $auth->getRole($name);
                foreach ($item['parents'] as $index => $parent) {
                    $parent = $auth->getRole($parent);
                    try {
                        $auth->addChild($parent, $child);
                    } catch (\Exception $e) {
                        $this->c->danger("При добавление к роли {$name} дочернего правила {$child->name} произошла ошибка: {$e->getMessage()}");
                        $transaction->rollBack();
                        return false;
                    }
                }
            }
        }
        try {
            $transaction->commit();
        } catch (\Exception $e) {
            $this->c->danger("При добавлении данных в базу произошла ошибка: {$e->getMessage()}");
        }

        $this->c->success("Все роли успешно добавлены в систему!");

    }

    /**
     * Удялет все роли из базы данных
     */
    public function actionRolesDown()
    {
        $this->c->title("Удаление ролей из системы");
        $auth = \yii::$app->authManager;
        $this->c->warning("Вы действительно хотите продолжить? (Y)");
        $input = \readline();
        if (mb_strtoupper($input) === "Y") {
            $auth->removeAllRoles();
            $this->c->success("Все данные успешно удалены");
            return true;
        }
        $this->c->info("Удаление ролей прервано.");
    }

    /**
     * @param $entity
     */
    public function actionPermissionsDown($entity = null)
    {
        $title = "Удаление разрешений ";
        if ($entity !== null) {
            $title .= "сущности {$entity}";
        }

        $this->c->title($title);

        if ($this->confirmAction() === true) {
            if ($entity === null) {
                $auth = \yii::$app->authManager;
                $auth->removeAllPermissions();
                $this->c->success("Все разрешения успешно удалены.");
                return true;
            } else {
                $permission = new Permission();
                $result = $permission
                    ->loadData($this->module->rbacFolder, $entity)
                    ->delete();
                if ($result === true) {
                    $this->c->success("Разрешения сущности {$entity} успешно удалены");
                }
            }
        }
    }

    /**
     * @return bool подтверждение действия пользователем
     */
    private function confirmAction()
    {
        $this->c->warning("Вы действительно хотите продолжить? Y/N");
        $input = \readline();
        if (mb_strtoupper($input) === "Y") {
            return true;
        }
        return false;
    }


}
