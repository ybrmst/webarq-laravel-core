<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/15/2018
 * Time: 3:11 PM
 */

namespace Webarq\Manager;


use Html;
use Wa;

class MetaManager
{
    use SingletonManagerTrait;

    protected $meta = [];

    protected $sharer = [
            'fb' => 'https://www.facebook.com/sharer/sharer.php?u=', // Facebook
            'tw' => 'https://twitter.com/home?status=', // Twitter
            'gp' => 'https://plus.google.com/share?url=', // Google Plus
            'ln' => 'https://www.linkedin.com/shareArticle?mini=true&url=', // Linkedin
    ];

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->meta += $key;
        } else {
            $this->meta[$key] = $value;
        }

        return $this;
    }

    public function share($type, array $attr = [], $tag = 'a')
    {
        $attr += [
                'target' => '_blank',
                'rel' => 'nofollow'
        ];

        if (!isset($attr['href']) && isset($this->sharer[$type])) {
            $attr['href'] = $this->sharer[$type] . request()->fullUrl();
        }

        return '<' . $tag . Html::attributes($attr) . '>' . $type . '</' . $tag . '>';
    }

    /**
     * Og meta builder
     *
     * @param array $elements
     * @return string
     */
    public function og(array $elements = [])
    {
        $html = '';
        $elements += $this->meta;

        foreach ($elements as $key => $value) {
            $html .= '<meta property="og:' . $key . '" content="'
                    . Wa::modifier()->legacy_html_entity_decode((strip_tags($value))) . '"/>';
        }

        return $html;
    }
}