<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 6/15/2017
 * Time: 10:47 AM
 */

namespace Webarq\Http\Controllers\Panel\System;


use Illuminate\Pagination\LengthAwarePaginator;
use Request;
use Illuminate\Support\Str;
use Wa;
use Webarq\Http\Controllers\Panel\BaseController;

class LeadsController extends BaseController
{

    /**
     * @var \Illuminate\View\View
     */
    protected $layout = 'leads';

    public function actionGetIndex()
    {
        $columns = [];
        $type = app('request')->query('type');
        if (is_null($type)) {
            foreach (config('webarq.leads', []) as $type => $columns) break;
        } else {
            $columns = config('webarq.leads.' . $type, []);
        }

        if (isset($type)) {
            $paginate = $this->getPaginate($type, request()->query('perpage', 20));

            if ('export' === request()->query('button')) {
                $this->streamDownload($type, $columns, $paginate, ';');
                die;
            }

            $this->fixPaginator($paginate);

            $list = Wa::html('table');

            $list->driver('json')->setString($this->toJsonStr($paginate, $columns));

            $this->layout->with([
                    'leadGroups' => array_keys(config('webarq.leads', [])),
                    'leadHtml' => $list->toHtml(),
                    'leadActive' => $type,
                    'paginate' => $paginate->render(
                            Wa::getThemesView(config('webarq.system.themes', 'default'), 'common.pagination', false)
                    )
            ]);
        }
    }

    /**
     * @param $type
     * @param int $limit
     * @return mixed
     */
    protected function getPaginate($type, $limit = 20)
    {
        $builder = Wa::model('lead')
                ->where('lead_type', $type)
                ->orderBy('create_on', 'desc');

        if (app('request')->query('q')) {
            if (null !== Wa::table('leads')->getColumn('lead_value')) {
                $builder->where('lead_data', 'like', '%' . app('request')->query('q') . '%');
            } else {
                $builder->where('lead_data', 'like', '%' . app('request')->query('q') . '%');
            }
        }

        return $builder->paginate($limit);
    }

    /**
     * @param string $type
     * @param array $columns
     * @param LengthAwarePaginator $rows
     * @param string $delimiter
     * @param string $enclosure
     * @return mixed
     */
    protected function streamDownload($type, array $columns, LengthAwarePaginator $rows, $delimiter = ',', $enclosure = '"')
    {
        $fn = 'leads-' . snake_case($type, '-') . '-' . time();

        if ([] !== $rows->count()) {
            // tell the browser it's going to be a csv file
            header('Content-Type: application/csv');
            // tell the browser we want to save it instead of displaying it
            header('Content-Disposition: attachment; filename="' . $fn . '.csv";');
//        Create a file pointer
            $output = fopen('php://output', 'w');
//        Headings
            fputcsv($output, array_pluck($columns, 'label'), $delimiter, $enclosure);
//        Loop through the contents
            foreach ($rows as $row) {
                $row = unserialize($row->getAttribute('lead_data')) + $row->toArray();
                $tmp = [];
                foreach ($columns as $k => $column) {
                    $tmp[] = array_get($row, array_pull($column, 'path', $k));
                }

                fputcsv($output, $tmp, $delimiter, $enclosure);
            }

            fclose($output);
            die;
        } else {
            return $this->actionGetForbidden();
        }
    }

    /**
     * Add query into paginator
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     */
    protected function fixPaginator(\Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator)
    {
        foreach (['perpage', 'type'] as $k) {
            $paginator->addQuery($k, request()->query($k));
        }
    }

    /**
     * @param LengthAwarePaginator|null $object
     * @param array $columns
     * @return string
     */
    protected function toJsonStr(LengthAwarePaginator $object = null, array $columns)
    {
        $s = [];
        if ($object->count()) {
            foreach ($object as $item) {
                $arr = $item->toArray();
// Un-serialize the lead data
                $lead = Str::saveUnSerialize(array_pull($arr, 'lead_data'));
// Merge with previous cell
                if (is_array($lead)) {
                    $arr = $this->getLeadItems($lead + $arr, $columns);
                }

                $s[] = $arr;
            }
        }

        return json_encode($s);
    }

    /**
     * @param array $row
     * @param array $columns
     * @return array
     */
    protected function getLeadItems(array $row, array $columns)
    {
        $result = [];

        foreach ($columns as $name => $attr) {
            $result[$name] = array_get($row, $name);
            if (null !== ($modifier = array_get((array)$attr, 'modifier'))) {
                $result[$name] = Wa::modifier($result[$name]);
            }
        }

        return $result;
    }

    /**
     * @param $type
     * @return string
     */
    protected function head($type)
    {
        $s = $type . '<br/>';
        $s .= '<select></select>';

        return $s;
    }
}