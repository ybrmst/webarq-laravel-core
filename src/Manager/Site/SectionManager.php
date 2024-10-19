<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/23/2017
 * Time: 1:24 PM
 */

namespace Webarq\Manager\Site;


use DB;
use Illuminate\Contracts\Support\Htmlable;
use Wa;
use Webarq\Manager\SetPropertyManagerTrait;
use Webarq\Model\NoModel;

class SectionManager implements Htmlable
{
    use SetPropertyManagerTrait;

    /**
     * @var
     */
    protected $data = false;

    /**
     * @var
     */
    protected $id;

    /**
     * @var
     */
    protected $key;

    /**
     * Model options
     *
     * @var array
     */
    protected $model = [];

    /**
     * @var
     */
    protected $name;

    /**
     * @var number
     */
    protected $paginate;

    /**
     * @var array
     */
    protected $table;

    /**
     * @var
     */
    protected $template;

    /**
     * @var
     */
    protected $view;

    /**
     * Others data
     *
     * @var array
     */
    protected $viewVars = [];

    /**
     * Section is static raw html, it does not need data
     *
     * @var bool
     */
    protected $raw = false;

    public function __construct(array $options)
    {
        $this->setPropertyFromOptions($options);
//        Append non property options in to $viewVars property
        foreach ($options as $k => $v) {
            $this->viewVars[camel_case('opt ' . $k)] = $v;
        }
//        Check for string model was given
        if (is_string($this->model)) {
            if (str_contains($this->model, ':')) {
//                Separate class from parameters
                list($class, $params) = explode(':', $this->model, 2);
//                Separate method from parameters
                $params = explode(',', $params);
//                Beware with reference array pull action
                $this->model = array_combine(['class', 'method', 'attr'], array($class, array_pull($params, 0), $params));
            } else {
                $this->model = ['class' => $this->model];
            }
        }

        if ([] !== $this->model) {
            $this->model += [
                    'class' => 'mustBeFilled',
                    'method' => 'getDataSection',
                    'attr' => []
            ];
        }

        if (null === $this->view) {
            $this->view = $this->key;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * @param null|string $view
     * @return string
     */
    public function toHtml($view = null)
    {
// Get menu template
        $template = Wa::menu()->getActive()->template;

        if (is_null($view)) {
            if (is_callable($this->raw)) {
                return call_user_func($this->raw);
            }

            $view = 'webarq::themes.front-end.sections.' . $template . '.' . $this->view;

            if (!view()->exists($view)) {
                $view = 'webarq::themes.front-end.sections.' . $this->view;
            }
        }

        if (!view()->exists($view)) {
            return '<div style="font-size:50px;">Section '
            . $this->template . ' ' . $this->key . ' does not have respected view</div>';
        } else {
            $this->viewVars += [
                    'shareData' => $this->getData(),
                    'pagingView' => $this->paginate,
                    'shareTemplate' => $this->template
            ];
            if (true === $this->raw) {
                return view($view, $this->viewVars)->render();
            } elseif ($this->getData()->count()) {
                return view($view, $this->viewVars)->render();
            }
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if (false === $this->data) {
            if (isset($this->model['class']) && null !== ($model = Wa::model($this->model['class']))) {
                $this->data = $model->{$this->model['method']}($this->id, array_get($this->model, 'attr'));
            } elseif (is_array($this->table)) {
                $model = Wa::model(str_singular($this->table['name'])) ?: NoModel::instance($this->table['name']);

                $builder = $model->whereSectionId($this->id);

                if (null !== ($column = array_get($this->table, 'select'))) {
                    $builder->select($column);
                }

                if (null !== ($column = array_get($this->table, 'translate'))) {
                    if (!is_bool(last($column))) {
                        array_push($column, true);
                    }

                    call_user_func_array([$builder, 'selectTranslate'], $column);
                }

                $builder
                        ->makeWhereFromOptions(array_get($this->table, 'where', []))
                        ->makeSequenceFromOptions(array_get($this->table, 'sequence'));
// Only active record
                if (true === array_get($this->table, 'activeness')) {
                    $builder->whereIsActive(1);
                }
// Limit the record
                if (isset($this->table['limit'])) {
                    $builder->limit($this->table['limit']);
                }

                if (!isset($this->paginate)) {
                    $this->data = $builder->get();
                } else {
                    $arr = explode(':', $this->paginate);

                    $this->data = $builder->paginate((int)$arr[0]);
                }
            } else {
                return $this;
            }
        }

        return $this->data;
    }

    /**
     * Always return 0, because if you hit this, its mean you do not set the proper configuration
     *
     * @return int
     */
    public function count()
    {
        return 0;
    }

    /**
     * Share something with view
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function with($key, $value)
    {
        if (is_array($key)) {
            $this->viewVars = $key + $this->viewVars;
        } else {
            $this->viewVars[$key] = $value;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}