<?php

namespace alexeevdv\utils\console;

use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\TableSchema;

class DatabaseController extends Controller
{
    public function actionCreateMigrations()
    {
        foreach (Yii::$app->db->schema->getTableNames() as $table) {
            $schema = Yii::$app->db->schema->getTableSchema($table);
            $this->createMigrationForTable($schema);
        }
    }

    private function createMigrationForTable(TableSchema $schema)
    {
        $code = '$this->createTable(\'' . $schema->name.  '\', [ ' . PHP_EOL;

        foreach ($schema->getColumnNames() as $columnName) {
            $column = $schema->getColumn($columnName);
            $code .= '    "' . $columnName . '" => $this';
            if ($column->isPrimaryKey) {
                $code .= '->primaryKey()';
            } else {
                $code .= '->' . $column->type . '(' . $column->size . ')';

                if ($column->allowNull) {
                    $code .= '->null()';
                } else {
                    $code .= '->notNull()';
                }

                if ($column->defaultValue !== null) {
                    if ($column->defaultValue instanceof Expression) {
                        $code .= '->defaultExpression(\'' . $column->defaultValue . '\')';
                    } else {
                        $code .= '->defaultValue(' . $column->defaultValue . ')';
                    }
                }
            }

            $code .= ',' . PHP_EOL;
        }
        $code .= '];' . PHP_EOL;

        echo $code;
    }
}
