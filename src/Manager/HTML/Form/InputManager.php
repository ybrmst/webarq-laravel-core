<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/8/2016
 * Time: 4:37 PM
 */

namespace Webarq\Manager\HTML\Form;


use Illuminate\Contracts\Support\Htmlable;
use Webarq\Manager\Cms\HTML\Form\RulesManager;
use Webarq\Manager\HTML\ElementManager;

class InputManager implements Htmlable
{
    protected $input;

    /**
     * @var object ContainerManager
     */
    protected $title;

    /**
     * @var object ContainerManager
     */
    protected $info;

    /**
     * Default container
     *
     * @var array
     */
    protected $containers = [
            'info' => '<small class="help-block"></small>',
            'input' => '<div class="form-group"></div>',
            'title' => '<label class="control-label"></label>'
    ];

    /**
     * Input type
     *
     * @var
     */
    protected $type;

    /**
     * @param array $args
     * @param null $title
     * @param null $info
     * @param string|array $container
     */
    public function __construct(array $args, $title = null, $info = null, $container = '<div class="form-group"></div>')
    {
        foreach ($args as $i => $arg) {
            if (!is_string($arg) && is_callable($arg)) {
// Remove callback from $args
                unset($args[$i]);
// $args should only contain one callback item
                break;
            }
        }
// Forgot rules attribute
        array_forget($args, 'options.rules');
// Shift input type
        $this->type = array_shift($args);
// Set input by calling laravel form type method
        $this->input = call_user_func_array(array(app('form'), $this->type), $args);
// Warning!!!
// Do not change code sequence
        $this->setContainer($container, 'input');
        $this->setTitle($title ?: title_case(array_get($args, 0)));
        $this->setInfo($info);
        if (is_callable($arg)) {
            $arg($this);
        }
    }

    /**
     * Decoration function.
     * Set container decoration
     *
     * @param mixed $value
     * @param string $key
     * @return InputManager
     */
    public function setContainer($value, $key = 'input')
    {
        if (!is_null($value)) {
            if (!is_array($value)) {
                $this->containers[$key] = $value;
            } else {
                $this->containers = $value + $this->containers;
            }
        }

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Decoration function.
     * Set title decoration
     *
     * @param mixed $value
     * @param null|string $container Html tag name or full html tag (with any attributes)
     * @return InputManager
     */
    public function setTitle($value, $container = null)
    {
        $this->title = new ElementManager($value, $container ?: $this->containers['title']);

        return $this;
    }

    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Decoration function.
     * Set info decoration
     *
     * @param mixed $value
     * @param string $container Html tag name or full html tag (with any attributes)
     * @return InputManager
     */
    public function setInfo($value, $container = null)
    {
        $this->info = new ElementManager($value, $container ?: $this->containers['info']);

        return $this;
    }

    public function toHtml()
    {
        if (isset($this->title) && $this->title instanceof ElementManager) {
            $this->title = $this->title->toHtml();
        }
        if (isset($this->info) && $this->info instanceof ElementManager) {
            $this->info = $this->info->toHtml();
        }
        $input = $this->input->toHtml();
        if (starts_with($this->containers['input'], ':')) {
            return view(substr($this->containers['input'], 1), [
                    'title' => $this->title,
                    'info' => $this->info,
                    'input' => $input
            ]);
        } elseif ('hidden' === $this->type) {
            return $input;
        } else {
            return (new ElementManager($this->title . $input . $this->info, $this->containers['input']))
                    ->toHtml();
        }
    }
}