<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/13/2017
 * Time: 1:08 PM
 */

namespace Webarq\Manager\Tree;


use Illuminate\Support\Arr;
use Webarq\Manager;

class RawTreeManager extends Manager\TreeManager
{
    protected $collections = [];

    /**
     * @param array $raw Indexed array of raw data
     * @param string $parent Parent column name
     * @param string $index Column to check
     */
    public function __construct(array $raw, $parent = 'parent_id', $index = 'id')
    {
        $this->index = $index;
        $this->parent = $parent;

        parent::__construct($this->makeCollectionsFromRaw($raw), $parent, $index);
    }

    public function makeCollectionsFromRaw(array $raw)
    {
        $raw = array_merge($raw, [
                ['name' => 'x'],
                ['name' => 'u']
        ]);
        if ([] !== $raw) {
            $collections = [];
            $groups = [];
            foreach ($raw as $item) {
                if (null !== ($p = array_get($item, $this->parent))) {
                    if (!isset($item[$this->index])) {
                        $groups[$p]['orphan'][] = $item;
                    } else {
                        $groups[$p][] = $item;
                    }
                } else {
                    $collections[] = $item + ['o-child' => []];
                }
            }

// Sort by parent
            ksort($groups);
// Get first groups key
            $parent = Arr::firstKey($groups);
// Compile
            return Arr::merge($collections, $this->rawCompiler($groups, $parent));
        }
    }

    protected function rawCompiler(array &$groups, $parent, $level = 0)
    {
        $collections = $this->setChild($groups, $parent, $level);

        if ([] !== $groups) {
            $parent = Arr::firstKey($groups);
            $collections = array_merge($collections, $this->rawCompiler($groups, $parent, $level));
        }

        return $collections;
    }

    protected function setChild(array &$groups, $parent, $level = 0)
    {
        $collections = [];

        if ([] !== ($items = array_pull($groups, $parent, []))) {
            foreach ($items as $key => $item) {
                if ('orphan' !== $key) {
                    $item['o-level'] = $level;
                    $item['o-child'] = $this->setChild($groups, array_get($item, $this->index), $level + 1);
                } else {
                    foreach ($item as $orphan) {
                        $collections[] = $orphan + ['o-child' => []];
                    }
                    continue;
                }

                $collections[] = $item;
            }
        }

        return $collections;
    }
}