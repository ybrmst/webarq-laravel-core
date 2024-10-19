<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/10/2017
 * Time: 1:39 PM
 */

namespace Webarq\Manager\Cms;


use Wa;
use Webarq\Info\ModuleInfo;
use Webarq\Info\PanelInfo;
use Webarq\Info\TableInfo;
use Webarq\Manager\AdminManager;

/**
 * Class PanelManager
 *
 * @package Webarq\Manager\Cms
 */
class PanelManager
{
    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var
     */
    protected $menus = [];

    /**
     * @var array
     */
    protected $icons = [];

    /**
     * Create PanelManager instance
     *
     * @param null|AdminManager $admin
     */
    public function __construct(AdminManager $admin = null)
    {
        $this->admin = $admin;

        $this->collectMenus();
    }

    /**
     * Collect menus
     */
    protected function collectMenus()
    {
        $modules = Wa::modules();

        if (is_array($modules) && [] !== $modules) {
            foreach ($modules as $module) {
                $module = Wa::load('info.module', $module);
                if ([] !== ($panels = $module->getPanels()) && !$module->isIgnored()) {
                    foreach ($panels as $panel) {
                        if ($this->isAccessible($module, $panel) && null === $panel->getAttribute('ignored')) {
                            $link = $this->listingURL($panel);
                            $this->menus[$module->getTitle()][$panel->getName()] = [$panel->getTitle(), $link];
                            $this->icons['module'][$module->getTitle() . '-icon'] = $module->getIcon();
                            $this->icons['panel'][$panel->getName() . '-icon'] = $panel->getAttribute('icon');
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if given module, panel, action path is accessible by current admin
     *
     * @param ModuleInfo $module
     * @param PanelInfo $panel
     * @param null|string|array $action
     * @return true
     */
    public function isAccessible(ModuleInfo $module, PanelInfo $panel = null, $action = null)
    {
        if (!is_null($action) && 'index' !== $action) {
            if (!is_array($action)) {
                $action = [$action];
            }

            foreach ($action as &$item) {
                if (is_bool($item)) {
                    continue;
                }
                $item = trim($item, '.');

                switch (substr_count($item, '.')) {
                    case 1 :
                        $item = $module->getName() . '.' . $item;
                        break;
                    case 0 :
                        $item = $module->getName() . '.' . $panel->getName() . '.' . $item;
                        break;
                }
            }
        } else {
            $action = $module->getName() . (null !== $panel->getName() ? '.' . $panel->getName() : '');
        }

        return !$panel->isGuarded() || $this->admin->hasPermission($action);
    }

    /**
     * @param PanelInfo $panel
     * @return string
     */
    public function listingURL(PanelInfo $panel)
    {
        $module = Wa::module($panel->getModule());
        if (null !== $module) {
            if ('configuration' === $panel->getType()) {
                $u = true === $panel->getPermalink() ? 'edit' : 'configuration/edit';
                return \URL::panel(\URL::detect($panel->getPermalink(), $module->getName(), $panel->getName(), $u));
            }

            $action = !empty($panel->getListing()) || 'listing' === $panel->getType()
                    ? 'listing' : $panel->getUrlParamAction();

            $link = $this->generateURL(
                    $panel->getPermalink(),
                    $module->getName(),
                    $panel->getName(),
                    $action
            );

            return $link;
        }
    }

    /**
     * @param $permalink
     * @param $module
     * @param $panel
     * @param string $action
     * @param array $rows
     * @return string
     */
    public function generateURL($permalink, $module, $panel, $action = '', array $rows = [])
    {
        list($permalink, $params) = $this->getPermalinkAndParams(
                $permalink, $action, Wa::table(Wa::module($module)->getPanel($panel)->getTable()));

        if (str_contains($permalink, '{')) {
            return $this->permalinkWithPattern($permalink, $rows);
        }

        return \URL::panel(\URL::detect($permalink, $module, $panel, $this->makeAction($action, $permalink)))
        . $this->suffixedParam($params, $rows);
    }

    /**
     * @param string $permalink
     * @param string $action
     * @param TableInfo|null $table
     * @return array
     */
    protected function getPermalinkAndParams($permalink, $action, TableInfo $table = null)
    {
        $params = [];
// Injected params
        if (is_string($permalink)) {
            if (starts_with($permalink, '.')) {
                $params = explode(',', substr($permalink, 1));
                $permalink = true;
            } elseif (starts_with($permalink, '?')) {
                $params = explode(',', substr($permalink, 1));
                $permalink = null;
            }
        }

// Default params
        if ([] === $params && null !== $table) {
            if (null === $table->primaryColumn() || !is_object($table->primaryColumn())) {
                abort(500, 'Could not find primary key for table ' . $table->getName() . ' in module ' . $table->getModule());
            }

            $params[] = $table->primaryColumn()->getName();
            switch ($action) {
                case 'activeness':
                    $params[] = 'is_active';
                    break;
                case 'is_system':
                    $params[] = 'is_system';
                    break;
            }
        }

        return [$permalink, $params];
    }

    /**
     * @param $permalink
     * @param array $rows
     * @return string
     */
    protected function permalinkWithPattern($permalink, array $rows)
    {
        foreach ($rows as $i => $j) {
            $permalink = str_replace('{' . $i . '}', $j, $permalink);
        }

        return \URL::panel($permalink);
    }

    /**
     * @param mixed $action
     * @param mixed $permalink
     * @return string
     */
    protected function makeAction($action, $permalink)
    {
        switch ($action) {
            case 'create':

            case 'edit':
                return 'form/' . $action;

            default:
                if (null === $permalink && '' === $action) {
                    $action .= '/' . config('webarq.system.default-action');
                }
                return $action;

        }
    }

    /**
     * @param array $params
     * @param array $rows
     * @return string
     */
    protected function suffixedParam(array $params, array $rows)
    {
        $str = '';

        if ([] !== $params && $rows !== []) {
            foreach ($params as $param) {
                $str .= '/' . array_get($rows, $param);
            }
        }

        return $str;

    }

    /**
     * @return null|AdminManager
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @return array
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * @param string $type
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getIcon($type, $key, $default = '')
    {
        return array_get($this->icons, $type . '.' . $key . '-icon') ?: $default;
    }

    /**
     * Generate completed panel html element
     *
     * @param null|string $view
     * @return string
     */
    public function generateMenu($view = null)
    {
        $html = '';

        if (is_null($view)) {
            if ([] !== $this->menus) {
                $html = '<ul class="navigation main">';
                foreach ($this->menus as $module => $items) {
                    $html .= '<li>';
                    $html .= '<h3 style="text-transform: capitalize;margin-bottom: 0;">' . $module . '</h3>';
                    $html .= '<ul class="navigation child">';
                    foreach ($items as $item) {
                        $html .= '<li>' . \Html::link($item[1], $item[0]) . '</li>';
                    }
                    $html .= '</ul>';
                    $html .= '</li>';
                }
            }
            return $html . '</ul>';
        }

        return view($view, ['menus' => $this->menus, 'html' => $html]);
    }

    /**
     * @param array $actions
     * @param ModuleInfo $module
     * @param PanelInfo $panel
     * @param array $row
     * @return string
     */
    public function generateActionButton($actions = [], ModuleInfo $module, PanelInfo $panel, array $row = [])
    {
// Keep actions as array
        if (!is_array($actions)) {
            $actions = [$actions];
        }

        $html = '';

        if ([] !== $actions) {
            foreach ($actions as $action => $options) {
// Button permissions
                $permissions = array_pull($options, 'permissions', []);
                if (!is_array($permissions)) {
                    $permissions = [$permissions];
                }
                $permissions = array_merge([$action], $permissions);

                if ($this->isAccessible($module, $panel, $permissions)) {
// Pull out rules from button settings
                    $rules = array_pull($options, 'rules', []);

                    if (Wa::manager('cms.rule', $this->admin, $rules, $row, $panel->getTable(), $action)->isValid()) {
                        $options['permalink'] = $this->generateURL(array_get($options, 'permalink'),
                                $module->getName(), $panel->getName(), $action, $row);

                        $html .= Wa::manager('cms.HTML!.table.button', $options + ['type' => $action])->toHtml();
                    }
                }
            }
        }

        return $html;
    }
}