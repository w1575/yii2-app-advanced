<?php


namespace w1575\FastRbac\controllers;


use yii\helpers\Inflector;

class MigrateController extends \yii\console\controllers\MigrateController
{

    public $migrationPath = '@console/rbac/migrations';

    // public $template = "@w1575/FastRbac/views/migrate/template.php";

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

        $migrateData = $this->renderFile("@w1575/FastRbac/views/migrate/migration.php", [
            'className' => $this->generateClassName('derp'),
            'safeUpData' => "die(1)",
            'safeDownData' => "die(0)",
        ]);

        // \Yii::getAlias("@w1575/FastRbac/views/migrate/migration.php")

        file_put_contents(\Yii::getAlias("@console/rbac/migrations/1.php"), $migrateData);
    }
}