<?php
/**
 * Created by PhpStorm
 * Date: 05/02/2017
 * Time: 11:09
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\HTML\Form;


trait LaravelInputHint
{
    protected $inputHints = [
            'label' => ['name', 'value' => null, 'options' => [], 'escape_html' => null],
            'input' => ['type', 'name', 'value' => null, 'options' => []],
            'text' => ['name', 'value' => null, 'options' => []],
            'password' => ['name', 'options' => []],
            'hidden' => ['name', 'value' => null, 'options' => []],
            'email' => ['name', 'value' => null, 'options' => []],
            'tel' => ['name', 'value' => null, 'options' => []],
            'number' => ['name', 'value' => null, 'options' => []],
            'date' => ['name', 'value' => null, 'options' => []],
            'datetime' => ['name', 'value' => null, 'options' => []],
            'datetimeLocal' => ['name', 'value' => null, 'options' => []],
            'time' => ['name', 'value' => null, 'options' => []],
            'url' => ['name', 'value' => null, 'options' => []],
            'file' => ['name', 'options' => []],
            'textarea' => ['name', 'value' => null, 'options' => []],
            'select' => ['name', 'list' => [], 'selected' => null, 'options' => []],
            'selectRange' => ['name', 'begin', 'end', 'selected' => null, 'options' => []],
            'selectYear' => ['name', 'begin', 'end', 'selected' => null, 'options' => []],
            'selectMonth' => ['name', 'selected' => null, 'options' => [], 'format' => null],
            'checkbox' => ['name', 'value' => null, 'checked' => null, 'options' => []],
            'radio' => ['name', 'value' => null, 'checked' => null, 'options' => []],
            'reset' => ['value', 'attributes' => []],
            'image' => ['url', 'name' => null, 'attributes' => []],
            'color' => ['name', 'value' => null, 'options' => []],
            'submit' => ['value' => null, 'options' => []],
            'button' => ['value' => null, 'options' => []],
    ];
}