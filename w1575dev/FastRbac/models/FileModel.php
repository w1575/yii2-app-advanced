<?php


namespace w1575\FastRbac\models;

use phpDocumentor\Reflection\Types\Object_;
use yii\helpers\FileHelper;

class FileModel extends \yii\base\Model
{
    /**
     * @var array массив с данными
     */
    public $data = [];

    /**
     * @var Object authManager
     */
    public $auth;

    /**e
     * Инициализация модели
     */
    public function init()
    {
        $this->auth = \Yii::$app->authManager;
    }


}