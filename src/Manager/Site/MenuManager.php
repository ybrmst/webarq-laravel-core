<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/16/2017
 * Time: 1:14 PM
 */

namespace Webarq\Manager\Site;


use Html;
use Illuminate\Support\Arr;
use Request;
use URL;
use Wa;
use Webarq\Manager\TreeManager;
use Webarq\Model\MenuModel;

class MenuManager extends TreeManager
{
    /**
     * @var array
     */
    protected $originBranch = [];

    /**
     * @var array
     */
    protected $positionBranch = [];

    /**
     * Current position
     *
     * @var
     */
    protected $currentPosition;

    /**
     * Menu permalink(s)
     *
     * @var array
     */
    protected $permalinkNodes = [];

    /**
     * Active node menu
     *
     * @var array
     */
    protected $activesNode;

    /**
     * @var array
     */
    protected $segments;

    /**
     * @var array
     */
    protected $eloquentNodes = [];

    public function __construct($nodeKey = 'id', $parentKey = 'parent_id', $childKey = 'o-child')
    {
        parent::__construct($this->selectMenu($nodeKey), $nodeKey, $parentKey, $childKey);

        $this->positionBranching();
    }

    protected function selectMenu($nodeKey)
    {
        if (class_exists('App\Webarq\Model\MenuModel')) {
            $object = new \App\Webarq\Model\MenuModel();
        } else {
            $object = new MenuModel();
        }

        $builder = $object->from('menus as m')
                ->select('m.id', 'm.parent_id', 'm.external_link', 'm.template', 'p.position', 'm.lead')
                ->selectTranslate('m.title', 'm.permalink!', 'm.meta_title', 'm.meta_description', true)
                ->leftJoin('menu_positions as p', 'm.id', '=', 'p.menu_id')
                ->where('m.is_active', 1)
                ->orderBy('m.parent_id', 'ASC')
                ->orderBy('m.sequence', 'ASC')
                ->get();

        $nodes = [];

        if ($builder->count()) {
            foreach ($builder as $item) {
// Put item in to object nodes
                $this->eloquentNodes[$item->{$nodeKey}] = $item;

                $node = $item->toArray();

// Pair permalink node with it's key
                $this->permalinkNodes[$node['permalink']] = $node[$nodeKey];

                if (isset($node['permalink_lang'])) {
                    $this->permalinkNodes[Wa::getGhost()][$node['permalink_lang']] = $node[$nodeKey];
                }

                if (!isset($nodes[$node[$nodeKey]])) {
                    $node['position'] = [$node['position']];
                    $nodes[$node[$nodeKey]] = $node;
                } else {
                    $nodes[$node[$nodeKey]]['position'][] = $node['position'];
                }
            }
        }

        return $nodes;
    }

    protected function positionBranching()
    {
        if ([] === $this->branch) {
            return [];
        }

        $this->originBranch = $this->branch;

        $positions = config('webarq.menu.positions');

        if ([] !== $positions) {
            foreach ($this->branch as $index => $items) {
                foreach ($items as $node) {
                    foreach ($positions as $position => $lbl) {
                        if (in_array($position, array_get($this->nodes, $node . '.position', []))) {
                            $this->positionBranch[$position][$index][] = $node;
                        }
                    }
                }
            }
// Nodes with no positions
            $this->positionBranch['unplaced'][] = array_values(array_diff(
                    array_keys($this->nodes),
                    array_flatten($this->positionBranch)
            ));
        }
    }

    public function breadcrumb()
    {
        $actives = $this->getActive(true);
        $str = Html::link(URL::trans(''), Wa::trans('webarq:title.home'));

        if (!empty($actives)) {
            $last = array_shift($actives);

            while ([] !== $actives) {
                $node = array_pop($actives);
                if (null !== ($title = $this->getNode($node)->title)) {
                    $str .= ' &nbsp;/ ' . Html::link(URL::trans($this->getNode($node)->permalink), $title);
                }
            }

            if (null !== $last && null !== ($title = $this->getNode($last)->title)) {
                $str .= ' <span>/ ' . '</span> ' . $title;
            }
        } else {
            $str .= ' <span>/ ' . '</span> ' . title_case($this->path());
        }

        return $str;
    }

    /**
     * Get active menu
     *
     * @param bool $all
     * @return array|$this
     */
    public function getActive($all = false)
    {
        $path = $this->getSegment(0);

        if (null !== ($node = $this->getNodeByPermalink($path))) {
            if (false === $all) {
                $this->node = $node;
                return $this;
            }

            if (null === $this->activesNode) {
                $this->activesNode = [$node];

                while (0 !== ($parent = (int)array_get($this->nodes, $node . '.' . $this->parentKey))) {
                    $this->activesNode[] = $node = array_get($this->nodes, $parent . '.' . $this->nodeKey);
                }
            }

            return $this->activesNode;
        } elseif ('/' === $path && [] !== $this->nodes) {
            return [];
        }
    }

    public function getSegment($key, $default = 'x0x0x0')
    {
        if (null === $this->segments) {
            $path = Request::path();

            if (class_exists('Wl')) {
                if (null !== Request::segment(1) && in_array(Request::segment(1), \Wl::getCodes())) {
                    $path = trim(substr($path, strlen(Request::segment(1))), '/');
                }

                if ('' === $path) {
                    return '/';
                }
            }

            $this->segments = [$path];

            if ([] !== config('webarq.menu.markup-url')) {
                $index = 0;
                foreach (config('webarq.menu.markup-url') as $markup) {
                    $index2 = strpos($path, '/' . $markup . '/');
//                    In case path contains multiple markup key, the first one to be founded will be used.
                    if (false !== $index2 && 0 !== $index2 && (0 === $index || $index2 < $index)) {
                        $this->segments = explode('/' . $markup . '/', $path, 2);
                        $this->segments['markup'] = explode('/', array_get($this->segments, 1, ''));
                        $this->segments['markup']['key'] = $markup;
                        $index = $index2;
                    }
                }
            }
        }

        return array_get($this->segments, $key, $default);
    }

    protected function getNodeByPermalink($path)
    {
        return array_get($this->permalinkNodes, $path) ?:
                array_get($this->permalinkNodes, Wa::getGhost() . '.' . $path);
    }

    /**
     * @return mixed
     */
    protected function path()
    {
        $path = strtolower(Request::path());
        if ('/' !== $path && class_exists('Wl')) {
            foreach (\Wl::getCodes() as $code) {
                if ($code === $path) {
                    return '/';
                } elseif (0 === strpos($path, $code . '/')) {
                    return substr($path, 3);
                }
            }
        }

        return $path;
    }

    /**
     * Magic to get branch node by it's position
     *
     * @param $method
     * @param array $arg
     * @return mixed
     */
    public function __call($method, array $arg)
    {
        if (starts_with($method, 'generate')) {
            $method = strtolower(substr($method, 8));
            $generate = true;
        }

        $this->currentPosition = $method;

        if (!isset($this->compiled[$method])) {
            $this->compiled[$method] = [];

            if ([] !== ($branch = array_get($this->positionBranch, $method, []))) {
                $this->branch = $branch;

                foreach (current($branch) as $k => $node) {
                    $this->compiled[$method][$k] = clone $this;
                    $this->compiled[$method][$k]->node = $node;
                }
            }
        }

        $this->branch = $this->originBranch;

        if (isset($generate)) {
            array_unshift($arg, $this->compiled[$method]);

            return $this->generate(...$arg);
        }

        return $this->compiled[$method];
    }

    /**
     * @param array $items
     * @param bool|true $tree
     * @param array $options
     * @param int $level
     * @return string
     */
    public function generate(array $items, $tree = true, array $options = [], $level = 0)
    {
        $options += config('webarq.menu.wrapper', []);

        $str = '';

        if ([] !== $items) {
            $str1 = '';

            foreach ($items as $item) {
                $str2 = $this->anchorGenerator($item, array_get($options, 'anchor-attributes', []));

                $elem = array_get($options, 'inner-no-child', '<li></li>');

                if ($this->isActive($item->{$this->nodeKey})) {
                    $elem = array_get($options, 'inner-no-child-active', $elem);
                }

                if ([] !== ($child = $item->getChild()) && false !== $tree) {
                    $elem = array_get($options, 'inner-with-child', $elem);

                    if ($this->isActive($item->{$this->nodeKey})) {
                        $elem = array_get($options, 'inner-with-child-active', $elem);
                    }

                    $str2 .= $this->generate($child, $tree, $options, $level + 1);
                }

                $str1 .= Wa::html('element', $str2, $elem)->toHtml();
            }

            if (0 === $level || true === $tree) {
                $elem = array_get($options, 'outer-level-' . $level . '',
                        array_get($options, 'outer-level-n', '<ul></ul>'));

                $str .= Wa::html('element', $str1, $elem)->toHtml();
            } else {
                $str .= $str1;
            }
        }

        return $str;
    }

    protected function anchorGenerator(MenuManager $item, array $attr = [])
    {
        $attr['href'] = Url::trans($item->{'permalink_lang'} ?: $item->{'permalink'});

        if ($this->isActive($item->{$this->nodeKey})) {
            $attr = Arr::merge($attr, config('webarq.menu.wrapper.anchor-active-attributes', []));
        }

        return '<a' . Html::attributes($attr) . '>' . $item->{'title'} . '</a>';
    }

    /**
     * Check if menu node is active or not
     *
     * @param $id
     * @return bool
     */
    public function isActive($id = null)
    {
        return in_array($id ?: $this->{'id'}, $this->getActive(true) ?: []);
    }

    /**
     * Magic to access the node attributes
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (null !== $this->node) {
            return array_get($this->nodes, $this->node . '.' . $key);
        }

        $this->node = null;
    }

    public function localizePermalink($lang = null, $attr = [], $secure = null)
    {
        if (!$this->getActive()) {
            $u = trim(Request::getPathInfo(), '/');
        } else {
            if (class_exists('Wl') && $lang === \Wl::getSystem()) {
                $u = $this->eloquent()->getAttribute('permalink');
            } else {
                $u = $this->getPermalink();
            }

//            Join with the markup item
            $s = $this->getSegment('markup', []);

            if ([] !== $s && is_array($s)) {
                $u .= '/' . array_pull($s, 'key') . '/' . implode('/', $s);
            }
        }

        $u .= (Request::getQueryString() ? ('?' . Request::getQueryString()) : '');

        if (class_exists('Wl')) {
            if ('' !== $u) {
                foreach (\Wl::getCodes() as $c) {
                    if ($u === $c) {
                        return URL::trans('', $attr, $secure, $lang);
                    } elseif (starts_with($u, $c . '/')) {
                        return URL::trans(substr($u, 3), $attr, $secure, $lang);
                    } elseif (starts_with($u, $c . '?')) {
                        return URL::trans(substr($u, 2), $attr, $secure, $lang);
                    }
                }
            }
        }

        return URL::trans($u, $attr, $secure, $lang);
    }

    public function eloquent()
    {
        return array_get($this->eloquentNodes, $this->node);
    }

    public function getPermalink()
    {
        return $this->{'permalink_lang'} ?: $this->{'permalink'};
    }

    public function generatePermalink(array $attr = [])
    {
        if ($this->{'external_link'}) {
            $attr['href'] = $this->{'external_link'};
            $attr += [
                    'target' => '_blank',
                    'rel' => 'nofollow'
            ];
        } else {
            $attr['href'] = URL::trans($this->getPermalink());
        }

        if ($this->isActive($this->{$this->nodeKey})) {
            $attr = Arr::merge($attr, config('webarq.menu.wrapper.anchor-active-attributes', []));
        }

        return '<a' . Html::attributes($attr) . '>' . $this->{'title'} . '</a>';
    }
}