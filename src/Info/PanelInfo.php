<?php
/**
 * Created by PhpStorm
 * Date: 18/12/2016
 * Time: 11:03
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Info;


use Wa;
use Webarq\Manager\setPropertyManagerTrait;

/**
 * Helper class
 *
 * Class PanelInfo
 * @package Webarq\Info
 */
class PanelInfo
{
    use SetPropertyManagerTrait;

    /**
     * Panel type
     *
     * @var
     */
    protected $type;

    /**
     * Panel name
     * Used when generate panel anchor <a/> html tag
     *
     * @var
     */
    protected $name;

    /**
     * Panel permalink
     * Used when generate panel anchor <a/> html tag
     *
     * @var mixed
     */
    protected $permalink;

    /**
     * Panel title
     *
     * Used when generate panel anchor <a/> html tag
     *
     * @var
     */
    protected $title;

    /**
     * Panel listing configuration
     *
     * @var array
     */
    protected $listing = [];

    /**
     * Panel actions configuration
     *
     * Used when generate panel button in listing
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Panel attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Panel module
     *
     * @var
     */
    protected $module;

    /**
     * Is panel guarded
     *
     * @var bool
     */
    protected $guarded = true;

    /**
     * Panel primary table
     *
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $urlParamAction = '';

    /**
     * @var
     */
    protected $model;

    /**
     * Create PanelInfo instance
     *
     * @param string $name Panel name
     * @param string $module Panel Module
     * @param array $options Panel options
     */
    public function __construct($name, $module, array $options)
    {
        $this->name = $name;

        $this->module = $module;

        $this->setPropertyFromOptions($options, true);

        $this->attributes = $options;

        if ([] !== $this->actions) {
            foreach ($this->actions as $key => $options) {
                if (is_numeric($key)) {
                    unset($this->actions[$key]);
                    $this->actions[$options] = [];
                }
            }
        }

        $this->setTitle();

        $this->setTable();
    }

    /**
     * Get panel action
     *
     * @param null|string $key
     * @param null $default
     * @return mixed
     */
    public function getAction($key = null, $default = null)
    {
        return array_get($this->actions, $key, $default);
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get panel title
     *
     * @return mixed
     */
    public function getTitle()
    {
        if (null === $this->title) {
            $this->title = ucfirst($this->name);
        }

        return $this->title ?: ucfirst($this->name);
    }

    /**
     *
     */
    protected function setTitle()
    {
        if (null === $this->title) {
            $this->title = $str = $this->name;
            if (str_contains($str, ':')) {
                list($this->title, $this->table) = explode(':', $str, 2);
            }

            $this->title = Wa::trans($this->title);
        }
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    protected function setTable()
    {
        if (null === $this->table) {
            $this->table = $this->name;
        }
    }

    /**
     * @return array
     */
    public function getListing()
    {
        return $this->listing;
    }

    public function getModel($action = null)
    {
        $model = null !== $action
                ? array_get($this->actions, $action . '.model')
                : $this->model;

        if (null !== $model) {
            return Wa::model($model);
        } else {
            return Wa::table($this->getTable())->model();
        }
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get panel permalink, auto generate url panel when action is set
     *
     * @param null|string $action
     * @return mixed
     */
    public function getPermalink($action = null)
    {
        return is_null($action)
                ? $this->permalink
                : \URL::panel(\URL::detect($this->permalink, $this->module, $this->name, $action));
    }

    public function isGuarded()
    {
        return true === $this->guarded;
    }

    /**
     * @return string
     */
    public function getUrlParamAction()
    {
        return $this->urlParamAction;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return array_get($this->attributes, $key, $default);
    }
}