<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/18/2017
 * Time: 1:34 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Illuminate\Support\Collection;
use Wa;
use Webarq\Info\ColumnInfo;
use Webarq\Model\NoModel;

class SelectTableInputManager extends SelectInputManager
{
    /**
     * Source config
     *
     * @var array
     */
    protected $sources = [];

    /**
     * @var
     */
    protected $trees;

    /**
     * Select option items
     *
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $attributeOptions = [];

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @return mixed
     */
    protected function buildInput()
    {
        if (true === $this->trees) {
            $this->trees = 'parent_id';
        }

        if (!is_numeric($this->limit)) {
            $this->limit = (int)$this->limit;
        }

        if (is_string($this->sources)) {
            list($model, $method) = explode(':', $this->sources, 2);
            $this->buildOptionFromModel($model, $method);
        } elseif (isset($this->sources['model'])) {
            $this->buildOptionFromModel(
                    $this->sources['model'],
                    $this->sources['method'],
                    (array)array_get($this->sources, 'params', [])
            );
        } else {
            $this->buildOptionsFromSource();
        }

        return parent::buildInput();
    }

    /**
     * Get options from defined model class
     * Options should have "model" and "method" member
     *
     * @param $class
     * @param $method
     * @param array $options
     */
    protected function buildOptionFromModel($class, $method, array $options = [])
    {
        $this->options += Wa::model($class)->{$method}(... $options);
    }

    /**
     * Build options from defined source options
     */
    protected function buildOptionsFromSource()
    {
        if (!is_callable($this->options) && [] !== $this->sources) {
            $options = $this->sources;

            if (null === ($table = array_pull($options, 'table'))) {
                $table = $this->table->getName();
            }

            if (null === ($column = array_pull($options, 'column'))) {
                $column = $this->table->primaryColumn()->getName();
            }

            if (!is_array($column)) {
                $label = $column;
            } elseif (count($column) === 2) {
                list($column, $label) = $column;
            } else {
                $this->options = ['Column sources config should be an array, and expect to have 2 member'];

                return [];
            }

            $columns = [$column, $label];

            if (null !== $this->trees) {
                $columns[] = $this->trees;
            }
            $options['columns'] = $columns;

            $instance = NoModel::instance($table);

            $get = $instance
                    ->optionQueryBuilder($options);

            if (null !== ($seq = array_get($options, 'sequence'))) {
                $instance->sequenceQueryBuilder($get, $seq);
            }

            if (false === array_get($this->sources, 'self')) {
                $get->where($columns[0], '<>', $this->rules->getRowId());
            }

            $get = $get->get();

            if (null === $this->trees) {
                if ($get->count()) {
                    foreach ($get as $item) {
                        if (!$this->isLimited($table, $item->{$column})) {
                            try {
                                $decode = html_entity_decode($item->{$label});
                            } catch (\Exception $e) {
                                $decode = $item->{$label};
                            }
                            $this->options[$item->{$column}] = strip_tags($decode);
                        }
                    }
                }
            } else {
                $this->makeOptionTrees($get, $column, $label, $this->trees);
            }
        }
    }

    protected function isLimited($table, $value)
    {
        if ($this->limit > 0) {
            $source = $this->table->getName();
            $column = $table === $source ? 'parent_id' : Wa::table($table)->getReferenceKeyName();

            $builder = \DB::table($source)
                    ->select($this->table->primaryColumn()->getName())
                    ->where($column, $value);

            if (isset($this->value) && Wa::table($source)->primaryColumn() instanceof ColumnInfo) {
                $builder->whereId(Wa::table($source)->primaryColumn()->getName(), '<>', $this->value);

            }

            return $builder->get()->count() >= $this->limit;
        }

        return false;
    }

    /**
     * @param Collection $collection
     * @param mixed $value Column name for option value
     * @param string $label Column name for option label
     * @param string $parent Column name which used for traversing
     */
    protected function makeOptionTrees(Collection $collection, $value, $label, $parent)
    {
        if ($collection->count()) {
            $options = [];

            foreach ($collection as $row) {
// When its coming to trees while in editing mode, each option items could not select theirs self or
// even their descendants as parent.
// Its something crazy to begin with
                if (isset($this->value) && Wa::abs($this->rules->getRowId()) === Wa::abs($row->{$value})) {
                    continue;
                }

                $options[$row->{$parent}][] = [$row->{$value}, $row->{$label}];
            }

            if ([] !== ($parents = array_pull($options, 0, []))) {
                foreach ($parents as $item) {
                    $this->options[$item[0]] = strip_tags(html_entity_decode($item[1]));

                    $this->getSubOption($options, $item[0], 1);
                }
            }
        }
    }

    /**
     * @param array $collections
     * @param $parent
     * @param int $level
     */
    protected function getSubOption(array $collections, $parent, $level = 1)
    {
        if ([] !== ($items = array_pull($collections, $parent, []))) {
            foreach ($items as $item) {
                $i = 1;
                while ($i) {
                    $item[1] = ' -- ' . strip_tags(html_entity_decode($item[1]));;
                    $i++;
                    if ($i > $level) {
                        break;
                    }
                }
                $this->options[$item[0]] = $item[1];

                $this->getSubOption($collections, $item[0], $level + 1);
            }
        }
    }
}