<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/31/2017
 * Time: 4:35 PM
 */

namespace Webarq\Http\Controllers\Panel\Helper;


use Wa;
use Webarq\Http\Controllers\Panel\BaseController;
use Webarq\Manager\SetPropertyManagerTrait;
use Webarq\Model\NoModel;

class ExportController extends BaseController
{
    use SetPropertyManagerTrait;

    /**
     * Columns to select
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Column alias (to be printed on CSV row head)
     *
     * @var array
     */
    protected $alias = [];

    /**
     * Column value modifier
     *
     * @var array
     */
    protected $modifiers = [];

    /**
     * File CSV delimiter
     *
     * @var string
     */
    protected $delimiter = ',';

    /**
     * File CSV enclosure
     *
     * @var string
     */
    protected $enclosure = '"';

    public function actionGetIndex()
    {
//        Export options
        $options = $this->panel->getAction('export', []);
//        Compile the columns
        $this->setColumns(array_pull($options, 'columns', []));
//        Set property from options
        $this->setPropertyFromOptions($options);
//        Extend options from query string
        $this->optionsFromQueryString($options);
//        Get the rows to export
        $rows = $this->getRows($this->getParam(1), $options);

        if ([] !== $rows) {
            $fn = studly_case($this->panel->getName()) . '-' . date('M-d-y-s');
            // tell the browser it's going to be a csv file
            header('Content-Type: application/csv');
            // tell the browser we want to save it instead of displaying it
            header('Content-Disposition: attachment; filename="' . $fn . '.csv";');
            $this->streamDownload($rows);
        } else {
            return $this->actionGetForbidden();
        }
    }

    /**
     * Compile given columns
     *
     * @param array $columns
     */
    protected function setColumns(array $columns = [])
    {
        if ([] === $columns) {
            $this->columns = '*';
        } else {
            foreach ($columns as $i => $j) {
                if (is_numeric($i)) {
                    $this->columns[] = $j;
                    $this->alias[$i] = str_replace('_', ' ', title_case($j));
                } elseif (!is_array($j)) {
                    $this->columns[] = $i;
                    $this->alias[$i] = $j;
                } else {
                    $this->columns[] = $i;
                    $this->alias[$i] = array_get($j, 'title', str_replace('_', ' ', title_case($i)));
                    if (isset($j['modifier'])) {
                        $this->modifiers[$i] = $j['modifier'];
                    }
                }
            }
        }
    }

    /**
     * Extend options from query string
     *
     * @param array &$options
     */
    protected function optionsFromQueryString(array &$options = [])
    {
        $query = request()->query();

        if ([] !== $query) {
            foreach ($query as $k => $v) {
                if ('' === $v) {
                    continue;
                }
                list($k, $c) = explode(':', $k, 2) + [1 => false];

                if (false !== $c && in_array($c, $this->columns)) {
                    switch ($k) {
                        case 'w':
                            $options['where'][$c] = $v;
                            break;
                        case 's':
                            $options['sequence'][$c] = $v;
                    }
                }
            }
        }
    }

    /**
     * @param null $id
     * @param array $options
     * @return array
     */
    protected function getRows($id = null, array $options = [])
    {
// Check for model
        if (null !== ($var = array_get($options, 'model'))) {
            if (is_string($var)) {
                return Wa::model($var)->findExportData($id, $options);
            } elseif (is_callable($var)) {
                return $var($id, $options);
            }
        }

        return $this->nativeModel($id, $options)->get()->toArray();
    }

    /**
     * @param null $id
     * @param array $options
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function nativeModel($id = null, array $options)
    {
        $table = array_get($options, 'table', $this->panel->getTable());
        $class = NoModel::instance($table, Wa::table($table)->primaryColumn()->getName());
        $model = clone $class;
        $model = $model->select($this->columns);

        if (is_numeric($id)) {
            $model->where(Wa::table($table)->primaryColumn()->getName(), $id);
        }

        if (isset($options['where'])) {
            $class->whereQueryBuilder($model, $options['where']);
        }

        if (isset($options['sequence'])) {
            $class->sequenceQueryBuilder($model, $options['sequence']);
        }

        if (null !== ($limit = request()->query('perpage', array_get($options, 'pagination')))) {
            $model->limit($limit);
            if (null !== request()->query('page')) {
                $model->offset((request()->query('page') - 1) * $limit);
            }
        }

        return $model;
    }

    /**
     * @param array $rows
     */
    protected function streamDownload(array $rows)
    {
//        Create a file pointer
        $output = fopen('php://output', 'w');
//        Headings
        fputcsv($output, $this->alias, $this->delimiter, $this->enclosure);
//        Loop through the contents
        foreach ($rows as $row) {
//            Looking for modifier
            foreach ($row as $i => &$c) {
                if (isset($this->modifiers[$i])) {
                    $c = Wa::modifier($this->modifiers[$i], $c);
                }
            }
            fputcsv($output, $row, $this->delimiter, $this->enclosure);
        }

        fclose($output);
        die;
    }
}