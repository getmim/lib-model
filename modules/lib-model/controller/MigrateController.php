<?php
/**
 * MigrateController
 * @package lib-model
 * @version 0.0.1
 */

namespace LibModel\Controller;

use Cli\Library\Bash;
use Mim\Library\Fs;
use LibModel\Library\Schema;

class MigrateController extends \Cli\Controller
{

    private function getMigrators(): ?array{
        $tables = getopt('', ['table::']);
        if(isset($tables['table'])){
            $tables = explode(',', $tables['table']);
            array_walk($tables, function(&$a){
                $a = trim($a);
            });
        }

        $result = Schema::collectSchema($tables);
        if(!$result)
            return null;

        $migrators = Schema::getMigrator($result);

        return $migrators;
    }

    public function testAction() {
        $migrators = $this->getMigrators();
        if(!$migrators){
            Bash::echo('No schema to compare');
            exit;
        }

        $result = [];
        $model_length = 0;
        $result_length = 0;

        $keys = [
            'tc' => ['table_create', 'Create the table of the model'],
            'fc' => ['field_create', 'Add new column to the table'],
            'fu' => ['field_update', 'Update exists column on the table'],
            'fd' => ['field_delete', 'Delete exists column from the table'],
            'ic' => ['index_create', 'Create new index for the table'],
            'iu' => ['index_update', 'Update exists index of the table'],
            'id' => ['index_delete', 'Delete exists index on the table'],
            'dc' => ['data_create', 'Insert new row to the table']
        ];

        $exists_diff = [];

        foreach($migrators as $model => $migrator){
            $res = $migrator->test();
            if(!$res)
                continue;
            
            $model_len = strlen($model);
            if($model_length < $model_len)
                $model_length = $model_len;

            $res_text = [];
            foreach($keys as $ix => $iv){
                if(isset($res[$iv[0]])){
                    $res_text[] = $ix;
                    if(!in_array($ix, $exists_diff))
                        $exists_diff[] = $ix;
                }
            }
            $result[$model] = implode(', ', $res_text);

            $result_len = strlen($result[$model]);
            if($result_length < $result_len)
                $result_length = $result_len;
        }

        if(!$result){
            Bash::echo('No different found between schema and database');
            exit;
        }

        $model_length+= 4;
        $result_length+= 4;

        Bash::echo('');
        Bash::echo(str_pad('MODEL', $model_length, ' ') . '| RESULT', 2);
        Bash::echo(str_pad('', $model_length, '-') . '|-' . str_pad('', $result_length, '-'), 2);

        foreach($result as $model => $res){
            Bash::echo(
                str_pad($model, $model_length, ' ')
                . '| '
                . $res
                , 2
            );
        }

        Bash::echo('');

        foreach($exists_diff as $ix)
            Bash::echo($ix . ' => ' . $keys[$ix][1], 3);

        Bash::echo('');
    }

    public function startAction() {
        $migrators = $this->getMigrators();
        if(!$migrators){
            Bash::echo('No schema to compare');
            exit;
        }

        $with_error = false;
        foreach($migrators as $model => $migrator){
            Bash::echo('Migrating model `' . $model . '`');
            if(!$migrator->start()){
                Bash::echo('Failed: ' . $migrator->lastError(), 3);
                $with_error = true;
            }else{
                Bash::echo('Success, continue...', 3);
            }
        }

        $msg = 'All models migrate already done';
        if($with_error)
            $msg.= ' with error';
        $msg.= '.';

        Bash::echo($msg);
    }

    public function schemaAction() {
        $target = $this->req->param->dirname;
        if(substr($target,0,1) != '/')
            $target = realpath(getcwd() . '/' . $target);
        if(!$target)
            Bash::error('Target dir not found');

        $migrators = $this->getMigrators();
        if(!$migrators){
            Bash::echo('No schema to compare');
            exit;
        }

        $with_error = false;
        foreach($migrators as $model => $migrator){
            $dbname = $model::getDBName();
            $target_file = $target . '/' . $dbname;

            Bash::echo('Generating migration file for model `' . $model . '`');
            if(!$migrator->schema($target_file)){
                Bash::echo('Failed: ' . $migrator->lastError(), 3);
                $with_error = true;
            }else{
                Bash::echo('Success, continue...', 3);
            }
        }

        $msg = 'All model migrate generator already done';
        if($with_error)
            $msg.= ' with error';
        $msg.= '.';

        Bash::echo($msg);
    }
}