<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 6:49 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class TextareaInputManager extends AbstractInput
{
    protected function buildInput()
    {
        return \Form::textarea($this->name, $this->value, $this->attribute->toArray());
    }
}