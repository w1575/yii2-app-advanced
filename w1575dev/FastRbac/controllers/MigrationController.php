<?php


namespace w1575\FastRbac\controllers;


use yii\helpers\Inflector;

class MigrationController extends \yii\console\controllers\MigrateController
{

    /***
     * Самый грязный способ - стащил из наследуемого класса :(
     * @param string $name
     * @return array
     */
    private function generateClassName($name)
    {
        $namespace = null;
        $name = trim($name, '\\');
        if (strpos($name, '\\') !== false) {
            $namespace = substr($name, 0, strrpos($name, '\\'));
            $name = substr($name, strrpos($name, '\\') + 1);
        } elseif ($this->migrationPath === null) {
            $migrationNamespaces = $this->migrationNamespaces;
            $namespace = array_shift($migrationNamespaces);
        }

        if ($namespace === null) {
            $class = 'm' . gmdate('ymd_His') . '_' . $name;
        } else {
            $class = 'M' . gmdate('ymdHis') . Inflector::camelize($name);
        }

        return [$namespace, $class];
    }

    public function actionGenerate($name = 'derp')
    {
        $className = $this->generateClassName($name);
    }
}