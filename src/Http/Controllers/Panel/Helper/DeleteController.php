<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/26/2017
 * Time: 11:21 AM
 */

namespace Webarq\Http\Controllers\Panel\Helper;


use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Http\Controllers\Panel\BaseController;
use Webarq\Info\PanelInfo;
use Webarq\Info\TableInfo;
use Webarq\Model\NoModel;

class DeleteController extends BaseController
{
    /**
     * @var mixed
     */
    protected $id;

    public function actionGetIndex()
    {
        $id = $this->getParam('1');
        $op = $this->panel->getAction('delete', []);
// Find row
        $cn = Wa::table($this->panel->getTable())->primaryColumn()->getName();
        $rw = NoModel::instance($this->panel->getTable($cn))
                ->where($cn, $id)
                ->first();

        if (null === $rw) {
            return $this->actionGetForbidden();
        } else {
            $rw = $rw->toArray();
            if ([] === ($rules = array_get($op, 'rules', []))
                    || Wa::manager('cms.rule', $this->admin, $rules, $rw, $this->panel->getTable())->isValid()
            ) {
                if (isset($op['model'])) {
                    return $this->deleteByModel(Wa::model($op['model']) ?: new $op['model'], $id);
                } else {
                    $tb = $this->compile($op);

                    if ([] !== $tb) {
                        list($primary, $secondaries) = $tb;
                        list($table, $options) = $primary;
// Deleting some row
                        $mgr = Wa::table($table);
                        $this->checkSequenceColumn($mgr, $options);
                        $del = Wa::manager('cms.query.delete', $table, $options, [$rw], $mgr->primaryColumn()->getName())
                                ->delete(true);

                        if ($del) {
                            if ([] !== $secondaries) {
                                foreach ($secondaries as $table => $options) {
                                    $this->checkSequenceColumn(Wa::table($table), $options);

                                    Wa::manager('cms.query.delete', $table, $options,
                                            $this->getParam(1), $mgr->getReferenceKeyName())->delete(false);
                                }
                            }
                        }
// Set message to session
                        $this->setTransactionMessage(Wa::trans('webarq::core.messages.success-delete'), 'success');

                        if (null !== ($callback = array_get($op, 'callback')) && is_callable($callback)) {
                            if (null !== ($call = $callback($id))) {
                                return $call;
                            }
                        }

                        return redirect(Wa::panel()->listingURL($this->panel));
                    } else {
                        return $this->actionGetForbidden();
                    }
                }
            } else {
                return $this->actionGetForbidden();
            }
        }
    }

    protected function deleteByModel(Model $model, $id)
    {
        $row = null;
        if ((int)$model->cmsDelete($id, $row) > 0) {
            if (is_array($row)) {
                foreach ($row as $row) break;
            }

            Wa::instance('manager.cms.history')
                    ->record(
                            $this->admin,
                            'delete',
                            Wa::table($model->getTable()),
                            $row->toArray()
                    );

            $this->setTransactionMessage(Wa::trans('webarq::core.messages.success-delete'), 'success');
        }

        return redirect(Wa::panel()->listingURL($this->panel));
    }

    /**
     * @param array $options
     * @return array
     */
    protected function compile(array &$options = [])
    {
        $tables = array_pull($options, 'tables', []);

        if ([] === $tables) {
            $tables = $this->checkTable($this->panel);
        }

        if ([] !== $tables) {
            $primary = '';
            foreach ($tables as $table => $options) {
                if (is_numeric($table)) {
                    unset($tables[$table]);
                    $table = $options;
                    $options = [];
                    $tables[$table] = $options;
                }

                if ('' === $primary || 0 === strpos($primary, $table)) {
                    $primary = $table;
                }
            }

            return [[$primary, array_pull($tables, $primary, [])], $tables];
        }

        return [];
    }

    /**
     * @param PanelInfo|null $panel
     * @return array
     */
    protected function checkTable(PanelInfo $panel = null)
    {
        if (null !== $panel) {
            if ([] !== ($inputs = $panel->getAction('create.form', []))) {
                $tables = [];
                foreach ($inputs as $input => $setting) {
                    if (is_numeric($input)) {
                        $input = $setting;
                        $setting = [];
                    }
// This is a must
                    if (2 !== substr_count($input, '.')) continue;

                    list($module, $table, $column) = explode('.', $input);

                    if (!isset($tables[$table])) {
                        $tables[$table] = [];
                    }

                    if (null !== Wa::table($table)) {
                        if (null !== ($config = Wa::table($table)->getColumn($column))) {
                            $setting = Arr::merge($config->unserialize(), $setting);
                        }

                        if ('sequence' === array_get($setting, 'master')) {
                            $tables[$table]['sequence-column'] = $column;
                            if (null !== ($grouping = array_get($setting, 'grouping-column'))) {
                                if (is_array($grouping)) {
                                    $tables[$table]['sequence-column'] .= ':' . implode(',', $grouping);
                                } else {
                                    $tables[$table]['sequence-column'] .= ':' . $grouping;
                                }
                            }
                        }

                        if (null !== array_get($setting, 'file')) {
                            $tables[$table]['mime-column'][] = $column;
                        }
                    }
                }

                return $tables;
            } else {
                return [$panel->getTable()];
            }
        }

        return [];
    }

    /**
     * @param TableInfo $table
     * @param array $options
     */
    protected function checkSequenceColumn(TableInfo $table = null, array &$options)
    {
        if (null !== $table && !isset($options['sequence-column']) && null !== $table->getSequence()) {
            $options['sequence-column'] = $table->getSequence()->getName();
        }
    }
}