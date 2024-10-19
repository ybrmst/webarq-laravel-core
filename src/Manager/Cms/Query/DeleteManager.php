<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/1/2017
 * Time: 4:30 PM
 */

namespace Webarq\Manager\Cms\Query;


use DB;
use Wa;
use Webarq\Manager\SetPropertyManagerTrait;
use Webarq\Model\NoModel;

class DeleteManager
{
    use SetPropertyManagerTrait;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var
     */
    protected $mimeColumn;

    /**
     * @var
     */
    protected $sequenceColumn;

    /**
     * @var array
     */
    protected $row = [];

    /**
     * Column name for where statement
     *
     * @var string
     */
    protected $where;

    /**
     * @param string $table
     * @param array $options
     * @param number|array $id
     * @param string $where
     */
    public function __construct($table, array $options, $id = [], $where = 'id')
    {
        $this->where = $where;
        $this->table = $table;

        if (is_array($id)) {
            $this->row = $id;
        } else {
            $this->row = NoModel::instance($table, $where)
                    ->where($where, $id)
                    ->get()
                    ->toArray();
        }

        $this->setPropertyFromOptions($options);
    }


    public function delete($history = true)
    {
        if ([] !== $this->row) {
            foreach ($this->row as $row) {
// Delete primary column
                $builder = NoModel::instance($this->table, $this->where)
                        ->where($this->where, array_get($row, $this->where))
                        ->delete();

                if ($builder) {
// Update sequence
                    $this->sequenceUpdate($row);
// Delete mime files
                    $this->deleteMimes($row);
// Delete translation
                    $this->deleteTranslation($row);
// History record
                    if (true === $history) {
                        Wa::instance('manager.cms.history')
                                ->record(\Auth::user(), 'delete', Wa::table($this->table), $row);
                    }

                    return $builder;
                }
            }
        }

        return false;
    }

    /**
     * Fixing sequence
     *
     * @param array $row
     */
    protected function sequenceUpdate(array $row)
    {
        if (null !== $this->sequenceColumn) {
            $sequence = !is_array($this->sequenceColumn) ? explode(':', $this->sequenceColumn) : $this->sequenceColumn;
            $builder = NoModel::instance($this->table, $this->where)
                    ->where($sequence[0], '>', array_get($row, $sequence[0]));

            if (isset($sequence[1])) {
                $columns = !is_array($sequence[1]) ? explode(',', $sequence[1]) : $sequence[1];
                foreach ($columns as $column) {
                    if (null !== ($value = array_get($row, $column))) {
                        $builder->where($column, $value);
                    }
                }
            }

            $builder->update([$sequence[0] => DB::raw($sequence[0] . ' - 1')]);
        }
    }

    /**
     * Delete files from disk
     *
     * @param array $row
     */
    protected function deleteMimes(array $row)
    {
        if (null !== $this->mimeColumn) {
            foreach ((array)$this->mimeColumn as $column) {
                if (is_file($file = array_get($row, $column))) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Delete translation table
     *
     * @param array $row
     */
    protected function deleteTranslation(array $row)
    {
        if (null !== Wa::table($this->table) && Wa::table($this->table)->isMultilingual()) {

            $del = NoModel::instance(\Wl::translateTableName($this->table), $this->where)
                    ->where(Wa::table($this->table)->getReferenceKeyName(), array_get($row, $this->where));

            $rows = clone $del;
            $rows = $rows->get()->toArray();

            if ([] !== $rows && $del->delete()) {
                foreach ($rows as $row) {
                    $this->deleteMimes($row);
                }
            }
        }
    }
}