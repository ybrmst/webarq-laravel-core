<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/18/2017
 * Time: 6:34 PM
 */

namespace Webarq\Manager\Cms\Query;


use DB;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Info\TableInfo;

class InsertManager extends QueryManager
{
    protected $formType = 'create';

    /**
     * @return null|array
     */
    public function execute()
    {
        if (isset($this->model)) {
            return $this->model->formInsert($this->post);
        } elseif ([] !== $this->post && !is_null($this->master)) {
// Master data should be inserted before another
            $row = array_pull($this->post, $this->master, []);

            if ([] !== $row) {
                $m = Wa::table($this->master);
// Initiate model
                $model = $this->initiateModel($this->master);
// Create time completion
                $this->addCreateTime($m, $row);
// Bind row in to model
                $this->rowBinder($model, $row);
// Save
                $model->save();
// Collect inserted id
                $id[$this->master] = $model->{$model->getKeyName()};
// Log activity
                Wa::instance('manager.cms.history')->record(\Auth::user(), 'insert', $m, $row);
// Translation
                $tr = array_pull($this->post, 'translation', []);
                $this->insertTranslation($id[$this->master], $m, $tr);
// Secondary row
                return array_merge($id, $this->insertSecondaryRow($id[$this->master], $m, $this->post));
            }
        }
    }

    /**
     * @param $id
     * @param TableInfo $table
     * @param array $rows
     */
    protected function insertTranslation($id, TableInfo $table, array $rows = [])
    {
        if ($table->isMultiLingual() && [] !== $rows) {
// Table name translation
            $t = \Wl::translateTableName($table->getName());
            $rows = array_get($rows, $t, []);

            if ([] !== $rows) {
                foreach ($rows as $code => $row) {
// Translation row completion
                    $row += [
                            'create_on' => date('Y-m-d H:i:s'),
                            \Wl::getLangCodeColumn('name') => $code,
                            $table->getReferenceKeyName() => $id
                    ];

                    $model = $this->initiateModel($t, 'id');
                    $this->rowBinder($model, $row);
                    $model->save();
                }
            }
        }
    }

    /**
     * @param $id
     * @param TableInfo $master
     * @param array $groups
     * @return array
     */
    protected function insertSecondaryRow($id, TableInfo $master, array $groups)
    {
        $ids = [];
        foreach ($groups as $table => $rows) {
            $t = Wa::table($table);
            if (Arr::isAssoc($rows)) {
                $rows = [$rows];
            }
// We need to collect inserted id, so just inserted row by row
            foreach ($rows as $row) {
// Initiate model
                $model = $this->initiateModel($table);
// Add create time column
                $this->addCreateTime($t, $row);
// Add foreign key column
                $row[$master->getReferenceKeyName()] = $id;
// Bind row in to model
                $this->rowBinder($model, $row);
// Save
                $model->save();
// Collect inserted id
                $ids[$table][] = $model->{$model->getKeyName()};
            }
        }

        return $ids;
    }
}