<?php

namespace w1575\FastRbac;

use Yii;

/**
 * w1575 module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'w1575\FastRbac\controllers';

    /**
     * @var путь к директории, в которой хранятся данные с доступами
     */
    public $rbacFolder;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'w1575\FastRbac\controllers';
        }
        // custom initialization code goes here
    }
}
