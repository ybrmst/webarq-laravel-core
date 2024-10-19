<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 6:14 PM
 */

namespace Webarq\Manager\Cms\HTML\Form\Input;


use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class PasswordInputManager extends AbstractInput
{
    protected function buildInput()
    {
        return \Form::password($this->name, $this->attribute->toArray());
    }
}