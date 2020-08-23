<?php


namespace w1575\FastRbac\models;


use yii\helpers\FileHelper;

class Permission extends FileModel
{
    /**
     * @var string базовый путь к директории с разрешениями
     */
    public $basePath;


}