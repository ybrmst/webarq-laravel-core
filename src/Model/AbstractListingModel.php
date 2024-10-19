<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 5:58 PM
 */

namespace Webarq\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Wa;

class AbstractListingModel extends Model
{
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = null;

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;

    /**
     * Translation attributes
     *
     * @var array
     */
    protected $translations = [];

    /**
     * Adaptor method to get active row
     *
     * @param array $select
     * @param array $selectTranslate
     * @return mixed
     */
    public function getActive(array $select = [], array $selectTranslate = [])
    {
        foreach ($select as &$column) {
            if (false === strpos($column, '.')) {
                $column = $this->table . '.' . $column;
            }
        }

        $builder = $this->select($select);

        if ([] !== $selectTranslate) {
            $builder->selectTranslate($selectTranslate);
        }

        return $builder->whereIsActive(1)->get();
    }

    /**
     * Adaptor method to get latest row with some condition
     *
     * @param $limit
     * @param int $offset
     * @param null $activeness
     * @return mixed
     */
    public function getLatest($limit, $offset = 0, $activeness = null)
    {
        if (is_bool($offset)) {
            $activeness = $offset;
            $offset = 0;
        }

        $builder = $this->limit($limit)
                ->offset($offset);

        if (!is_null($activeness)) {
            if (is_string($activeness)) {
                $builder->where($activeness, 1);
            } else {
                $builder->whereIsActive($activeness);
            }
        }

        return $builder;
    }

    /**
     * Adaptor method to decide how the model will translate a key column
     *
     * @param $key
     * @param null $code
     * @return array|mixed
     */
    public function trans($key, $code = null)
    {
        if (is_null($code)) {
            $code = app()->getLocale();
        }

        if (class_exists('Wl') && $code !== \Wl::getSystem()) {
            if (!isset($this->translations[$code])) {
// Get all translations
                $this->translations = Arr::merge($this->translations,
                        \Wl::getTranslation(Wa::table($this->table), $this->{$this->primaryKey}));
            }

            if (null !== ($row = array_get($this->translations, $code)) && $row instanceof Model) {
                return $row->getOriginal($key, $this->getOriginal($key));
            }
        }

        return $this->getOriginal($key);
    }

    /**
     * Adaptor method which get row translation for specific table and row id
     *
     * @param $table
     * @param $id
     * @param array $columns
     * @param null $code
     * @return mixed
     */
    public function getTranslationsRow($table, $id, array $columns = null, $code = null)
    {
        if (class_exists('Wl')) {
            $builder = \DB::table(\Wl::translateTableName($table))
                    ->where(Wa::table($table)->getReferenceKeyName(), $id);

            if (isset($code)) {
                $builder->where(\Wl::getLangCodeColumn('name'), $code);
            } elseif (null !== $columns) {
                $columns[] = \Wl::getLangCodeColumn('name');
            }

            return $builder->get($columns);
        }
    }

    /**
     * Adaptor method to call specific render mutator
     *
     * @param $key
     * @param array $attributes
     * @return mixed
     */
    public function render($key, ... $attributes)
    {
        $m = camel_case($key . ' attribute render');

        if (method_exists($this, $m)) {
            return $this->$m($this->{$key}, ... $attributes);
        }

        return $this->{$key};
    }

    /**
     * Adaptor method to find some row with some conditions
     *
     * @param $where
     * @param array $columns
     * @param array $translate
     * @param array $relations
     * @param null $activeness
     * @return null
     */
    public function finder($where, $columns = [], $translate = [], $relations = [], $activeness = null)
    {
// Customization with value
        if (is_bool($relations)) {
            $activeness = $relations;
            $relations = [];
        }

// Customization columns value
        if (is_bool($columns)) {
            $activeness = $columns;
            $columns = [];
        }
// Customization translate value
        if (is_bool($translate)) {
            $activeness = $translate;
            $translate = [];
        }

// Init select
        $find = $this->select(... (array)$columns);
// The relations
        if ([] !== $find) {
            $find = $find->with($relations);
        }

// Select translation
        if ($translate) {
            $find = $find->selectTranslate(... (array)$translate);
        }
// Where should be array
        if (!is_array($where)) {
            $where = ['id' => $where];
        }
// Activeness where
        if (true === $activeness) {
            $where['is_active'] = 1;
        } elseif (false === $activeness) {
            $where['is_active'] = 0;
        }
// Loop the where conditions
        foreach ($where as $wc => $wv) {
            $find = $find->where($wc, $wv);
        }

        $find = $find->get();

        switch ($find->count()) {
            case 1:
                return $find->first();
            case 0:
                return null;
        }

        return $find;
    }

    /**
     * Adaptor method which handle some row deletion & unlink given mimes column
     *
     * @param null|array|string $mime Column names
     * @return number of deleted row
     * @throws \Exception
     */
    public function rowDelete($mime = null)
    {
//        Unlink given mime columns
        if (!empty($mime)) {
            foreach ((array)$mime as $column) {
                if (file_exists($this->{$column})) {
                    unlink($this->{$column});
                }
            }
        }
//        Delete translations if any
        $this->deleteTranslations();

        return $this->delete() ? 1 : 0;
    }

    /**
     * Adaptor method to handle the row translation deletion
     *
     * @return mixed
     */
    protected function deleteTranslations()
    {
        if (class_exists('Wl') && Wa::table($this->getTable()) && Wa::table($this->getTable())->isMultilingual()) {
            return \DB::table(\Wl::translateTableName($this->getTable()))
                    ->where(Wa::table($this->getTable())->getReferenceKeyName(), $this->getKey())
                    ->delete();
        }
    }

    /**
     * Mutator
     *
     * @param $date
     * @param bool $short Short version of month or not
     * @return bool|string
     */
    protected function postDateAttributeRender($date, $short = false)
    {
        if (is_array(trans('site.Month'))) {
            list($y, $m, $d) = explode('-', date('Y-n-d', strtotime($date)));
            $m = Wa::trans('site.Month.' . $m);
        } else {
            list($y, $m, $d) = explode('-', date('Y-m-d', strtotime($date)));
        }
        if ($short) {
            $m = substr($m, 0, 3);
            $y = substr($y, -2);
        }

        return $d . ' ' . $m . ' ' . $y;
    }
}