<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 4:01 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Illuminate\Support\Arr;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class SelectInputManager extends AbstractInput
{
    /**
     * @var array|callable $options
     */
    protected $options = ['Off', 'On'];

    /**
     * @var
     */
    protected $blankOption;

    /**
     * @var array
     */
    protected $needs = [];

    /**
     * Set <option> tags attributes besides value and select
     *
     * @var array
     */
    protected $optionAttributes = [];

    public function getDefault($key = false)
    {
        return null !== $this->default
                ? $this->default : array_first(true === $key ? array_keys($this->options) : $this->options);
    }

    protected function buildInput()
    {
// Options should be array
        if (!is_array($this->options)) {
            $this->options = [$this->options];
        }

        if (!is_string($this->options) && is_callable($method = $this->options)) {
            $this->options = $method($this->value);
        } else {
            $this->setBlankOption();
        }

        return \Form::select($this->name, $this->options, $this->value, $this->attribute->toArray(),
                $this->optionAttributes);
    }

    protected function setBlankOption()
    {
        if (null !== $this->blankOption) {
            if (true === $this->blankOption || 1 === $this->blankOption || '1' === $this->blankOption) {
                $this->options = Arr::merge(['' => config('webarq.system.input.blank-option-label')], $this->options);
            } else {
                $this->options = Arr::merge((array)$this->blankOption, $this->options);
            }
        }
    }
}