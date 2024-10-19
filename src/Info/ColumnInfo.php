<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/25/2016
 * Time: 5:59 PM
 */

namespace Webarq\Info;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Wa;
use Webarq\Manager\setPropertyManagerTrait;

/**
 * Helper class
 *
 * Class ColumnInfo
 * @package Webarq\Info
 */
class ColumnInfo
{
    use SetPropertyManagerTrait;

    /**
     * Column comment
     *
     * @var string
     */
    protected $comment;

    /**
     * Column length
     *
     * @var number
     */
    protected $length;

    /**
     * Column name
     *
     * @var string
     */
    protected $name;

    /**
     * Column is primary
     *
     * @var bool
     */
    protected $primary = false;

    /**
     * Column data type
     *
     * @var string
     */
    protected $type;

    /**
     * Column could not be null
     *
     * @var bool
     */
    protected $notnull = false;

    /**
     * Column default value
     *
     * @var null|mixed
     */
    protected $default = null;

    /**
     * Column is protected, used on insert|update table process
     *
     * @var bool
     * @todo Implement protected column
     */
    protected $guarded = false;

    /**
     * Database engine
     *
     * @var string
     */
    protected $engine;

    /**
     * Column extra information
     *
     * @var array
     */
    protected $extra;

    /**
     * Serialize column options
     *
     * @var string
     */
    protected $serialize;

    /**
     * Column is unique
     *
     * @var bool
     */
    protected $unique = false;

    /**
     * Column is unique along with another column
     *
     * @var bool
     */
    protected $uniques = false;

    /**
     * @var
     */
    protected $master;

    /**
     * Create ColumnInfo instance
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->mergeWithMasterData($options);

        $this->serialize = base64_encode(serialize($options));

        $this->setPropertyFromOptions($options, true);

        $this->extra = $options;
    }

    /**
     * Merge options with master data while key "master" available
     * in $options
     *
     * @param array $options
     */
    protected function mergeWithMasterData(array &$options)
    {
        if (null !== ($name = array_get($options, 'master'))
                && [] !== ($master = config('webarq.data-type-master.' . $name, []))
        ) {

            $options = Arr::merge($master, $options);
        }
    }

    /**
     * Get column comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get column length
     *
     * @return number
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Get column name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get column type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Column is primary
     *
     * @return bool
     */
    public function isPrimary()
    {
        return $this->primary;
    }

    /**
     * Column is guarded
     *
     * @return bool
     */
    public function isGuarded()
    {
        return $this->guarded;
    }

    /**
     * Get column engine
     *
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Get column default value
     *
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Column is nullable
     *
     * @return bool
     */
    public function nullable()
    {
        return false === $this->notnull;
    }

    /**
     * Unserialize options which is already serialized
     *
     * @return array
     */
    public function unserialize()
    {
        return isset($this->serialize) ? unserialize(base64_decode($this->serialize)) : [];
    }

    /**
     * Magic method, to get property or extra information
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (method_exists($this, ($method = Str::camel('get ' . $key)))) {
            return $this->{$method}();
        } else {
            return $this->getExtra($key);
        }
    }

    /**
     * Get extra column information
     *
     * @param $key
     * @param null|mixed $default
     * @return mixed
     */
    public function getExtra($key, $default = null)
    {
        return array_get($this->extra, $key, $default);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setExtra($key, $value)
    {
        $this->extra[$key] = $value;

        return $this;
    }

    /**
     * Get column master config
     *
     * @return mixed
     */
    public function getMaster()
    {
        return $this->master;
    }

    /**
     * Get input attributes
     *
     * @return array
     */
    public function getInputAttribute()
    {
        $attr = [
                'name' => $this->name,
                'type' => 'text',
                'required' => $this->notnull || !empty($this->getExtra('required')) ? 'required' : null,
                'default' => $this->default,
                'numeric' => $this->isInt(),
                'multilingual' => $this->getExtra('multilingual'),
                'length' => $this->length,
                'unique' => $this->isUnique(),
                'uniques' => $this->isUniques()
        ];

        return Arr::filter(Arr::merge($this->getExtra('form', []), $attr, 'ignore'), [0]);
    }

    /**
     * Check if column data type is integer
     *
     * @return bool
     */
    public function isInt()
    {
        return str_contains($this->type, 'int')
        || in_array($this->type, ['decimal', 'float', 'double', 'numeric', 'real']);
    }

    /**
     * Column is unique
     *
     * @return bool
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * Column is unique along with another column
     *
     * @return bool
     */
    public function isUniques()
    {
        return $this->uniques;
    }

    /**
     * Check if column is multilingual
     *
     * @return bool
     */
    public function isMultilingual()
    {
        $value = $this->getExtra('multilingual');

        return true === $value || (is_array($value) && [] !== $value);
    }

    public function __clone()
    {
        $multilingual = $this->getExtra('multilingual');

        if (is_array($multilingual)) {
            $this->setPropertyFromOptions($multilingual);
        } elseif (true === $multilingual) {
            $this->notnull = false;
        }
    }
}