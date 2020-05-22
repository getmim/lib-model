<?php
/**
 * Schema
 * @package lib-model
 * @version 0.0.1
 */

namespace LibModel\Library;

use Mim\Library\Fs;
use StableSort\StableSort;

class Schema
{

    static function collectSchema(array $tables=[]): array {
        $result = [];

        $modules = Fs::scan(BASEPATH . '/modules');
        foreach($modules as $module){
            $module_migrate_file = BASEPATH . '/modules/' . $module . '/migrate.php';
            if(!is_file($module_migrate_file))
                continue;
            $module_migrate = include $module_migrate_file;
            $result = array_replace_recursive($result, $module_migrate);
        }

        // app migrate?
        $app_migrate_file = BASEPATH . '/etc/migrate.php';
        if(is_file($app_migrate_file)){
            $app_migrate = include $app_migrate_file;
            $result = array_replace_recursive($result, $app_migrate);
        }

        $filtered_result = [];

        // sort the fields
        foreach($result as $model => &$conf){
            if(!isset($conf['fields']))
                continue;
            foreach($conf['fields'] as $name => &$field)
                $field['name'] = $name;
            unset($field);

            StableSort::uasort($conf['fields'], function($a, $b){
                return ( $a['index'] ?? 100 ) - ( $b['index'] ?? 100 );
            });
        }
        unset($conf);

        if(!$tables)
            return $result;
        foreach($result as $model => $opts){
            if(!class_exists($model))
                continue;
            $table = $model::getTable();
            if(in_array($table, $tables))
                $filtered_result[$model] = $opts;
        }

        return $filtered_result;
    }

    static function getMigrator(array $models): array{
        $result = [];
        $migrators = \Mim::$app->config->libModel->migrators;
        foreach($models as $model => $data){
            if(!class_exists($model))
                continue;
            $driver = $model::getDriver();
            if(!isset($migrators->$driver))
                Bash::error('Migrator for driver `' . $driver . '` not found');
            $migrate_class = $migrators->$driver;
            $result[$model] = new $migrate_class($model, $data);
        }

        return $result;
    }
}