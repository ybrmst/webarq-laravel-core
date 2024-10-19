<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/16/2016
 * Time: 1:09 PM
 */

namespace Webarq\Commands\Installer;


class UniqueDefinitionManager
{

    public function getDefinitionUnique(array $name)
    {
        if ([] !== $name) {
            $definition = PHP_EOL;
            foreach ($name as $column) {
                $definition .= '            $table->unique(\''
                        . $column . '\', \'' . $this->identifierName($column) . '\');'
                        . PHP_EOL;
            }
            return $definition;
        }
    }

    private function identifierName($key, $limit = 40)
    {
        if (strlen($key) > $limit) {
            //Remove vowels
            $key = str_replace(['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'], '', $key);
        }

        return $key;
    }

    public function getDefinitionUniques(array $name)
    {
        if ([] !== $name) {
            return PHP_EOL
            . '            $table->unique([\''
            . implode('\', \'', $name) . '\'], \'' . $this->identifierName(implode('-', $name)) . '\');'
            . PHP_EOL;
        }
    }

}