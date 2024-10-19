<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/16/2017
 * Time: 3:04 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Wa;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class DefaultInputManager extends AbstractInput
{
    /**
     * @inheritdoc
     */
    protected function buildInput()
    {
        return app('form')->{$this->type ?: 'text'}($this->name, $this->value, $this->attribute->toArray());
    }

}