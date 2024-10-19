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

class RowManager implements Htmlable
{

    /**
     * Row cell collections
     *
     * @var array
     */
    protected $cells = [];

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
     * @param $value
     * @param string|null $container
     * @param array $attributes
     * @return $this
     */
    public function addCell($value, $container = null, $attributes = [])
    {
        if (is_array($container)) {
            $attributes = $container;
            $container = null;
        }

        if (!isset($container)) {
            $container = 'head' === $this->type ? 'th' : 'td';
        }

        if ('head' === $this->type) {
            $value = array_get($attributes, 'title', $value);
        }

        $this->cells[] = Wa::html('element', $value, $container, $attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ([] !== $this->cells) {
            $s = '';
            foreach ($this->cells as $cell) {
                $s .= $cell->toHtml();
            }
            return $s;
        }
    }
}