<?php


namespace w1575\FastRbac\models;


use yii\helpers\{FileHelper};


class Permission extends FileModel
{
    /**
     * @param $path
     * @return mixed|string
     */
    private function getFileName($path)
    {
        $array = explode('/', $path);
        return end($array);
    }

    public $item;
    public $rule;


    /**
     * @param $path string путь к директории с разрешениями
     * @param null $entity
     */
    public function loadData(string $path, string $entity = null)
    {
        if ($entity === null) {
            try {
                $entitiesList = FileHelper::findFiles($path, ['only' => ['*.php']]);
            } catch (\Exception $e) {
                die("При получении списка сущностей произошла ошибка: {$e->getMessage()}" . PHP_EOL); // не красиво, но пока оставлю
            }

            foreach ($entitiesList as $index => $item) {
                $this->data[$this->getFileName($path)] = require $item;
            }
        } else {
            try {
                //$this->data[$entity] = require "{$path}/{$entity}.php";
                $entitiesList = FileHelper::findFiles($path, ['only' => ["{$entity}.php"]]);

                if ($entitiesList !== []) {
                    $this->data[$entity] = require "{$entitiesList[0]}";
                } else {
                    die("При получении данных сущности {$entity} произошла ошибка: не удалось найти файл."); // не красиво, но пока оставлю
                }
                // TODO: очень корявый вариант. нужно переделать
            } catch (\Exception $e) {
                die("При получении списка сущностей произошла ошибка: {$e->getMessage()}"); // не красиво, но пока оставлю
            }
        }

        return $this;

    }

    /***
     * Сохраняет все разрешения, которые удалось найти для сущности
     * @return $this
     * @throws \yii\db\Exception
     */
    public function savePermissions()
    {
        $transaction = \yii::$app->db->beginTransaction();
        foreach ($this->data as $index => $entity) {
            foreach ($entity as $name => $data) {
                $this->item = $data;
                $this->item['name'] = $name;
                $permission = $this->savePermission();
                if ($permission === false) {
                    $transaction->rollBack();
                    return $this;
                }

            }
        }
        if ($this->saveParents() === true and $this->saveChildren() === true) {
            $transaction->commit();
            return $this;
        }
        $transaction->rollBack();
        return $this;
    }

    /**
     * @return bool сохраняет родителей для
     */
    public function saveParents()
    {
        foreach ($this->data as $index => $entity) {
            foreach ($entity as $name => $data) {
                $permission = $this->auth->getPermission($name);
                $isSuccess = $this->addParents($permission);
                if ($isSuccess !== true) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return bool сохраняет связь правила-родителя с правилом-потомком
     */
    public function saveChildren()
    {
        foreach ($this->data as $index => $entity) {
            foreach ($entity as $name => $data) {
                $permission = $this->auth->getPermission($name);
                $isSuccess = $this->addChildren($permission);
                if ($isSuccess !== true) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Сохраняет данные одного разрешения
     */
    public function savePermission()
    {
        $data = $this->item;
        $permission = $this->auth->createPermission($data['name']);

        if (isset($data['ruleClass'])) {
            if (class_exists($data['ruleClass']) === false) {
                $this->addError('rule', "Ошибка при добавлении класса {$data['ruleClass']}. Класса не существует");
                return false;
            }
            try {
                $rule = new $data['ruleClass'];
            } catch (\Exception $e) {
                $this->addError('rule', "При создании экземпляра класса правила {$data['ruleClass']} произошла ошибка: {$e->getMessage()}");
                return false;
            }
            $isRuleAdded = $this->auth->getRule($rule->name);
            if ($isRuleAdded === null) {
                try {
                    $this->auth->add($rule);
                } catch (\Exception $e) {
                    $this->addError('rule', "При добавлении правила в базу данных {$rule->name} произошла ошибка: {$e->getMessage()}");
                    return false;
                }
            }
            $permission->ruleName = $rule->name;
        }

        $permission->description = $data['description'];

        try {
            $this->auth->add($permission);
        } catch (\Exception $e) {
            $this->addError('item', "При добавлении разрешения в базу данных {$permission->name} произошла ошибка: {$e->getMessage()}");
            return false;
        }

        return $permission;
    }

    /**
     * Добавляет потомков к данному разрешению
     * @return bool
     */
    public function addChildren($thisPermission)
    {
        if (isset($this->item['children']) === false or $this->item['children'] === []) {
            return true;
        }

        $children = $this->item['children'];

        foreach ($children as $index => $child) {
            $childPermission = $this->auth->getPermission($child);

            if ($childPermission === null) {
                $this->addError('item', "Невозможно добавить дочернее разрешение {$child}! Убедитесь, что оно находится в списке выше, чем правило {$thisPermission->name}");
                return false;
            }

            try {
                $this->auth->addChild($thisPermission, $childPermission);
            } catch (\Exception $e) {
                $this->addError('item', "Невозможно добавить дочернее разрешение {$child} для разрешения {$childPermission->name}: {$e->getMessage()}");
                return false;
            }
        }
        return true;
    }

    /**
     * Добавляет потомков к данному разрешению
     * @return bool
     */
    public function addParents($thisPermission)
    {
        if (isset($this->item['parents']) === false or $this->item['parents'] === []) {
            return true;
        }

        $parents = $this->item['parents'];

        foreach ($parents as $index => $parent) {
            $role = $this->auth->getRole($parent);

            if ($role === null) {
                $this->addError('item', "Невозможно добавить разрешение {$thisPermission->name} к роли {$parent}. Убедитесь, что роль добавлена в базу");
                return false;
            }

            try {
                $this->auth->addChild($role, $thisPermission);
            } catch (\Exception $e) {
                $this->addError('item', "Не удалось добавить разрешение {$thisPermission->name} к роли {$role->name}: {$e->getMessage()}");
                return false;
            }
        }
        return true;
    }

    /**
     * @param $entity string|null удаление разрешений из базы
     */
    public function delete()
    {
        $auth = $this->auth;

        $data=array_shift($this->data);
        // TODO: из-за корявого проектирования пришлось сделать так

        foreach ($data as $index => $datum) {

            $permission = $auth->getPermission($index);

            if ($permission !== null) {
                $auth->remove($permission);
            }
        }
        return true;
    }

}