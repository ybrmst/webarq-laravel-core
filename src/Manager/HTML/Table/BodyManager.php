<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 2:22 PM
 */

namespace Webarq\Manager\HTML\Table;


use Illuminate\Contracts\Support\Htmlable;
use Wa;

class BodyManager implements Htmlable
{
    /**
     * Body row collections
     *
     * @var array
     */
    protected $rows = [];

    /**
     * Row container
     *
     * @var array
     */
    protected $containers = [];

    /**
     * Table section: head|body|foot
     *
     * @var string
     */
    protected $type;


    /**
     * Table section: head|body|foot
     *
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Add tr into thead, tbody or tfoot
     *
     * @param string $container
     * @param array $attributes
     * @return mixed
     */
    public function addRow($container = 'tr', $attributes = [])
    {
        if (is_array($container)) {
            $attributes = $container;
            $container = 'tr';
        } elseif (null === $container) {
            $container = 'tr';
        }

        $this->containers[] = [$container, $attributes];

        return $this->rows[] = Wa::html('table.row', $this->type);
    }

    /**
     * Get tr html element
     *
     * @return string
     */
    public function toHtml()
    {
        if ([] !== $this->rows) {
            $s = '';
            foreach ($this->rows as $i => $row) {
                $s .= Wa::html('element', $row->toHtml() ,
                        array_get($this->containers, $i . '.0' , 'tr'),
                        array_get($this->containers, $i . '.1' , []))->toHtml();
            }
            return $s;
        }
    }
}