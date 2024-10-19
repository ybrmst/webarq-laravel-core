<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/7/2017
 * Time: 12:53 PM
 */

namespace Webarq\Manager\Cms\Query;


use DB;
use Webarq\Info\ColumnInfo;
use Webarq\Info\TableInfo;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class SequenceManager
{
    /**
     * @var array
     */
    protected $inputs = [];

    /**
     * @var array
     */
    protected $sequenceInputs = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $remote = [];

    /**
     * @var number
     */
    protected $id;

    /**
     * @param array $inputs
     * @param array $values
     * @param number $id
     * @param array $remote
     */
    public function __construct(array $inputs, array $values, $id, array $remote = [])
    {
        $this->inputs = $inputs;
        $this->values = $values;
        $this->id = $id;
        $this->remote = $remote;

        $this->setSequenceInput();
    }

    /**
     * @return array
     */
    protected function setSequenceInput()
    {
// Inputs and values could not be empty
        if ([] !== $this->inputs && [] !== $this->values) {
            foreach ($this->inputs as $input => $manager) {
                if ($manager->column instanceof ColumnInfo && 'sequence' === $manager->column->getMaster()) {
                    $this->sequenceInputs[$input] = $manager;
                }
            }
        }

        return [];
    }

    /**
     *
     */
    public function execute()
    {
        if ([] !== $this->sequenceInputs && is_numeric($this->id)) {
            foreach ($this->sequenceInputs as $input => $manager) {
// Sequence grouping
                $grouping = $this->getAttributeGrouping($manager);
// Some existing data just update;
                if ([] !== $this->remote) {
                    $this->makeUpdate($input, $manager, (array)$grouping);
                } else {
// Mean a new row just inserted
                    $this->makeInsert(
                            $manager->table,
                            $manager->column->getName(),
                            $this->id,
                            (array)$grouping,
                            $manager->getValue());
                }
            }
        }
    }

    /**
     * @param AbstractInput $input
     * @return array
     */
    protected function getAttributeGrouping(AbstractInput $input)
    {
        $groups = $input->attribute()->get('grouping-column', []);

        if (!is_array($groups)) {
            try {
                return array_keys(json_decode($groups, true));

            } catch (\Excetion $e) {
                dd('Something wrong with your sequence configuration');
            }
        }

        return $groups;
    }

    /**
     * @param $input
     * @param AbstractInput $info
     * @param array $grouping
     * @return bool
     */
    protected function makeUpdate($input, AbstractInput $info, array $grouping = [])
    {
// Sequence column name
        $col = $info->column->getName();
// Get sequence grouping column
        $old = $this->valueToInt(array_get($this->remote, $input));
        $new = $this->valueToInt(
                array_get($this->values, $info->table->getName() . '.' . $input));

// Check for sequence grouping
        $groupingOld = $groupingNew = [];
        if ([] !== $grouping) {
            foreach ($grouping as $group) {
                $input = array_get($this->inputs, $group);
                if ($input instanceof AbstractInput) {
                    $groupingOld[$input->column->getName()] = $this->valueToInt(array_get($this->remote, $group));
                    $groupingNew[$input->column->getName()] = $this->valueToInt(array_get(
                            $this->values, $input->table->getName() . '.' . $input->column->getName()));
                }
            }
        }

// Master builder
        $master = DB::table($info->table->getName())
                ->where($info->table->primaryColumn()->getName(), '<>', $this->id);

        if ($groupingOld !== $groupingNew) {
// Same order different group
// Decrease by one leaving group where sequence bigger than old sequence
            $out = clone $master;
            foreach ($groupingOld as $column => $value) {
                $out->where($column, $value);
            }
            $out->where($col, '>', $old)->update([$col => DB::raw($col . ' - 1')]);

// Increase by one new group where sequence bigger than or equal to new sequence
            $in = clone $master;
            foreach ($groupingNew as $column => $value) {
                $in->where($column, $value);
            }

            $in->where($col, '>=', $new)->update([$col => DB::raw($col . ' + 1')]);
        } elseif ($old !== $new) {
// Change order same group
            if ($old < $new) {
// Eg. from 1 (old) to 4 (new), than we need to decrease sequence by one, when sequence bigger than old
// and smaller or equal to new
                $raw = DB::raw($col . ' - 1');
                $builder = clone $master;
                $builder->where($col, '>', $old)->where($col, '<=', $new);
            } else {
// Eg. from 4 (old) to 1 (new), than we need to increase sequence by one, when sequence smaller than old
// and bigger than or equal to new
                $raw = DB::raw($col . ' + 1');
                $builder = clone $master;
                $builder->where($col, '<', $old)->where($col, '>=', $new);
            }

            if ([] !== $groupingOld) {
                foreach ($groupingOld as $column => $value) {
                    $builder->where($column, $value);
                }
            }

            $builder->update([$col => $raw]);
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function valueToInt($value)
    {
        if (is_numeric($value) && floor($value) == $value) {
            return (int)$value;
        } elseif (is_string($value)) {
            return strtolower($value);
        }

        return $value;
    }

    /**
     * @param TableInfo $table
     * @param string $column Sequence column mae
     * @param number $id Last insert id
     * @param array $groups
     * @param number $sequence Sequence value
     * @return mixed
     */
    protected function makeInsert(TableInfo $table, $column, $id, array $groups, $sequence)
    {
        $builder = DB::table($table->getName())
                ->where($table->primaryColumn()->getName(), '<>', $id)
                ->where($column, '>=', $sequence);

        if ([] !== $groups) {
            foreach ($groups as $group) {
                if (null !== ($input = array_get($this->inputs, $group))) {
                    $builder->where($input->column->getName(),
                            array_get($this->values, $input->table->getName() . '.' . $input->column->getName()));
                }
            }
        }

        return $builder->update([$column => DB::raw($column . ' + 1')]);
    }
}