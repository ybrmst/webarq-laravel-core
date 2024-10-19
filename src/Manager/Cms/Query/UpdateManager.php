<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/25/2017
 * Time: 12:19 PM
 */

namespace Webarq\Manager\Cms\Query;


use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Info\TableInfo;

class UpdateManager extends QueryManager
{
    /**
     * @var string
     */
    protected $formType = 'edit';

    /**
     * @var
     */
    protected $id;

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function execute()
    {
        $count = 0;

        if (isset($this->model)) {
            $count = $this->model->formUpdate($this->id, $this->post);
        } elseif ([] !== $this->post && !is_null($this->master) && is_numeric($this->id)) {
// Master data should be inserted before another
            $row = array_pull($this->post, $this->master, []);

            if ([] !== $row) {
                $m = Wa::table($this->master);
// Total updating row
                $count = 0;
// Initiate model
                $model = $this->initiateModel($this->master);
// Update master
                $update = $this->update($model, $row, [$m->primaryColumn()->getName() => $this->id]);
                if (true === $update || (is_numeric($update) && $update > 0)) {
                    if (null !== $m->getUpdateTimeColumn()) {
                        $this->addUpdateTime($m, $row);
                        $this->update($model, $row, [$m->primaryColumn()->getName() => $this->id]);
                    }
                    $count += 1;
                }

// Translation
                $tr = array_pull($this->post, 'translation', []);
                $update = $this->translation($this->id, $m, $tr);
                if (true === $update) {
                    $count += 1;
                }
// Support rows
                $update = $this->supportData($this->id, $m, $this->post);
                if (true === $update || (is_numeric($update) && $update > 0)) {
                    $count += 1;
                }

                if ($count) {
                    Wa::manager('cms.history')->record(\Auth::user(), 'update', $m, $row);
                }
            }
        }

        return $count;
    }

    protected function update(Model $model, array $row, array $where)
    {
        $model = $this->buildWhere($model, $where);

        return $model->update($row);
    }

    /**
     * @param $id
     * @param TableInfo $table
     * @param array $rows
     * @return true|null
     */
    protected function translation($id, TableInfo $table, array $rows = [])
    {
        if ($table->isMultiLingual() && [] !== $rows) {
// Table name translation
            $t = \Wl::translateTableName($table->getName());
            $rows = array_get($rows, $t, []);

            if ([] !== $rows) {
                $updated = null;
                foreach ($rows as $code => $row) {
// Check for translation row
                    $find = DB::table($t)
                            ->where(\Wl::getLangCodeColumn('name'), $code)
                            ->where($table->getReferenceKeyName(), $id)
                            ->get();

                    $model = $this->initiateModel($t, 'id');
                    if ($find->count()) {
                        $affect = $this->update($model, $row, [
                                        \Wl::getLangCodeColumn('name') => $code,
                                        $table->getReferenceKeyName() => $id]
                        );
                        if ((is_numeric($affect) && $affect > 0) || true === $affect) {
                            $updated = true;
                        }
                    } else {
// Translation row completion
                        $row += [
                                'create_on' => date('Y-m-d H:i:s'),
                                \Wl::getLangCodeColumn('name') => $code,
                                $table->getReferenceKeyName() => $id
                        ];

                        $this->rowBinder($model, $row);
                        $model->save();

                        if (!empty($model->id)) {
                            $updated = true;
                        }
                    }
                }

                return $updated;
            }
        }
    }

    /**
     * @param $id
     * @param TableInfo $master
     * @param array $groups
     * @return number
     */
    protected function supportData($id, TableInfo $master, array $groups)
    {
        $count = 0;
        if ([] !== $groups) {
            foreach ($groups as $table => $rows) {
                $t = Wa::table($table);
                if (Arr::isAssoc($rows)) {
                    $rows = [$rows];
                }

                if ($t->isFlushUpdate()) {
                    if (\DB::table($table)->where($master->getReferenceKeyName(), $this->id)->delete()) {
                        $count += 1;
                    }
                }

                foreach ($rows as $row) {
// Default value
                    $where = [];
                    $find = null;
// Initiate model
                    $model = $this->initiateModel($table);
                    if (!$t->isFlushUpdate()) {
                        $find = DB::table($table);

                        foreach (['getUniquesColumns', 'getUniqueColumns'] as $method) {
                            if ([] !== ($uniques = Wa::table($table)->{$method}())) {
                                foreach ($uniques as $unique) {
                                    $val = $unique === $master->getReferenceKeyName()
                                            ? $this->id
                                            : array_get($row, $unique);
// Push where column for later updates
                                    $where[$unique] = $val;
// Query where
                                    $find->where($unique, $val);
// For deleting support row purpose
                                    if ($unique !== $master->getReferenceKeyName()) {
                                        $notInDelete[$table][$unique][] = $val;
                                    }
                                }
                            }
                        }

                        if ([] === $where) {
// Get table foreign column
                            $f = $t->getForeignColumn() ?: [$master->getReferenceKeyName()];

                            foreach ($f as $clm) {
                                $val = $clm === $master->getReferenceKeyName() ? $this->id : array_get($row, $clm);
                                if (null !== $val) {
                                    $find->where($clm, $val);

                                    $where[$clm] = $val;
                                }
                            }
                        }
// Trying to find row
                        $find = $find->get();
                    }

                    if (!is_null($find) && $totalRow = $find->count()) {
                        if ($totalRow) {
                            $update = $this->update($model, $row, $where);
                            if (true === $update || (is_numeric($update) && $update > 0)) {
                                $count += 1;
                            }
                        } else {
                            abort(404, 'Could not update multiple row at once');
                        }

                    } else {
// Add create time column
                        $this->addCreateTime($t, $row);
// Add foreign key column
                        $row[$master->getReferenceKeyName()] = $id;
// Bind row in to model
                        $this->rowBinder($model, $row);
// Save
                        $model->save();

                        if (is_int($model->id) && $model->id > 0) {
                            $count += 1;
                        }
                    }
                }

                if (isset($notInDelete[$table])) {
                    $query = \DB::table($table)->where($master->getReferenceKeyName(), $this->id);
                    foreach ($notInDelete[$table] as $column => $value) {
                        $query->whereNotIn($column, $value);
                    }

                    if ($query->delete()) {
                        $count += 1;
                    }
                }
            }
        }
        return $count;
    }

}