<?php
/**
 * Created by PhpStorm
 * Date: 05/12/2016
 * Time: 10:21
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Manager\HTML;


use Html;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;


/**
 * Helper class
 * Generate formatted html element by given $content, $container, and $attr
 *
 * Class ElementManager
 * @package Webarq\Manager\HTML
 */
class ElementManager implements Htmlable
{
    /**
     * HTML with content
     *
     * @var string
     */
    protected $html = '';

    /**
     * HTML tag|elements without content
     *
     * @var
     */
    protected $container;

    /**
     * HTML element attributes
     *
     * @var array
     */
    protected $attr = [];

    /**
     * Empty HTML tag
     *
     * @var array
     */
    protected $void = ['br', 'hr', 'img', 'input', 'button'];

    /**
     * Create ElementManager instance
     *
     * @param $content
     * @param string $container HTML tag|elements without content
     * @param array $attr Html attributes|view variable if $container is a view file
     */
    public function __construct($content, $container = 'div', array $attr = [])
    {
        $this->setHtml($content);
        $this->setContainer($container);
        $this->setAttr($attr);
    }

    /**
     * Set element content
     *
     * @param $html
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Set HTML container attributes
     *
     * @param array $attr
     * @return $this
     */
    public function setAttr($attr = [])
    {
        if (2 === count($args = func_get_args())) {
            $attr = [$args[0] => $args[1]];
        } elseif (!is_array($attr)) {
            $attr = ['id' => $attr];
        }

        $this->attr = $attr;

        return $this;
    }

    /**
     * Get element container
     *
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set HTML container
     *
     * @param $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Create HTML element
     *
     * @return string
     */
    public function toHtml()
    {
        array_forget($this->attr, ['modifier', 'render']);

        $this->compile($this->html, $this->container, $this->attr);

        return $this->html;
    }

    /**
     * Compile given $html, $container and $attr
     *
     * @param string $html
     * @param string $container
     * @param array $attr
     * @throws \Exception
     * @throws \Throwable
     */
    protected function compile($html, $container, array $attr)
    {

        if ('' === $container || null === $container) {
            $this->html = $html;
        } elseif (Str::startsWith($container, ':')) {
            $this->html = view(substr($container, 1), ['html' => $html] + $attr)->render();
        } elseif (str_contains($container, ':html')) {
            $this->html = str_replace(':html', $html, $container);
        } elseif (false !== ($midPoint = strpos($container, '></'))) {
            $midPoint++;
            $this->html = substr($container, 0, $midPoint) . $html . substr($container, $midPoint);
        } else {
            foreach ($attr as &$val) {
                if (is_array($val)) {
                    $val = serialize($val);
                }
            }

            $this->html = $this->buildContent($html, $container, $attr);
        }
    }

    /**
     * Generate well formatted html element
     *
     * @param $html
     * @param $tag
     * @param array $attr
     * @return string
     */
    protected function buildContent($html, $tag, $attr = [])
    {
// Splits tag
        $tags = explode('>', $tag);
// Split class attributes
        $this->checkForBasicAttributes(array_shift($tags), $tag, $attr);
        if (!in_array($tag, $this->void)) {
// Open tag
            $o = '<' . $tag . Html::attributes($attr) . '>';
// Close tag
            $c = '</' . $tag . '>';
        } else {
            $o = '<' . $tag . '/>';
            $c = '';
        }

        if ([] !== $tags) {
            foreach ($tags as $str) {
                $attr = [];
                $this->checkForBasicAttributes($str, $tag, $attr);
                if (!in_array($tag, $this->void)) {
                    $o .= '<' . $tag . Html::attributes($attr) . '>';
                    $c = '</' . $tag . '>' . $c;
                } else {
                    $o .= '<' . $tag . '/>';
                }
            }
        }

        return $o . $html . $c;
    }

    /**
     * Check if given tag contain class and id attributes
     *
     * @param $string
     * @param $tag
     * @param array $attr
     */
    protected function checkForBasicAttributes($string, &$tag, &$attr = [])
    {
        $string = trim($string);
// Separate class from tag
        $class = strpos($string, '.');
        $id = strpos($string, '#');
        if (false !== $class) {
            if (false !== $id) {
// Class attribute found before id
                if ($id > $class) {
                    list($tag, $string) = explode('.', $string, 2);
                    list($attr['class'], $attr['id']) = explode('#', $string, 2);
                } else {
                    list($tag, $string) = explode('#', $string, 2);
                    list($attr['id'], $attr['class']) = explode('.', $string, 2);
                }
            } else {
                list($tag, $attr['class']) = explode('.', $string, 2);
            }
        } elseif (false !== $id) {
            list($tag, $attr['id']) = explode('#', $string, 2);
        } else {
            $tag = $string;
        }
    }
}