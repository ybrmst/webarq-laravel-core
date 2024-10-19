<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/9/2017
 * Time: 6:14 PM
 */

namespace Webarq\Manager\Cms;


use Wa;
use Webarq\Manager\AdminManager;
use Webarq\Model\NoModel;

class RuleManager
{
    /**
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var array|callback
     */
    protected $rules;

    /**
     * @var array
     */
    protected $items = [];

    protected $operator = [
            '===' => 'isIdentical',
            '==' => 'isEqual',
            '!==' => 'isNotIdentical',
            '!=' => 'isNotEqual',
            '>=' => 'isGreaterEqual',
            '>' => 'isGreater',
            '<=' => 'isLowerEqual',
            '<>' => 'isLowerGreater',
            '<' => 'isLower',
    ];

    protected $table;

    protected $action;

    /**
     * @param AdminManager $admin
     * @param array|callback $rules
     * @param array $items
     * @param mixed $table
     * @param mixed $action
     */
    public function __construct(AdminManager $admin, $rules = [], $items = [], $table = null, $action = null)
    {
        $this->admin = $admin;
        $this->rules = !is_string($rules) && is_callable($rules) ? $rules : (array)$rules;
        $this->items = (array)$items;
        $this->table = $table;
        $this->action = $action;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (!is_string($this->rules) && is_callable($this->rules)) {
            return call_user_func_array($this->rules, [$this->admin, $this->items]);
        } elseif ([] === $this->rules || [] === $this->items) {
            return true;
        } elseif (is_array($this->rules) && [] !== $this->rules) {
            $and = false;
            if (true === last($this->rules)) {
                $and = true;
                array_pop($this->rules);
            }

            $valid = $this->checkChildRow($this->rules);
			
            if (!$valid && true === $and) {
                return false;
            }

            if ([] !== $this->rules) {
                foreach ($this->rules as $key => $value) {
                    if (!is_string($value) && is_callable($value)) {
                        $valid = $value($this->admin, $this->items);
                    } elseif (is_numeric($key)) {
                        $valid = $this->groupRules($value);
                    } else {
                        $valid = $this->compareValue($this->getValue($key), $this->getValue($value));
                    }

                    if (true === $and && !$valid) {
// All rules should be valid
                        return false;
                    } elseif ($valid) {
// One is enough for us
                        return true;
                    }
                }
            } else {
                return $valid;
            }
        }

        return false;
    }

    /**
     * @param array $groups
     * @return bool
     */
    protected function groupRules(array $groups)
    {
        if ([] !== $groups) {
            foreach ($groups as $key => $value) {
                if (!is_string($value) && is_callable($value)) {
                    $valid = $value($this->admin, $this->items);
                } else {
                    $valid = $this->compareValue($this->getValue($key), $this->getValue($value));
                }

                if (!$valid) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check for child rules
     *
     * @param array $rules
     * @return bool
     */
    protected function checkChildRow(array &$rules)
    {
        $child = array_pull($rules, 'has-child');
        $parent = array_pull($rules, 'parent-column');

        if (true !== $child && null !== $parent && null !== $this->table) {
            $id = array_pull($rules, 'primary-column', Wa::table($this->table)->primaryColumn()->getName());
            return null === NoModel::instance($this->table, $id)
                    ->where($parent, array_get($this->items, $id))
                    ->first();
        }
        return true;
    }

    /**
     * @param $left
     * @param $right
     * @param string $operator
     * @return bool
     */
    protected function compareValue($left, $right, $operator = '===')
    {
        if (is_array($right) && isset($this->operator[$right[0]])) {
            $operator = array_pull($right, 0);
            if (is_array($right) && 1 === count($right)) {
                $right = $this->getValue(array_shift($right));
            }
            if ('edit' === $this->action) {

            }
        }

        if (!is_array($left) && is_array($right)) {
            return in_array($left, $right);
        } else {
            return $this->{$this->operator[$operator]}($this->safeValue($left), $this->safeValue($right));
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function getValue($value)
    {
        if (is_string($value) && str_contains($value, '.')) {
            list($property, $path) = explode('.', $value, 2);
            $method = 'get' . ucfirst(strtolower($property));
            if (method_exists($this, $method)) {
                $value = $this->{$method}($path);
            }
        }

        return $this->safeValue($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function safeValue($value)
    {
        return is_numeric($value) ? (str_contains($value, '.') ? (float)$value : (int)$value) : $value;
    }

    /**
     * @param $left
     * @param $right
     * @return bool
     */
    protected function isEqual($left, $right)
    {
        return $left == $right;
    }

    protected function isNotEqual($left, $right)
    {
        return $left != $right;
    }

    protected function isIdentical($left, $right)
    {
        return $left === $right;
    }

    protected function isNotIdentical($left, $right)
    {
        return $left !== $right;
    }

    protected function isGreater($left, $right)
    {
        return $left > $right;
    }

    protected function isGreaterEqual($left, $right)
    {
        return $left >= $right;
    }

    protected function isLower($left, $right)
    {
        return $left < $right;
    }

    protected function isLowerEqual($left, $right)
    {
        return $left <= $right;
    }

    protected function isLowerGreater($left, $right)
    {
        return $left <> $right;
    }

    /**
     * @param $key
     * @return array|mixed|number
     */
    protected function getAdmin($key)
    {
        if ('level' === $key) {
            return $this->admin->getLevel(true);
        } else {
            return $this->admin->getProfile($key);
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getItem($key)
    {
        return array_get($this->items, $key);
    }

}