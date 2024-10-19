<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 1:46 PM
 */

namespace Webarq\Manager\HTML;


use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Manager\HTML\Table\BodyManager;
use Webarq\Manager\HTML\Table\Driver\DriverAbstractManager;
use Webarq\Manager\HTML\Table\RowManager;

class TableManager implements Htmlable
{
    /**
     * @var \Webarq\Manager\HTML\ElementManager
     */
    protected $title;

    /**
     * @var \Webarq\Manager\HTML\Table\BodyManager
     */
    protected $head;

    /**
     * Table column name
     *
     * @var array
     */
    protected $columns = [];

    /**
     * @var \Webarq\Manager\HTML\Table\BodyManager
     */
    protected $body;

    /**
     * @var \Webarq\Manager\HTML\Table\BodyManager
     */
    protected $foot;

    /**
     * Default containers
     *
     * @var array
     */
    protected $componentContainer = [
            'table' => ['table', ['class' => 'table']],
            'head' => ['thead', ['class' => 'thead']],
            'body' => ['tbody', ['class' => 'tbody']],
            'foot' => ['tfoot', ['class' => 'tfoot']]
    ];

    /**
     * Data driver
     *
     * @var \Webarq\Manager\Cms\HTML\Table\Driver\AbstractManager
     */
    protected $driver;

    public function __construct($title = null, array $heads = [])
    {
        if (isset($title)) {
            if (is_array($title)) {
                $heads = $title;
                $title = null;
            } else {
                $this->setTitle($title);
            }
        }

        if ([] !== $heads) {
            $manager = $this->addHead()->addRow();
            foreach ($heads as $key => $setting) {
                if (is_numeric($key)) {
                    $key = $setting;
                    $setting = [];
                }

                $this->columns[] = $key;

                $manager->addCell($key, $setting);
            }
        }
    }

    /**
     * @param $title
     * @param string $container
     * @param array $attr
     * @return $this
     */
    public function setTitle($title, $container = 'h3', array $attr = [])
    {
        $this->title = Wa::html('element', $title, $container, $attr);

        return $this;
    }

    /**
     * @param string $container
     * @param array $attributes
     * @return mixed
     */
    public function addHead($container = 'thead', $attributes = [])
    {
        if (is_array($container)) {
            list($container, $attributes) = $container;
        }

        return $this->addRows(['head', $container, $attributes]);
    }

    /**
     * @param array $args Array of [$type, $container, $attributes] or [$type, $callback, $container, $attributes]
     * @return mixed
     */
    public function addRows(array $args = [])
    {
        $type = $args[0];
        $container = array_get($args, 1);
        $attributes = array_get($args, 2, []);

        if (is_callable($container) && !is_string($container)) {
            $callback = $container;
            $container = array_get($args, 2, 't' . $type);
            $attributes = array_get($args, 3, []);
        }

        if (is_array($container)) {
            $attributes = $container;
            $container = 't' . $type;
        }

        if (!isset($this->{$type})) {
            if (isset($container)) {
                $this->setContainer($type, $container, $attributes);
            }

            $this->{$type} = Wa::html('table.body', $type);
        }

        if (isset($callback)) {
            $callback($this->{$type});

            return $this;
        }

        return $this->{$type};
    }

    /**
     * @param $key
     * @param $value
     * @param array $attributes
     * @return $this
     */
    public function setContainer($key, $value, array $attributes = [])
    {
        if (!is_array($key)) {
            $this->componentContainer[$key] = [$value, Arr::merge(
                    array_get($this->componentContainer, $key . '.1', []), $attributes)];
        } else {
            $this->componentContainer = $key + $this->componentContainer;
        }

        return $this;
    }

    /**
     * To use driver sampling data give bool true as second parameter
     *
     * @param string $type
     * @param mixed $args [, ... $args]
     * @return mixed
     */
    public function driver($type, $args = null)
    {
// Check if using sampling data
        $sampling = false;
// Get arguments
        $args = func_get_args();
// Remove type arguments
        array_shift($args);
// Remove second parameter while true
        if (true === array_get($args, 0)) {
            $sampling = array_pull($args, 0);
        }

        $this->setDriver(Wa::load('manager.HTML!.table.driver.' . $type, $args, Wa::getGhost()), $sampling);

        return $this->driver;
    }

    /**
     * Set table manager driver
     *
     * @param DriverAbstractManager|null $driver
     * @param bool|false $sampling
     */
    protected function setDriver(DriverAbstractManager $driver = null, $sampling = false)
    {
        $this->driver = $driver;

        if ($sampling) {
            $this->driver->sampling();
        }
    }

    /**
     * @param string $container
     * @param array $attributes
     * @return mixed
     */
    public function addFoot($container = 'tfoot', $attributes = [])
    {
        return $this->addRows(array_merge(['foot'], func_get_args()));
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (null !== $this->driver && $this->driver instanceof DriverAbstractManager) {
            $this->compileDriver();
        }

        $str = '';
        if (isset($this->title)) {
            $str .= $this->title->toHtml();
        }

        $rowHtml = '';
        foreach (['head', 'body', 'foot'] as $key) {
            $rowHtml .= $this->buildComponent($key);
        }
        $str .= Wa::html('element', $rowHtml, array_get($this->componentContainer, 'table.0', 'table'),
                array_get($this->componentContainer, 'table.1', []))->toHtml();

        return $str;
    }

    /**
     * Set head, body, and footer based on driver data
     */
    protected function compileDriver()
    {
// Check if head has been set before
        if (null === $this->head && is_array($this->driver->getData('head'))) {
            $head = $this->addHead()->addRow();
            foreach ($this->driver->getData('head') as $column => $attributes) {
                if (is_numeric($column)) {
                    $column = $attributes;
                    $attributes = [];
                } elseif (!is_array($attributes)) {
                    $attributes = [];
                }

                $this->columns[] = $column;

                $head->addCell(
                        title_case(str_replace(['-', '_'], ' ', $column)),
                        array_pull($attributes, 'container'), $attributes
                );
            }
        }

        if ([] !== ($rows = $this->driver->getData('rows'))) {
            $this->addBody();

            foreach ($rows as $row) {
                $this->buildRow($this->body->addRow(), $row);
            }
        }
    }


    /**
     * @param string $container
     * @param array $attributes
     * @return mixed
     */
    public function addBody($container = 'tbody', $attributes = [])
    {
        return $this->addRows(array_merge(['body'], func_get_args()));
    }

    /**
     * Helper to decide how to print row member
     *
     * @param RowManager $handler
     * @param array $row
     */
    protected function buildRow(RowManager $handler, array $row)
    {
        if (Arr::isAssoc($row) && [] !== $this->columns) {
            foreach ($this->columns as $column) {
                $handler->addCell(array_get($row, $column));
            }
        } else {
            foreach ($row as $value) {
                $handler->addCell($value);
            }
        }
    }

    /**
     * @param $type
     * @return string
     */
    private function buildComponent($type)
    {
        if (isset($this->{$type}) && $this->{$type} instanceof BodyManager) {
            return Wa::html(
                    'element',
                    $this->{$type}->toHtml(),
                    array_get($this->componentContainer, $type . '.0', 't' . $type),
                    array_get($this->componentContainer, $type . '.1', [])
            )->toHtml();
        }
    }
}