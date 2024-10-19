<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/16/2017
 * Time: 2:36 PM
 */
namespace Webarq\Manager\Cms\HTML;


use Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Wa;
use Webarq\Info\ModuleInfo;
use Webarq\Manager\AdminManager;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;
use Webarq\Manager\Cms\HTML\Form\RulesManager;
use Webarq\Manager\SetPropertyManagerTrait;


/**
 * Panel form generator
 *
 * Generate form based on configuration module files
 *
 * Class FormManager
 * @package Webarq\Manager\Cms\HTML
 */
class FormManager
{
    use SetPropertyManagerTrait;

    /**
     * Current login admin
     *
     * @var AdminManager
     */
    protected $admin;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $panel;

    /**
     * Form model, useful to get input values
     *
     * @var true|array [class name, method name]|string method name|
     */
    protected $model;

    /**
     * Form transaction type, create or edit
     *
     * @var string
     */
    protected $type;

    /**
     * Form title
     *
     * @var string
     */
    protected $title;

    /**
     * Form action
     *
     * @var string
     */
    protected $action;

    /**
     * Validator messages
     *
     * @var array
     */
    protected $validatorMessages = [];

    /**
     * Inputs rule in laravel format
     *
     * @var array
     */
    protected $validatorRules = [];

    /**
     * Transaction pairs, used on insert|update processing
     *
     * @var array
     */
    protected $pairs = [];

    /**
     * Form error message
     *
     * @var string HTML element
     */
    protected $message = [];

    /**
     * Input values
     *
     * @var array
     */
    protected $values = [];

    /**
     * Form inputs
     *
     * @var array
     */
    protected $inputs = [];

    /**
     * @var array
     * @todo Support media tab on form
     */
    protected $media = [];

    /**
     * @var string
     */
    protected $view = 'webarq::manager.cms.form.index';

    /**
     * HTML structure;
     *
     * @var string
     */
    protected $html;

    /**
     * Form attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $alerts = [];

    /**
     * Transaction master table.
     * Usable for multiple table transaction
     *
     * @var
     */
    protected $master;

    /**
     * Master row id
     *
     * @var
     */
    protected $editingRowId;

    /**
     * Sequence input, in [input => column info] pair
     *
     * @var array
     */
    protected $sequenceInputs = [];

    public function __construct(AdminManager $admin, array $options = [])
    {
        $this->admin = $admin;

        $this->setup($options);
    }

    protected function setup(array $options)
    {
        $this->setPropertyFromOptions($options);

        $this->prepareInputs($options);
    }

    /**
     * @param array $inputs
     * @todo Support row grouping, following [[path1 => array setting, path2 => array setting] ...] format
     */
    protected function prepareInputs(array $inputs)
    {
        if ([] !== $inputs) {
            $master = null;
            foreach ($inputs as $path => $attr) {
                if (is_numeric($path)) {
                    $path = $attr;
                    $attr = [];
                }
// Build input
                $input = $this->makeInput($path, $attr);
// Input not found
                if (null === $input) {
// Make sure the input has a name
                    $attr += ['name' => $path];
// When file attribute has defined, then the input type should be file
                    $type = isset($attr['file']) ? 'file' : array_get($attr, 'type', 'text');
// Try to load the class
                    $input = Wa::load('manager.cms.HTML!.form.input.' . $type . ' input', null, null, $attr)
                            ?: Wa::load('manager.cms.HTML!.form.input.default input', null, null, $attr);
// This is input have no module and panel
                    if (null !== $input) {
                        $this->inputs[$input->getInputName()] = $input;
                    }
                    continue;
                }
// Check for master table
                if (null === $master || 0 === strpos($master, str_singular($input->{'table'}->getName()))) {
                    $master = $input->{'table'}->getName();
                }
// Process valid input
                if ($input->isValid()) {
                    if ($input->isPermissible()) {
                        $this->inputs[$input->getInputName()] = $input;
                        if ($input->isMultilingual()) {
                            foreach (\Wl::getCodes() as $code) {
                                if (\Wl::getSystem() != $code) {
                                    $clone = clone $input;
                                    $clone->attribute()->setName($clone->name, $code);
                                    $clone->setTitle($clone->getTitle() . ' (' . strtoupper($code) . ')');
                                    $this->inputs[$clone->getInputName()] = $clone;
                                    $this->pairs['multilingual'][$input->name][$code] = $clone;
                                }
                            }
                        }
                    }

                    $this->pairs[$input->getInputName()] = $input;
                }
            }

            if (null === $this->master) {
                $this->master = $master;
            }
        }
    }

    /**
     * @param $path
     * @param array $attr
     * @return mixed
     */
    protected function makeInput($path, array $attr)
    {
        list($module, $table, $column) = explode('.', $path, 3) + [1 => null, 2 => null];
//      Module which table is registered
        $module = Wa::module($module);

        if (!$module instanceof ModuleInfo || !$module->hasTable($table)) {
            return null;
        }
        $table = $module->getTable($table);
        $column = $table->getColumn($column);
        $old = $column->unserialize();
// Get extra form attributes
        $xtra = $column->getExtra('form', []);
// Options should not be merged
        if (isset($attr['options'])) {
            array_forget($xtra, 'options');
        }
// Merge all attributes
        $attr = Arr::merge(Arr::merge($old, $xtra), $attr);
        $attr = ['table' => $table, 'column' => $column, 'db-type' => $old['type'], 'form-type' => $this->type] + $attr;
// Input type
        $type = isset($attr['file']) ? 'file' : array_get($attr, 'type', 'null');
// This is could be pain on the process, but due to laravel input form method behaviour is different
// one from another, we need class helper to enable us adding consistent parameter
        $input = Wa::load('manager.cms.HTML!.form.input.' . $type . ' input', $this->module, $this->module->getPanel($this->panel), $attr)
                ?: Wa::load('manager.cms.HTML!.form.input.default input', $this->module, $this->module->getPanel($this->panel), $attr);

        return $this->inputManagerDependencies($input);
    }

    /**
     * @param AbstractInput $input
     * @return AbstractInput
     */
    protected function inputManagerDependencies(AbstractInput $input)
    {
        return $input;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function getValue($key = null, $default = null)
    {
        return array_get($this->values, $key, $default);
    }

    /**
     * Set post data form data
     *
     * @param array $values
     */
    public function setValues(array $values = [])
    {
//        Check for ignore input
        if (isset($values['remote-value']) && $remote = Str::decodeSerialize($values['remote-value'])) {
            foreach ($this->inputs as $input) {
                if (true === $input->ignored && empty($values[$input->name]) && isset($remote[$input->name])) {
                    $values[$input->name] = $remote[$input->name];
                }
            }
        }
        $this->values = Arr::merge($this->values, $values);
    }

    /**
     * @param null|number $id
     */
    public function dataModeling($id = null)
    {
        if (null !== ($model = $this->getModel($method))) {
            $this->values = $model->{$method}($id, $this->pairs);
//            Make sure the values well prepared
            foreach ($this->pairs as $name => $input) {
                if ($input instanceof AbstractInput && !isset($this->values[$input->getInputName()])) {
                    $this->values[$input->getInputName()] = array_pull($this->values, $input->{'column'}->getName());
                }
            }
        } else {
            $this->values = Wa::load('manager.cms.HTML!.form.model$', $id, $this->pairs, $this->master)
                    ->getData();
        }

        if (is_array($this->values)) {
            $this->html .= Form::hidden('remote-value', Str::encodeSerialize($this->values));
        }
    }

    /**
     * Get form model
     *
     * @return null|\Webarq\Model\AbstractListingModel
     */
    public function getModel(&$method = null)
    {
        $method = 'formRowFinder';
        if (true === $this->model) {
            return Wa::table($this->panel)->model();
        } elseif (is_array($this->model)) {
            $method = array_get($this->model, 1, $method);
            return Wa::model($this->model[0]);
        } elseif (is_string($this->model)) {
            if (null !== ($model = Wa::model($this->model))) {
                return $model;
            } else {
                $method = $this->model;
                return Wa::table($this->panel)->model();
            }
        }
    }

    /**
     * @param $id
     */
    public function setEditingRowId($id)
    {
        $this->editingRowId = $id;
    }

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getInput($key = null, $default = null)
    {
        return array_get($this->inputs, $key, $default);
    }

    /**
     * Get pairs
     *
     * @return array
     */
    public function getPairs()
    {
        return $this->pairs;
    }

    /**
     * @return array
     */
    public function getValidatorMessages()
    {
        return $this->validatorMessages;
    }

    /**
     * @return array
     */
    public function getValidatorRules()
    {
        return $this->validatorRules;
    }

    /**
     * @param array $messages
     * @param string $level
     */
    public function setAlert($messages = [], $level = 'warning')
    {
        if (is_array($messages)) {
            foreach ($messages as &$message) {
                if (is_array($message)) {
                    $message = current($message);
                }
            }
            $this->alerts = [$messages, $level];
        }
    }

    /**
     * @return mixed
     */
    public function getMaster()
    {
        return $this->master;
    }

    /**
     * Compile form inputs
     *
     * @return $this
     */
    public function compile()
    {
        if ([] !== $this->inputs) {

            foreach ($this->inputs as $input) {
// Set input value
                $input->setValue(array_get($this->values, $input->getInputName()), $this->editingRowId);

// Check for input sequence
                $this->modifySequenceRule($input);

// Collect input validator
                $this->collectInputValidator($input);

                $this->html .= $input->buildHtml();
            }
        }

        if (null !== \Request::input('remote-value')) {
            $this->html .= Form::hidden('remote-value', \Request::input('remote-value'));
        }

        return $this;
    }

    /**
     * @param AbstractInput $input
     */
    protected function modifySequenceRule(AbstractInput $input)
    {
        if (null !== $input->column && 'sequence' === $input->column->getMaster()) {
            $newItem = 'create' === $this->type;
            $remotes = [];
// Check for remote value
            if ('edit' === $this->type) {
                $remotes = \Request::input('remote-value');
                if (null !== $remotes) {
                    $remotes = Str::decodeSerialize($remotes);
                }
            }

            $max = \DB::table($input->table->getName());
            if (null !== ($parents = $input->attribute()->get('grouping-column'))) {
                $parents = (array)$parents;
                $tmp = [];
                foreach ($parents as $column) {
                    if (null !== ($ipt = array_get($this->inputs, $column))) {
                        $max->where($ipt->column->getName(), $ipt->getValue());
                        if (is_numeric($ipt->getValue())
                                && (int)$ipt->getValue() !== (int)array_get($remotes, $column)
                        ) {
                            $newItem = true;
                        }
                        $tmp[$column] = $ipt->column->getName();

                        $this->inputs[$column]->attribute()->set('onChange', 'sequenceAjax();', true);
                    }
                }

                $input->attribute()->set('grouping-column', json_encode($tmp));
            }

            $input->rules->setItem('max', $max->get()->count() + ($newItem ? 1 : 0));

            $this->sequenceInputs[$input->column->getName()] = $input;
        }
    }

    /**
     * Get input rules
     *
     * @param AbstractInput $input
     */
    protected function collectInputValidator(AbstractInput $input)
    {
        if ($input->{'rules'} instanceof RulesManager) {
// Collect validator rules
            $this->validatorRules[$input->getInputName()] = $input->rules->toString();

            if ([] !== $input->errorMessages) {
                foreach ($input->errorMessages as $errType => $errMsg) {
                    $this->validatorMessages[$input->name . '.' . $errType] = $errMsg;
                }
            }
        }
    }

    /**
     * Convert $builder into well formatted HTML element
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function toHtml()
    {
        $this->attributes['url'] = $this->action;

// Check for redirect session
        if (null !== ($redirect = \Session::get('redirect-url'))) {
            $this->html .= Form::hidden('redirect-url', $redirect);
        }

        return view($this->view, [
                'title' => $this->title ?: Wa::trans('webarq::core.title.' . $this->type,
                        ['item' => studly_case($this->panel)]
                ),
                'attributes' => $this->attributes,
                'html' => $this->html,
// In case you want to build your own elements html structure
                'elements' => $this->inputs,
                'alerts' => $this->alerts
        ]);
    }

    /**
     * @return array
     */
    public function getSequenceInput()
    {
        return $this->sequenceInputs;
    }

    /**
     * Modify value based on modifier config
     *
     * @param $modifier
     * @param $string
     * @return mixed
     */
    protected function modifyValue($modifier, $string)
    {
        if (null !== $modifier) {
            return Wa::load('manager.value modifier')->{$modifier}($string);
        }
        return $string;
    }
}