<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 4:11 PM
 */

namespace Webarq\Manager\Cms;


use Illuminate\Support\Arr;
use Webarq\Info\TableInfo;
use Webarq\Manager\AdminManager;
use Webarq\Model\HistoryModel;

class HistoryManager
{
    /**
     * @param AdminManager $admin
     * @param string $action Transaction action (delete, edit, create, ... etc)
     * @param TableInfo $table
     * @param array $row
     * @param int|number $parentId
     * @return bool
     */
    public function record(AdminManager $admin, $action, TableInfo $table, array $row = [], $parentId = 0)
    {
        $property = $table->getHistories();

        if (!isset($property['group'])) {
            $property['group'] = str_singular($table->getName());
        }

        if ([] !== $row) {
            foreach ($property as $key => &$value) {
                $value = array_pull($row, $value, $value);
            }
        }

        $model = new HistoryModel();
        $model->{'parent_id'} = $parentId;
        $model->{'role_level'} = $admin->getLevel(true);
        $model->{'action'} = array_pull($property, 'action', $action);
        $model->{'actor'} = $admin->getProfile('username');
        $model->{'properties'} = Arr::serialize($property);
        $model->{'create_on'} = date('Y-m-d H:i:s');

        return $model->save();
    }

    public function formatting(HistoryModel $item, $action)
    {
        $properties = Arr::deserialize($item->properties);

        $str = ucwords($action .
                ' <b>' . array_get($properties, 'group') . ' "' . array_get($properties, 'item') . '"</b>');

        if (isset($properties['object'])) {
            $str .= ' ' . $properties['object'];
        }

        return $str;
    }
}