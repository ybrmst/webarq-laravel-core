<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/22/2016
 * Time: 12:41 PM
 */

namespace Webarq\Manager\Cms\HTML;


use Illuminate\Support\Arr;
use URL;
use Wa;
use Webarq\Info\ModuleInfo;
use Webarq\Info\PanelInfo;
use Webarq\Manager\AdminManager;
use Webarq\Manager\Cms\HTML\Table\Driver\AbstractManager;
use Webarq\Manager\SetPropertyManagerTrait;
use Webarq\Reader\ModuleConfigReader;
use Illuminate\Support\Str;

class TableManager extends \Webarq\Manager\HTML\TableManager
{
    use SetPropertyManagerTrait;

    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var ModuleInfo
     */
    protected $module;

    /**
     * @var PanelInfo
     */
    protected $panel;

    /**
     * Table headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Table sequential
     *
     * @var array
     */
    protected $sequence = [];

    /**
     * @var
     */
    protected $actions;

    /**
     * Pagination settings, array [limit, view]
     *
     * @var array|number
     */
    protected $pagination = 10;

    /**
     * @var string
     * @todo
     */
    protected $themes = 'default';

    /**
     * @var
     */
    protected $table;

    /**
     * Searchable column
     *
     * @var array
     */
    protected $searchable = [];

    /**
     * @var callable
     */
    protected $where;

    /**
     * @var array
     */
    protected $modals = [];

    /**
     * @var array
     */
    protected $tree = [];

    /**
     * @var bool
     */
    protected $dataTables = false;

    /**
     * Additional scripts
     *
     * @var array
     */
    protected $scripts = [];

    /**
     * Additional styles
     *
     * @var array
     */
    protected $styles = [];

    /**
     * A view container to put on the html script
     *
     * @var
     */
    protected $layout = 'webarq::themes.admin-lte.layout.listing';

    /**
     * Additional note will be put below the table
     *
     * @var string
     */
    protected $note;

    /**
     * URL queries
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Listing top section
     *
     * @var string
     */
    protected $top = 'webarq::themes.admin-lte.listing.default.top';

    /**
     * Listing bottom section
     *
     * @var
     */
    protected $bottom = 'webarq::themes.admin-lte.listing.default.bottom';

    /**
     * Create CMS\TableManager instance
     *
     * @param AdminManager $admin
     * @param ModuleInfo $module
     * @param PanelInfo $panel
     */
    public function __construct(AdminManager $admin, ModuleInfo $module, PanelInfo $panel)
    {
        $this->admin = $admin;
        $this->module = $module;
        $this->panel = $panel;
        $this->actions = $panel->getActions();

        $settings = $panel->getListing();
        $this->setPropertyFromOptions($settings);

        if (null !== $this->searchable && !is_array($this->searchable)) {
            $this->searchable = explode(',', $this->searchable);
        }

        $this->setURLQueries();

        $this->collectPagination();

        $this->collectActions();

        if (is_string($this->title)) {
            $this->setTitle($this->title);
        }
// Merge table class
        $this->setContainer('table', 'table', [
                        'class' => array_get($this->componentContainer, 'table.1.class') . ' table-listing'
                ]
        );
    }

    /**
     * Set active url query
     */
    protected function setURLQueries()
    {
        $queries = request()->query();

        if ([] !== $queries) {
            foreach ($queries as $k => $v) {
                if ('' === $v) {
                    continue;
                } elseif (in_array($k, ['q', 'perpage', 'search'])) {
                    $this->queries[$k] = $v;
                    continue;
                }

                list($k, $c) = explode(':', $k, 2) + [1 => false];

                if (false !== $c) {
                    switch ($k) {
                        case 'w':
                            $this->queries[$k . ':' . $c] = $this->where[$c] = $v;
                            break;
                        case 's':
                            $this->queries['sequence'][$c] = $v;
                    }
                }
            }
        }
    }

    /**
     * Pagination setting
     */
    protected function collectPagination()
    {
// Pagination property should be array
        if (!is_array($this->pagination)) {
            $this->pagination = [
                    $this->pagination,
                    Wa::getThemesView(config('webarq.system.themes', 'default'), 'common.pagination', false)
            ];
        }

// Check for URL query pagination
        $this->pagination[0] = request()->query('perpage', $this->pagination[0]);
    }

    /**
     * Collect actions and grouping them according to placement setting
     */
    protected function collectActions()
    {
        if ([] !== $this->actions) {
// Create action should be on listing header
            if (null !== ($create = array_pull($this->actions, 'create'))) {
                if (!is_array($create)) {
                    $create = ['class' => 'header'];
                } else {
                    $create['class'] = isset($create['class']) ? $create['class'] . ' header' : 'header';
                }
                $groups['header']['create'] = $create;
            }

            foreach ($this->actions as $name => $setting) {
                if (is_numeric($name)) {
                    $name = $setting;
                    $setting = [];
                }

                $this->collectModals($name, $setting);

// By default, action will be put on listing row item column action
                $placement = array_pull($setting, 'placement', 'listing');

                if (is_array($placement)) {
                    foreach ($placement as $key) {
// Add class placement

                        $setting['class'] = isset($setting['class']) ? $setting['class'] . ' ' . $key : $key;
                        $groups[$key][$name] = $setting;
                    }
                } else {
// Add class placement
                    $setting['class'] = isset($setting['class']) ? $setting['class'] . ' ' . $placement : $placement;
                    $groups[$placement][$name] = $setting;
                }
            }
            $this->actions = $groups;
        }
    }

    /**
     * @param $type
     * @param array $setting
     */
    protected function collectModals($type, array &$setting)
    {
        if (isset($setting['modal'])) {
            if (true === $setting['modal']) {
                $modal = [
                        'message' => config('webarq.template.modals.' . $type, config('webarq.template.modals.default')),
                        'button' => 'btn-default'
                ];
            } elseif (is_array($setting['modal'])) {
                $modal = $setting['modal'];
            } else {
                $modal = ['level' => 'warning', 'message' => $setting['modal'], 'btn-outline'];
            }
        } elseif ('delete' === $type) {
            $modal = [
                    'level' => 'danger',
                    'message' => config('webarq.template.modals.' . $type, config('webarq.template.modals.default')),
                    'button' => 'btn-outline'
            ];
        }

        if (isset($modal)) {
            if (!isset($modal['button'])) {
                $modal['button'] = isset($modal['level']) ? 'btn-outline' : 'btn-default';
            }

            $this->modals[$type] = $modal;

            $setting['data-modal'] = $type;

            unset($setting['modal']);
        }
    }

    /**
     * Build table HTML element
     *
     * @return \Illuminate\View\View
     */
    public function toHtml()
    {
        $this->buildHeader(array_get($this->headers, 'columns', []), $headers, $options);

        $this->loadDriver($options);

        $this->buildBody($headers);

        return view($this->layout, [
                'modals' => $this->getModals(),
                'scripts' => $this->getScript(),
                'styles' => $this->getStyles(),
                'useDataTables' => $this->useDataTables(),
                'isTree' => $this->isTree(),
                'listing_top' => $this->buildSection($this->top, [
                        'module' => $this->module,
                        'panel' => $this->panel,
                        'rows' => $this->driver->getRows(),
                        'action' => Wa::panel()->generateActionButton(array_get($this->actions, 'header', []), $this->module, $this->panel),
                        'actions' => array_get($this->actions, 'header', [])
                ]),
                'listing_bottom' => $this->buildSection($this->bottom, [
                        'module' => $this->module,
                        'panel' => $this->panel,
                        'rows' => $this->driver->getRows(),
                        'pagination' => $this->driver->paginate($this->pagination[1], $this->getQueryPagination()),
                        'notes' => $this->note,
                ]),
                'listing_html' => parent::toHtml()
        ]);
    }

    /**
     * @param array $groups
     * @param array|null $headers
     * @param array|null $options
     */
    protected function buildHeader(array $groups = [], array &$headers = null, array &$options = null)
    {
        $options = [];

        if ([] !== $groups) {
// All header should be grouped in case we need to split one header into smaller column
            if (!isset($groups[0]) || !is_array($groups[0])) {
                $groups = [$groups];
            }

            $this->addHead(array_get($this->headers, 'container'));

            foreach ($groups as $i => $items) {
                $row = $this->head->addRow(array_pull($items, 'container'));

                foreach ($items as $column => $attr) {
                    if (is_numeric($column)) {
                        $column = $attr;
                        $attr = [];
                    }

                    if (':' === substr($column, 0, 1)) {
                        $conf = ModuleConfigReader::get('origin.alias.' . substr($column, 1));
                        if (null !== $conf && is_array($conf) && Arr::isAssoc($conf)) {
                            foreach ($conf as $column => $attr1) {
                                $attr += $attr1;
                            }
                        } else {
                            continue;
                        }
                    }

// Sequence-able column
                    $sequence = array_pull($attr, 'sequence');
// Push column attributes into &$options
                    $options[$column] = $attr;

// Guarded column should not be shown on listing
                    if (null === array_pull($attr, 'guarded')) {
                        $this->forgetNonAttribute($attr);

                        $headers[$column] = $attr;

                        $cell = Wa::trans('webarq::core.title.' . array_pull($attr, 'title', $column));
                        if (null !== $sequence) {
                            $cell .= $this->makeSequenceAbleColumnButton(true === $sequence ? $column : $sequence);
                        }
                        $row->addCell($cell, array_pull($attr, 'container'), $attr);
                    }
                }

// Add action column
                if (0 === $i && [] !== array_get($this->actions, 'listing', [])) {
                    $row->addCell(Wa::trans('webarq::core.title.actionButton'), ['rowspan' => count($groups)]);
                }
            }
        }
    }

    /**
     * Forget non attribute keys
     *
     * @param array $attr
     */
    protected function forgetNonAttribute(array &$attr = [])
    {
        array_forget($attr, ['on']);
    }

    /**
     * Generate the sequence column button.
     *
     * @param $column
     * @return string
     */
    protected function makeSequenceAbleColumnButton($column)
    {
        $f = 'fa-arrows-h';
        $u = request()->url();
        $q = $this->queries;
        $s = array_pull($q, 'sequence', []);
        if (!isset($s[$column])) {
            $s[$column] = 'asc';
        } elseif ('asc' === $s[$column]) {
            $s[$column] = 'desc';
            $f = 'fa fa-arrow-circle-up';
        } else {
            unset($s[$column]);
            $f = 'fa fa-arrow-circle-down';
        }

        if ([] !== $s) {
            foreach ($s as $i => $j) {
                $q['s:' . $i] = $j;
            }
        }

        if ([] !== $q) {
            $u .= '?' . http_build_query($q);
        }

        return '&nbsp;<a href="' . $u . '" class="fa ' . $f . '">&nbsp;</a>';
    }

    /**
     * Load data driver
     *
     * @param array $columns
     */
    protected function loadDriver(array $columns)
    {
// Got a data driver
        if (null === $this->driver) {
            $driverModule = null;
            $driverTable = $this->table ?: $this->panel->getTable();
            if (str_contains($driverTable, '.')) {
                list($driverModule, $driverTable) = explode('.', $driverTable);
            }

            $this->driver = Wa::manager('cms.HTML!.table.driver.paginate',
                    Wa::table($driverTable, $driverModule),
                    $columns, [] === $this->tree ? $this->pagination[0] : null)
                    ->buildSequence(array_get($this->queries, 'sequence', $this->sequence))
                    ->buildSearch($this->searchable, \Request::input('q'));

            if (isset($this->where)) {
                $this->driver->buildWhere($this->where);
            }
        } else {
            $args = $this->driver;
            if (!is_array($args)) {
                $driver = $args;
                $args = [];
            } else {
                $driver = array_shift($args);
            }

            $sampling = false;

            if (true === array_get($args, 0)) {
                $sampling = array_pull($args, 0);
            }

            $this->setDriver(Wa::load('manager.html.table.driver.' . $driver, $args, Wa::getGhost()), $sampling);
        }
    }

    /**
     * @param array|null $headers
     */
    protected function buildBody(array $headers = null)
    {
        if ($this->driver instanceof AbstractManager && [] !== ($rows = $this->driver->getRows())) {
            if (null === $headers) {
                foreach ($rows[0] as $key => $value) {
                    $headers[$key] = [];
                }

                $this->buildHeader($headers);
            }

            $this->addBody();
// Attributes modifier
            $while = array_get($this->headers, 'while', []) +
                    ['is_active' => [0 => ['class' => 'red'], 1 => ['class' => 'green']]];

            foreach ($rows as $i => $item) {
// Check for while setting
                $attr = ['class' => 0 === $i % 2 ? 'even' : 'odd'];

                if ([] !== $this->tree) {
                    $attr['data-tree-root'] = $item->{$this->tree['branch']};
                    $attr['data-tree-branch'] = $item->{$this->tree['root']};
                }
// Each row attributes
                if ([] !== $while) {
                    foreach ($while as $keyColumn => $attributes) {
                        foreach ($attributes as $keyValue => $members) {
                            if ($this->castingValue($item->{$keyColumn}) === $this->castingValue($keyValue)) {
                                $attr = Arr::merge($attr, $members, 'join');
                            }
                        }
                    }
                }
// Init table body row manager
                $row = $this->body->addRow(array_get($this->headers, 'wrapper', 'tr'), $attr);

                foreach ($headers as $key => $setting) {
                    if (Str::startsWith($key, '.')) {
                        $path = explode('.', substr($key, 1));
                        $val = clone $item;
                        while ([] !== $path) {
                            $pull = array_shift($path);
                            $val = $val->$pull;
                            if (empty($val)) {
                                break;
                            }
                        }
                    } else {
                        if (false !== ($column = strstr($key, '.'))) {
                            if (false !== ($alias = strstr($column, ' as '))) {
                                $key = trim(str_replace(' as ', '', $alias));
                            } else {
                                $key = trim($column, '. ');
                            }
                        }
                        $val = $item->{$key};
                    }

                    if (isset($setting['render'])) {
                        $val = $this->modelRender(is_object($val) ? $val : $item, $setting['render'], $key);
                    }

                    if (null !== ($modifier = array_get($setting, 'modifier'))) {
                        $row->addCell(Wa::modifier($modifier, $val));
                    } else {
                        $row->addCell($val);
                    }
                }

                if ([] !== ($actions = array_get($this->actions, 'listing', []))) {
                    $row->addCell(Wa::panel()->generateActionButton(
                            $actions, $this->module, $this->panel, $item->toArray()), 'td', ['class' => 'action']
                    );
                }
            }
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function castingValue($value)
    {
        if (is_numeric($value)) {
            return (int)$value;
        } elseif (is_string($value)) {
            return strtolower($value);
        }

        return $value;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param $render
     * @param $key
     */
    protected function modelRender($model, $render, $key)
    {
        if (true === $render) {
            return $model->render($key);
        } elseif (is_array($render)) {
            return $model->render(array_pull($render, 0), ... $render);
        } else {
            return $model->render($render);
        }
    }

    /**
     * Getter method for listing page modals
     *
     * @return array
     */
    public function getModals()
    {
        return $this->modals;
    }

    /**
     * Get additional scripts
     *
     * @return array
     */
    public function getScript()
    {
        return $this->scripts;
    }

    /**
     * Get additional styles
     *
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @return bool
     */
    public function useDataTables()
    {
        return $this->dataTables;
    }

    /**
     * @return bool
     */
    public function isTree()
    {
        return array_get($this->tree, 'initialState', [] !== $this->tree);
    }

    /**
     * @param $type
     * @param array $options
     * @return \Illuminate\View\View
     */
    protected function buildSection($type, array $options)
    {
        if (true === $type || is_string($type)) {
            return view($type, $options);
        } elseif (is_callable($type)) {
            return $type($options);
        }
    }

    /**
     * @return array
     */
    protected function getQueryPagination()
    {
        $arr = ['q', 'search', 'perpage'];
//        Loop url queries
        if ([] !== ($queries = request()->query())) {
            foreach ($queries as $k => $v) {
                if (Str::startsWith($k, 'w:') || Str::startsWith($k, 's:')) {
                    $arr[] = $k;
                }
            }
        }

        return $arr;
    }
}