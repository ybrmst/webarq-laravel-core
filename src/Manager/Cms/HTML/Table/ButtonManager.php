<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/9/2017
 * Time: 10:46 AM
 */

namespace Webarq\Manager\Cms\HTML\Table;


use Illuminate\Contracts\Support\Htmlable;
use Webarq\Manager\SetPropertyManagerTrait;

class ButtonManager implements Htmlable
{
    use SetPropertyManagerTrait;

    protected $attributes = [];

    protected $containerView;

    protected $type;

    protected $permalink;

    protected $label;

    protected $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;

        $this->setPropertyFromOptions($settings);

        if ([] !== $settings) {
            foreach ($settings as $key => $value) {
                if (!is_array($value) && !is_numeric($key)) {
                    $this->attributes[$key] = $value;
                }
            }
        }

        $this->setAttributes();
    }

    protected function setAttributes()
    {
        $this->attributes['alt'] = $this->label ?: $this->type;

        if (!isset($this->attributes['class'])) {
            $this->attributes['class'] = 'fa btn';
        } else {
            $this->attributes['class'] .= ' fa btn';
        }

        $fa = '';
        switch ($this->type) {
            case 'edit':
                $fa .= ' fa-edit';
                break;
            case 'activeness':
                $fa .= ' fa-rocket';
                break;
            case 'delete':
                $fa .= ' fa-eraser';
                break;
            case 'create':
                $fa .= ' fa-plus-square';
                break;
            case 'export':
            case 'download':
                $fa .= ' fa-download';
                break;
        }

        $this->attributes['class'] .= $fa . ' ' . array_pull($this->attributes, 'icon');
    }

    public function toHtml()
    {
        if (!empty($this->containerView)) {
            return view($this->containerView, $this->settings);
        }

        return \Html::link($this->permalink, $this->makeLabel(), $this->attributes)
        . '&nbsp; &nbsp;';
    }

    protected function makeLabel()
    {
        $l = ' ' . ucfirst($this->label ?: $this->type);

        return $l;
    }
}