<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/7/2016
 * Time: 12:34 PM
 */

namespace Webarq\Commands\Installer;


use Webarq\Info\ColumnInfo;

class DefinitionManager
{
    /**
     * @var ColumnInfo
     */
    protected $column;

    /**
     * @var string
     */
    protected $definition = '';

    /**
     * Mapping database type into laravel blueprint method and params (if any)
     *
     * @var array
     */
    protected $laravelEquivalent = [
            'bigint11' => 'bigIncrements:name',
            'bigint' => 'bigInteger:name, increment, unsigned',
            'char' => 'char:name, length',
            'decimal' => 'decimal:name, total, places',
            'double' => 'double:name, total, places',
            'enum:name, allowed',
            'float' => 'float:name, total, places',
            'int11' => 'increments:name',
            'int' => 'integer:name, increment, unsigned',
            'longtext' => 'longText:name',
            'mediumint11' => 'mediumIncrements:name',
            'mediumint' => 'mediumInteger:name, increment, unsigned',
            'mediumtext' => 'mediumText:name',
            'morphs' => 'morphs:name, indexName',
            'smallint11' => 'smallIncrements:name',
            'smallint' => 'smallInteger:name, increment, unsigned',
            'varchar' => 'string:name, length',
            'tinyint' => 'tinyInteger:name, increment, unsigned',
            'bigint01' => 'unsignedBigInteger:name, increment',
            'int01' => 'unsignedInteger:name, increment',
            'mediumint01' => 'unsignedMediumInteger:name, increment',
            'smallint01' => 'unsignedSmallInteger:name, increment',
            'tinyint01' => 'unsignedTinyInteger:name, increment',
    ];


    public function __construct(ColumnInfo $column)
    {
        $this->column = $column;
    }

    public function getDefinition()
    {
        $this->compile();

        return $this->definition . PHP_EOL;
    }

    private function compile()
    {
        $this->definition .= '            $table->';
        $this->compileType(strtolower($this->column->getType()));
        $this->compileDefaultValue($this->column->getDefault());
        $this->compileNullable($this->column->nullable());
        $this->compileComment($this->column->getComment());
        $this->compilePosition($this->column->getExtra('position'));
        $this->definition .= ';';
    }

    private function compileType($type)
    {
        $key = $type;
        if (str_contains($type, 'int')) {
            $key .= true === $this->column->getExtra('increment') ? 1 : 0;
            $key .= true === $this->column->getExtra('unsigned') ? 1 : 0;
        }
        $serial = explode(':',
                $this->column->getExtra('laravelEquivalent', array_get($this->laravelEquivalent, $key, $type)), 2);
        $this->definition .= $serial[0] . '(';
        if (!isset($serial[1])) {
            $serial[1] = 'name';
        }
        $this->compileParam(explode(',', $serial[1]));
        $this->definition .= ')';
    }

    private function compileParam(array $params)
    {
        foreach ($params as $i => $param) {
            $param = trim($param);
// Blank parameter
            if ('' === $param) {
                continue;
            }
            $param = $this->column->{$param};
            if (isset($param)) {
                if (is_bool($param)) {
                    $param = true === $param ? 'true' : 'false';
                } elseif (!is_numeric($param)) {
// String should be wrapping use "'"
                    $param = '\'' . $param . '\'';
                }
                $params[$i] = $param;
            } else {
                unset($params[$i]);
            }
        }
        $this->definition .= implode(', ', $params);
    }

    private function compileDefaultValue($value)
    {
        if (isset($value)) {
            $this->definition .= '->default(' . $value . ')';
        }
    }

    private function compileNullable($state)
    {
        if (true === $state) {
            $this->definition .= '->nullable()';
        }
    }

    private function compileComment($comment)
    {
        if (isset($comment)) {
            $this->definition .= '->comment(\'' . $comment . '\')';
        }
    }

    private function compilePosition($position)
    {
        if (null !== $position) {
            if (!str_contains($position, ':')) {
                $this->definition .= '->' . $position . '()';
            } else {
                list($method, $column) = explode(':', $position, 2);
                $this->definition .= '->' . $method . '(\''. $column. '\')';
            }
        }
    }

}