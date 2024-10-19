<?php
/**
 * Created by PhpStorm
 * Date: 05/12/2016
 * Time: 10:00
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\HTML;


use Form;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Wa;
use Webarq\Manager\HTML\Form\InputManager;
use Webarq\Manager\HTML\Form\LaravelInputHint;

/**
 * A helper class to generator form by adding default layout in to it
 *
 * Class FormManager
 * @package Webarq\Manager\HTML
 */
class FormManager implements Htmlable
{
    use LaravelInputHint;

    /**
     * Form attributes
     *
     * @var array
     */
    protected $attributes;

    /**
     * Form container
     *
     * @var string
     */
    protected $container;

    /**
     * Form title
     *
     * @var object Webarq\Manager\HTML\ContainerManager
     */
    protected $title;

    /**
     * Form message
     *
     * @var object Webarq\Manager\HTML\ContainerManager
     */
    protected $message;

    /**
     * Form inputs
     *
     * @var array
     */
    protected $inputs = [];

    /**
     * Input separator
     *
     * @var array
     */
    protected $br = [];

    /**
     * Default html title container
     *
     * @var
     */
    protected $elementTitleDefaultContainer;

    /**
     * Default html information container
     * @var
     */
    protected $elementInfoDefaultContainer;

    /**
     * Form submit button
     *
     * @var
     */
    protected $submit;

    /**
     * Create FormManager instance
     *
     * @param null $action
     * @param array $attributes
     * @param string $container
     */
    public function __construct($action = null, $attributes = [], $container = 'div')
    {
        if (is_array($action)) {
            $attributes = $action;
            $action = null;
        }

        $this->attributes = ['url' => $action] + (is_array($attributes) ? $attributes : ['id' => $attributes]);

        $this->container = $container;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setAction($url)
    {
        $this->attributes['url'] = $url;

        return $this;
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function setAttribute($key, $value = null)
    {
        if (is_array($key)) {
            $this->attributes = Arr::merge($this->attributes, $key);
        } elseif (false !== strpos($key, '.')) {
            array_set($this->attributes, $key, $value);
        } else {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getAttribute($key, $default = null)
    {
        return array_get($this->attributes, $key, $default);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function pullAttribute($key, $default = null)
    {
        return array_pull($this->attributes, $key, $default);
    }

    /**
     * Set form title
     *
     * @param $title
     * @param string $container
     * @param array $attr
     * @return $this
     */
    public function setTitle($title, $container = 'h3', array $attr = [])
    {
        if (is_array($container)) {
            $attr = $container;
            $container = 'h3';
        }
        $this->title = Wa::html('element', $title, $container, $attr);

        return $this;
    }

    /**
     * Build form message
     *
     * @param array|string $messages
     * @param string $level
     * @param string $container
     */
    public function setMessages($messages = [], $level = 'warning', $container = ':webarq.form.cms.message')
    {
        $html = $messages;
        if (is_array($messages)) {
            $html = '<ul>';
            foreach ($messages as $message) {
                $html .= '<li>' . $message . '</li>';
            }
        }
        $this->message = Wa::html('element', $html, $container, [
                'level' => $level
        ]);
    }

    /**
     * Set html title default container
     *
     * @param $container
     * @return $this
     */
    public function setElementTitleDefaultContainer($container)
    {
        $this->elementTitleDefaultContainer = $container;

        return $this;
    }

    /**
     * Set html information default container
     *
     * @param $container
     * @return $this
     */
    public function setElementInfoDefaultContainer($container)
    {
        $this->elementInfoDefaultContainer = $container;

        return $this;
    }

    /**
     * Insert <br/> tag between each inputs
     *
     * @param string $tag
     * @return $this
     */
    public function addBr($tag = 'br')
    {
        if (!isset($this->br['title']) && isset($this->title)) {
            $this->br['title'] = new ElementManager('', $tag);
        } elseif (isset($this->inputs[0])) {
            $this->br[count($this->inputs) - 1] = new ElementManager('', $tag);
        }

        return $this;
    }

    /**
     * Add multiple input at once
     *
     * @param array $inputs
     * @see setInputManager()
     */
    public function addCollectionGroup(array $inputs = [])
    {
        $inputs = func_get_args();
        foreach ($inputs as $i => &$input) {
            if (is_array($input[0])) {
                $input = $this->setInputManager(
                        $input[0], array_get($input, 1), array_get($input, 2), array_get($input, 3));
            } elseif (is_array($input)) {
                $input = $this->setInputManager($input);
            } else {
                $inputs['container'] = $input;
                unset($inputs[$i]);
            }
        }
        $this->inputs[] = $inputs;
    }

    /**
     * Get input manager
     *
     * @param $args
     * @param null $title
     * @param null $info
     * @param null $container
     * @return InputManager
     */
    private function setInputManager($args, $title = null, $info = null, $container = null)
    {
// Initiate InputManager class
        $manager = new InputManager(
                $args, $title ?: ucwords(snake_case(camel_case(array_get($args, 1)), ' ')), $info,
                array_filter([
                        'input' => $container,
                        'title' => $this->elementTitleDefaultContainer,
                        'info' => $this->elementInfoDefaultContainer]));

        return $manager;
    }

    /**
     * Add single or multiple input
     *
     * Deliberately this method will call related laravel form builder method (eg. Form::text, Form::radio, etc)
     * following by it is original parameter
     *
     * $f->addCollection(['text', 'codename', 'value'], 'Code Name', 'Get before i take it from you', 'div');
     * $f->addCollection('text', 'codename', 'value')
     *  ->setTitle('Code Name')
     *  ->setInfo('Get before i take it from you')
     *  ->setContainer('div);
     *
     * Multiple element
     * $f->addCollectionGroup(
     *  [['text', 'codename', 'value'], 'Code Name', 'Get before i take it from you', 'div']
     *  ['text', 'weapon', 'trident', function($ipt){
     *      $ipt->setTitle('Weapon')->setInfo('Select your weapon')
     *  }]
     * )
     *
     * @param mixed $args
     * @param mixed $title
     * @param mixed $info
     * @param mixed $container
     * @return InputManager|null
     */
    public function addCollection($args = [], $title = null, $info = null, $container = null)
    {
        $args = func_get_args();
        if (is_array($args[0])) {
            if (!is_array($title)) {
                return $this->inputs[] = $this->setInputManager($args[0], $title, $info, $container);
            } else {
                call_user_func_array(array($this, 'addCollectionGroup'), $args);
            }
        } else {
            return $this->inputs[] = $this->setInputManager($args);

        }
    }

    /**
     * Set form submit
     *
     * @param submit
     * @return $this
     */
    public function submit($string)
    {
        $this->submit = $string;

        return $this;
    }

    /**
     * Generate well formatted html form element
     *
     * @param string $before
     * @param string $after
     * @return mixed|string
     */
    public function toHtml($before = '', $after = '')
    {
        $s = '';
        if ([] !== $this->inputs) {
            if (isset($this->title)) {
                $s .= $this->title->toHtml();
                if (null !== ($br = array_get($this->br, 'title'))) {
                    $s .= $br->toHtml();
                }
            }

            if (isset($this->message)) {
                $s .= $this->message->toHtml();
                if (null !== ($br = array_get($this->br, 'message'))) {
                    $s .= $br->toHtml();
                }
            }

            return (new ElementManager($s . $before . $this->compile() . $after, $this->container))->toHtml();
        } else {
            $s = config('webarq.system.frm-configuration-error',
                    'Inputs not provided yet. Please support me by doing the right thing :)');
        }
        return $s;
    }

    /**
     * Compile inputs
     *
     * @return string
     */
    private function compile()
    {
        $s = Form::open($this->attributes);
        foreach ($this->inputs as $i => $collection) {
            if (is_array($collection)) {
                $s .= $this->compileGroup($collection);
            } else {
                $s .= $collection->toHtml();
            }
            if (null !== ($br = array_get($this->br, $i))) {
                $s .= $br->toHtml();
            }
        }
        if (!isset($this->submit)) {
            $s .= Wa::html('element', Form::submit('Submit'))->toHtml();
        } else {
            $s .= $this->submit;
        }
        return $s . Form::close();
    }

    /**
     * Compile group inputs
     *
     * @param array $collections
     * @return string
     */
    private function compileGroup(array $collections)
    {
        $s = '';
        $container = array_pull($collections, 'container');
        foreach ($collections as $collection) {
            $s .= $collection->toHtml();
        }

        return isset($container) ? Wa::element($s, $container, ['length' => count($collections)]) : $s;
    }

    /**
     * @param null|mixed $name
     * @return mixed
     */
    public function getInput($name = null)
    {
        return array_get($this->inputs, $name);
    }
}
