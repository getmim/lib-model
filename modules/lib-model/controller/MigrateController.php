<?php
/**
 * MigrateController
 * @package lib-model
 * @version 1.0.0
 */

namespace LibModel\Controller;

use Cli\Library\Bash;
use Mim\Library\Fs;
use LibModel\Library\Schema;

class MigrateController extends \Cli\Controller
{

    protected function getMigrators(): ?array
    {
        $tables = getopt('', ['table::']);
        if (isset($tables['table'])) {
            $tables = explode(',', $tables['table']);
            array_walk($tables, function (&$a) {
                $a = trim($a);
            });
        }

        $c_excludes = \Mim::$app->config->libModel->migrate->ignore->connections ?? [];

        $result = Schema::collectSchema($tables, $c_excludes);
        if (!$result) {
            return null;
        }

        $migrators = Schema::getMigrator($result);

        return $migrators;
    }

    public function dbAction()
    {
        $migrators = $this->getMigrators();
        if (!$migrators) {
            Bash::echo('No schema to compare');
            exit;
        }

        $with_error = false;

        $migrated = [];
        $connections = \Mim::$app->config->libModel->connections;
        $types = ['read','write'];

        foreach ($migrators as $model => $migrator) {
            Bash::echo('Checking database for model `' . $model . '`');

            foreach ($types as $type) {
                Bash::echo('Checking for `' . $type . '` connections', 3);

                $conn_name = $model::getConnectionName($type);
                if (!isset($connections->$conn_name)) {
                    Bash::error('No connection named `' . $conn_name . '` found');
                }

                if (in_array($conn_name, $migrated)) {
                    Bash::echo('Success, continue...', 6);
                } else {
                    if (!$migrator->db($connections->$conn_name->configs)) {
                        Bash::echo('Failed: ' . $migrator->lastError(), 6);
                        $with_error = true;
                    } else {
                        Bash::echo('Success, continue...', 6);
                        $migrated[] = $conn_name;
                    }
                }
            }
        }

        $msg = 'All models migrate already done';
        if ($with_error) {
            $msg.= ' with error';
        }
        $msg.= '.';

        Bash::echo($msg);
    }

    public function schemaAction()
    {
        $migrators = $this->getMigrators();
        if (!$migrators) {
            Bash::echo('No schema to compare');
            exit;
        }

        $with_error = false;
        foreach ($migrators as $model => $migrator) {
            $shards = $migrator->getShards();
            if (!$shards) {
                $shards = [$model::getTable()];
            }

            foreach ($shards as $shard) {
                if (!$migrator->schema($shard)) {
                    Bash::echo('Failed: ' . $migrator->lastError(), 3);
                    $with_error = true;
                }
            }
        }
    }

    public function startAction()
    {
        $migrators = $this->getMigrators();
        if (!$migrators) {
            Bash::echo('No schema to compare');
            exit;
        }

        $with_error = false;
        foreach ($migrators as $model => $migrator) {
            $shards = $migrator->getShards();
            if (!$shards) {
                $shards = [$model::getTable()];
            }

            foreach ($shards as $shard) {
                if (!$migrator->start($shard)) {
                    Bash::echo($model . ':' . $shard);
                    Bash::echo('Error: ' . $migrator->lastError(), 3);
                    $with_error = true;
                }
            }
        }

        $msg = 'All models migrate already done';
        if ($with_error) {
            $msg.= ' with error';
        }
        $msg.= '.';

        Bash::echo($msg);
    }

    public function testAction()
    {
        $migrators = $this->getMigrators();
        if (!$migrators) {
            Bash::echo('No schema to compare');
            exit;
        }

        $result = [];

        foreach ($migrators as $model => $migrator) {
            $shards = $migrator->getShards();
            if (!$shards) {
                $shards = [$model::getTable()];
            }

            foreach ($shards as $index => $shard) {
                $res = $migrator->test($shard);
                if (!$res) {
                    continue;
                }

                $model_conn = $model::getConnectionName();
                $model_table = $model::getTable();

                foreach ($res as $act => $cols) {
                    foreach ($cols as $col) {
                        $row = ['',''];
                        if (!$index) {
                            $row = [
                                $model_conn,
                                $model
                            ];
                        }

                        $row[] = $model_table;
                        $row[] = $col['name'];
                        $row[] = $act;

                        $result[] = $row;
                    }
                }
            }
        }

        if (!$result) {
            Bash::echo('No different found between schema and database');
            exit;
        }

        $headers = [
            'CONNECTION',
            'MODEL',
            'TABLE',
            'COLUMN',
            'ACTION'
        ];

        Bash::table($result, $headers);
    }
}
