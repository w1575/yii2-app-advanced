<?php

/**
 * Представление-обертка под генерируемый код
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */

echo "<?php " . PHP_EOL ?>
<?php if (empty($namespace) === false): ?>
<?= PHP_EOL ?>
namespace <?= $namespace ?> ;
<?php endif; ?>


class <?= $className[1] ?> extends \yii\db\Migration
{

    private $auth;

    public function init()
    {
        $this->auth = \yii::$app->authManager;
    }

    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        <?= $safeUpData ?>
    }

    /**
    * {@inheritdoc}
    */
    public function safeDown()
    {
        <?= $safeDownData ?>
    }

}
