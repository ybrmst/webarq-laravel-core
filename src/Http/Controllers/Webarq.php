<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 10:13 AM
 */

namespace Webarq\Http\Controllers;


use App\Http\Controllers\Controller;
use Wa;

class Webarq extends Controller
{
    /**
     * Called class
     *
     * @var string
     */
    protected $controller;

    /**
     * @var object|string
     */
    protected $module;

    /**
     * @var string Panel name
     */
    protected $panel;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var string
     */
    protected $themes = 'default';

    /**
     * @var \Illuminate\View\View
     */
    protected $layout = 'index';

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->controller = $this->getParam('controller');
        $this->action = $this->getParam('action');

        $this->setModule($this->getParam('module'));

        $this->setPanel($this->getParam('panel'));

        $this->setLayout($this->layout);
    }

    /**
     * Get params value by key
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    protected function getParam($key, $default = null)
    {
        return array_get($this->params, $key, $default);
    }

    /**
     * Set admin layout
     *
     * @param $name
     * @param $
     * @param array $attributes
     */
    protected function setLayout($name, array $attributes = [])
    {
        $this->layout = Wa::getThemesView($this->themes, 'layout.' . $name)
                ->with($attributes);

        $this->layout->{'themes'} = $this->themes;
    }

    /**
     * Our escaped method when something is wrong, so instead continue
     * to access the real action method, we just block the user.
     * This called automatically from routing.
     *
     * @return mixed
     */
    public function escape()
    {
        if ('POST' == \Request::method() && [] === \Request::input()) {
            return $this->actionGetNoMethod();
        }
    }

    public function actionGetNoMethod()
    {
        return Wa::getThemesView($this->themes, 'errors.405');
    }

    public function actionGetForbidden()
    {
        return Wa::getThemesView($this->themes, 'errors.403');
    }

    /**
     * Called from routing file
     *
     * @return object
     */
    public function after()
    {
        return $this->layout->render();
    }

    /**
     * Called from routing file.
     */
    public function before()
    {

    }

    /**
     * Get module name
     *
     * @return object|string
     */
    protected function getModule()
    {
        return is_object($this->module) ? $this->module->getName() : $this->module;
    }

    /**
     * Set module with an object of Webarq\Info\ModuleInfo
     * If module not exist in configuration, string $module will be used
     *
     * @param string $module Module name
     */
    protected function setModule($module)
    {
        $this->module = Wa::module($module) ?: $module;
    }

    /**
     * Get panel name
     *
     * @return string
     */
    protected function getPanel()
    {
        return is_object($this->panel) ? $this->panel->getName() : $this->panel;
    }

    /**
     * Set panel with an object of Webarq\Info\PanelInfo
     * If module not exists in configuration, string $panel will be used
     *
     * @param string $panel
     */
    protected function setPanel($panel)
    {
        $this->panel = is_object($this->module) ? $this->module->getPanel($panel, $panel) : $panel;
    }
}