<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/27/2017
 * Time: 10:36 AM
 */

namespace Webarq\Commands\Installer;


use Webarq\Info\TableInfo;

class AlterTable extends AbstractInstaller
{
    /**
     * DDL script
     *
     * @var
     */
    protected $ddl;

    /**
     * @var \Webarq\Info\TableInfo $table
     */
    protected $table;

    protected function installation(TableInfo $table)
    {
        if (null !== ($payload = array_get($this->payload, 'installed.' . $table->getName() . '.create'))
                && $payload !== $table->getSerialize()
        ) {
            $this->table = $table;

            $this->compareColumn($this->getColumnInfo($payload), $this->getColumnInfo($table->getSerialize()));
        }
    }

    protected function compareColumn(array $old, array $new)
    {
        $c1 = count($old);
        $c2 = count($new);
        if ($c1 > $c2) {
// There is column to be deleted
        } elseif ($c1 < $c2) {
// There is column to be added
            $cols = [];
            foreach ($new as $name => $options) {
                if (!isset($old[$name])) {
                    $info = $this->table->getColumn($name);

                    $info->setExtra('position', isset($prev) ? ('after:' . $prev) : 'first');

                    $cols[] = $info;
                }

                $prev = $name;
            }

            $this->migrationUp($cols);
        } else {
// There is column to be edited
        }
    }

    protected function migrationUp(array $columns)
    {
        foreach ($columns as $info) {
            $this->ddl .= (new DefinitionManager($info))->getDefinition();
        }
    }

    /**
     * Get table columns information
     *
     * @param $serial
     * @return array
     */
    protected function getColumnInfo($serial)
    {
        try {
            return unserialize($serial);
        } catch (\Exception $e) {
            return [];
        }
    }
}