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
                $this->data[$entity] = require "{$path}/{$entity}.php";
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
                $isSuccess = $this->savePermission();
                if ($isSuccess !== true) {
                    $transaction->rollBack();
                    return $this;
                }
            }
        }
        $transaction->commit();
        return $this;
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

        return true;

    }
}