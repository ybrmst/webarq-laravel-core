<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/13/2017
 * Time: 12:39 PM
 */

namespace Webarq\Manager;


class TreeManager
{
    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var array
     */
    protected $branch = [];

    /**
     * @var
     */
    protected $node;

    /**
     *
     * @var
     */
    protected $nodeKey;

    /**
     * @var
     */
    protected $parentKey;

    /**
     * @var
     */
    protected $childKey;

    /**
     * @var array
     */
    protected $compiled = [];

    /**
     * Node level collection
     *
     * @var array
     */
    protected $levels = [];

    public function __construct(array $items, $nodeKey = 'id', $parentKey = 'parent_id', $childKey = 'o-child')
    {
        $this->nodeKey = $nodeKey;
        $this->childKey = $childKey;
        $this->parentKey = $parentKey;

        $this->setNodes($items);
    }

    /**
     * Get root nodes
     *
     * @return array|mixed
     */
    public function root()
    {
        if (!isset($this->compiled['root'])) {
            $this->compiled['root'] = [];
            $nodes = current($this->branch);

            if (is_array($nodes) && [] !== $nodes) {
                foreach ($nodes as $k => $item) {
                    $this->compiled['root'][$k] = clone $this;
                    $this->compiled['root'][$k]->getNode(is_array($item[$this->nodeKey]) ? $item[$this->nodeKey] : $item);
                }
            }
        }

        return $this->compiled['root'];
    }

    /**
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    protected function setNodes(array $items)
    {
        if ([] !== $items) {
            $i = 0;
            foreach ($items as $item) {
                if (null === ($node = array_get($item, $this->nodeKey))) {
                    $node = 'orphan-' . $i;
                    $i++;
                }

                $this->nodes[$node] = $item;
            }

            ksort($this->nodes);

            $this->branching($items);
        }
    }

    /**
     * Check whether a node has child node or not
     *
     * @param null $node
     * @return bool
     */
    public function hasChild($node = null)
    {
        return isset($this->branch[$node ?: $this->node]);
    }

    /**
     * Get the node
     *
     * @param $node
     * @return $this
     */
    public function getNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @return $this|array|null|TreeManager
     */
    public function first()
    {
        return $this->getChild('first');
    }

    /**
     * Get child(s) of the node
     *
     * @param null $node
     * @return $this|array|null
     */
    public function getChild($node = null)
    {
        if (null !== $this->node) {
            $child = $this->getCompile('child-' . $this->node, $this->getMyBranch($this->node));

            if (null === $node) {
// Return all child				
                return $child;
            } elseif ('first' === $node || 'last' === $node) {
// Get first child item
                $item = 'first' === $node ? array_first($child) : array_last($child);
                if (!is_null($item)) {
                    $clone = clone $this;
                    $clone->node = $item->node;

                    return $clone;
                }

                return null;
            } elseif (isset($child[$node])) {
                $this->node = $node;

                return $this;
            }

            return [];
        } elseif (null !== $node) {
            return $this->getCompile('child-' . $node, $this->getMyBranch($node));
        }
    }

    /**
     * Grouping our nodes by given key
     *
     * @param $key
     * @param array $nodes
     * @return array|mixed
     */
    protected function getCompile($key, array $nodes)
    {
        if (null === array_get($this->compiled, $key)) {
            $this->compiled[$key] = [];

            if ([] !== $nodes) {
                foreach ($nodes as $node) {
//                    $this->compiled[$key][$node] = array_get($this->nodes, $node, []);
                    $this->compiled[$key][$node] = clone $this;
                    $this->compiled[$key][$node]->node = $node;
                }
            }
        }

        return $this->compiled[$key];
    }

    /**
     * @param $node
     * @param array $forget
     * @return mixed
     */
    protected function getMyBranch($node, $forget = [])
    {
        if (null === $node) {
            return [];
        }

        $nodes = array_get($this->branch, $node, []);

        if ([] !== $forget) {
            foreach ($nodes as $i => $v) {
                if (in_array($v, $forget)) {
                    unset($nodes[$i]);
                }
            }

            if ([] !== $nodes) {
                $nodes = array_values($nodes);
            }
        }

        return $nodes;
    }

    public function last()
    {
        return $this->getChild('last');
    }

    /**
     * Get sibling(s) of the node
     *
     * @param null $node
     * @return mixed
     */
    public function getSibling($node = null)
    {
        if (null !== $this->node) {
            $siblings = $this->getCompile(
                    'siblings-' . $this->node,
                    $this->getMyBranch($this->getParentNode($this->node), true === $node ? [] : [$this->node])
            );

            if (null === $node) {
                return $siblings;
            } elseif (is_numeric($node) && isset($siblings[$node])) {
                $this->node = $node;

                return $this;
            }
            return $siblings;
        } elseif (null !== $node) {
            return $this->getCompile('siblings-' . $node, $this->getMyBranch($this->getParentNode($node), [$node]));
        }
    }

    protected function getParentNode($node)
    {
        return array_get($this->nodes, $node . '.' . $this->parentKey);
    }

    /**
     * Get parent of the node. To get the very top level of the selected node, set $upper to true
     *
     * @param integer|true $upper
     * @return $this|array
     */
    public function getParent($upper = 1)
    {
        if (null !== $this->node) {
            if (is_numeric($upper)) {
                for ($i = 1; $i <= $upper; $i++) {
                    $this->node = $this->getParentNode($this->node);
                }
            } elseif (true === $upper) {
                while (null !== ($node = $this->getParentNode($this->node)) && is_numeric($node) && $node > 0) {
                    $this->node = $node;
                }
            }
        } else {
            return [];
        }

        return $this;
    }

    /**
     * Get node level.
     * Level is starting from 0
     *
     * @param mixed $node
     * @return int|null
     */
    public function getLevel($node = null)
    {
        if (null === $node) {
            $node = $this->node;
        }

        if (null !== $node && !isset($this->levels[$node])) {
            $level = 0;
            while (true) {
                $node = array_get($this->nodes, $node . '.' . $this->parentKey);
                if (null === $node) {
                    break;
                }
                $level++;
            }

            return $this->levels[$node] = $level;
        }

        return array_get($this->levels, $node);
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

    protected function branching(array $items)
    {
        $i = 0;
        while ([] !== $items) {
            $item = array_shift($items);
            $parent = array_get($item, $this->parentKey, 'orphan');

            if (null === ($node = array_get($item, $this->nodeKey))) {
                $node = 'orphan-' . $i;
                $i++;
            }

            $this->branch[isset($this->nodes[$parent]) ? $parent : 0][$node] = $node;
        }
        ksort($this->branch);
    }
}