<?php

namespace w1575\FastRbac\models;

use yii\helpers\FileHelper;

class Folder extends \yii\base\Model
{
    /**
     * @var string базовый путь к папке в которой будут хранится данный по rbac
     */
    public $basePath;

    public function createFolders()
    {
        $response = [];
        $response[] = $this->createFolder($this->basePath);
        $response[] = $this->createFolder("{$this->basePath}/roles");
        $response[] = $this->createFolder("{$this->basePath}/rules");
        $response[] = $this->createFolder("{$this->basePath}/permissions");
        return $response;

    }

    /**
     * Создает главную папку, в которой находятся все права.
     * @return string[] сообщение с его типом
     */
    public function createFolder($path)
    {
        try {
            FileHelper::createDirectory($path);
        } catch (\Exception $e) {
            return ['danger' => "Не удалось создать директорию {$path}. Возможно она уже сеществует"];
        }

        return ['success' => "Директория {$path} успешно создана"];
    }




}