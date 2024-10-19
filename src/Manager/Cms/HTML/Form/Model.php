<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 1:03 PM
 */

namespace Webarq\Manager\Cms\HTML\Form;


use DB;
use Wa;

class Model
{
    /**
     * @var array
     */
    protected $inputs = [];

    /**
     * @var
     */
    protected $master;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Master row id
     *
     * @var number
     */
    protected $id;

    public function __construct($id, array $inputs, $master)
    {
        $this->id = $id;
        $this->inputs = $inputs;
        $this->master = $master;

        $this->compile();
    }

    /**
     * @todo simplify the logic
     */
    protected function compile()
    {
        if ([] !== $this->inputs) {
            $pulls = array_pull($this->inputs, 'multilingual', []);
            $trans = [];

            foreach ($this->inputs as $name => $input) {
                $collections[$input->{'table'}->getName()][$name] = $input->{'column'}->getName();
                if (isset($pulls[$name]) && $input->{'table'}->isMultilingual()) {
                    $trans[\Wl::translateTableName($input->{'table'}->getName())][$name]
                            = $input->{'column'}->getName();
                    $trans['referenceKey'][\Wl::translateTableName($input->{'table'}->getName())] =
                            $input->{'table'}->getReferenceKeyName();
                }
            }

// Master table data
            $this->masterData(array_pull($collections, $this->master, []));
// Relational table data
            $this->relationalData($collections);
// Translation data
            $this->translationData($trans);
        }
    }

    protected function masterData(array $columns)
    {
        $row = $this
                ->rowFinder($this->master, Wa::table($this->master)->primaryColumn()->getName(), $columns)
                ->first();
        if (null !== $row) {
            foreach ($columns as $input => $column) {
                $this->data[$input] = $row->{$column};
            }
        }
    }

    protected function rowFinder($table, $whereColumn, array $columns)
    {
        if ([] !== $columns) {
            return DB::table($table)
                    ->select($columns)
                    ->where($whereColumn, $this->id)
                    ->get();
        }
    }

    /**
     * @param array $collections
     */
    protected function relationalData(array $collections)
    {
        if ([] !== $collections) {
            foreach ($collections as $table => $groups) {
                $get = $this->rowFinder($table, Wa::table($this->master)->getReferenceKeyName(), $groups);
                if ($get->count()) {
                    foreach ($groups as $name => $column) {
                        if (true === $this->inputs[$name]->attribute()->isMultiple()) {
                            foreach ($get as $item) {
                                $this->data[$name][] = $item->{$column};
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $collections
     * @todo Testing array input
     */
    protected function translationData(array $collections)
    {
        if ([] !== $collections) {
// References keys
            $referenceKeys = array_pull($collections, 'referenceKey');
// Lang column name (on translation table)
            $l = \Wl::getLangCodeColumn('name');
// Active languages
            $codes = \Wl::getCodes();

            foreach ($collections as $table => $groups) {
                foreach ($codes as $code) {
                    $s = DB::table($table)
                            ->select($groups + [$l, 'id'])
                            ->where($referenceKeys[$table], $this->id)
                            ->where($l, $code)
                            ->get();

//                    $s = $this->rowFinder($table, $referenceKeys[$table], $groups + [$l, 'id']);
                    $c = $s->count();
                    if (1 === $c) {
                        foreach ($s as $item) {
                            foreach ($groups as $input => $column) {
                                $this->data[$input . '_' . $item->{$l}] = $item->{$column};
                            }
                        }
                    } elseif ($c > 1) {
                        foreach ($s as $item) {
                            foreach ($groups as $input => $column) {
                                $this->data[$input . '_' . $item->{$l}][] = $item->{$column};
                            }
                        }
                    }
                }
            }
        }
    }

    public function getData()
    {
        return $this->data;
    }
}