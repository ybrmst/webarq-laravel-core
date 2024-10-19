<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 6/15/2017
 * Time: 3:12 PM
 */

namespace Webarq\Manager\Site;


use Request;
use Session;
use Validator;
use Wa;
use Webarq\Manager\HTML\Form\LaravelInputHint;
use Webarq\Manager\SetPropertyManagerTrait;

class LeadManager
{
    use LaravelInputHint, SetPropertyManagerTrait;

    /**
     * @var object Webarq\Manager\Html\FormManager
     */
    protected $form;

    /**
     * Lead group
     *
     * @var string
     */
    protected $group;

    /**
     * Redirect url, when lead form submitted
     *
     * @var array
     */
    protected $redirect;

    /**
     * Lead form validator
     *
     * @var object Validator
     */
    protected $validator;

    public function __construct($group = null)
    {
        $options = config('webarq.leads.' . $group, []);

        $this->setPropertyFromOptions($options);

        $this->group = $group;

        if (!isset($this->redirect)) {
            $this->redirect = [
                    'url' => \URL::trans('thank-you'),
                    'msg' => 'Thank you for submitting. We will contact you as soon as possible'
            ];
        } elseif (!is_array($this->redirect)) {
            $this->redirect = [
                    'url' => $this->redirect,
                    'msg' => 'Thank you for submitting. We will contact you as soon as possible'
            ];
        }

        $this->form = Wa::html('form',
                array_pull($options, 'action'),
                array_pull($options, 'attributes', []),
                array_pull($options, 'container', '<div class="box box-default"><div class="box-body"></div></div>'));

        foreach ($options as $key => $value) {
            if (isset($value['form'])) {
                $this->setInput($value['form'], $key);
            } else {
                $this->form->addCollection('hidden', $key, Request::input($key));
            }
        }

        $this->form->addCollection('hidden', 'landing_page', Request::input('landing_page', Request::url()));
    }

    /**
     * @param array $setting
     * @param $title
     */
    protected function setInput(array $setting, $title)
    {
// Type
        $t = array_pull($setting, 'type', 'text');
// Title
        $l = array_pull($setting, 'title', title_case(str_replace(['_', '-'], ' ', $title)));
// Info
        $i = array_pull($setting, 'info');
// Container
        $c = array_pull($setting, 'container');
// Arguments
        $a = [$t];
        foreach (array_get($this->inputHints, $t, []) as $k => $v) {
            if (is_numeric($k)) {
                if ('name' === $v) {
                    $a[$v] = array_get($setting, $v, $title);
                } else {
                    $a[$v] = $setting[$v];
                }
            } else {
                $a[$k] = array_get($setting, $k, $v);
            }
        }

        if (isset($setting['rule']) && isset($a['name'])) {
            $this->form->setAttribute('rules.' . $a['name'], $setting['rule']);

            if (isset($setting['error'])) {
                $this->form->setAttribute('errors.' . $a['name'], $setting['error']);
            }
        }

        $this->form->addCollection($a, $l, $i, $c);
    }

    /**
     * Generate general html
     *
     * @return mixed
     */
    public function toHtml()
    {
        $rules = $this->form->pullAttribute('rules');
        $messages = $this->form->pullAttribute('errors', []);

        if ('POST' === Request::method()) {
            if (null !== $rules) {
                $this->setValidator($rules, $messages);

                if ($this->validator->fails()) {
                    return $this->form->toHtml($this->error());
                } else {
                    $this->store();
                }
            } else {
                $this->store();
            }
        } else {
            return Session::get('redirect-msg', $this->form->toHtml());
        }
    }

    /**
     * Generate error message when param $html is set to true, else return array of error messages
     *
     * @param bool|true $html
     * @return string|array
     */
    public function error($html = true)
    {
        if (true === $html) {
            $s = '<div class="box-row"><div class="alert alert-warning">';
            $s .= '<h4><i class="icon fa fa-warning"></i> Alert!</h4>';
            foreach ($this->validator->errors()->getMessages() as $alerts) {
                $s .= current($alerts) . '<br/>';
            }

            return $s . '</div></div>';
        } else {
            return $this->validator->errors()->getMessages();
        }
    }

    /**
     *
     */
    public function store()
    {
// Get data
        $data = Request::all();
// Forgot unnecessary item(s)
        array_forget($data, ['_token']);

        $model = Wa::model('lead');
        $model->{'lead_type'} = $this->group;
        $model->{'landing_page'} = array_get($data, 'landing_page', Request::path());
        $model->{'lead_data'} = serialize($data);
        if (null !== (Wa::table('leads')->getColumn('lead_value'))) {
            $model->{'lead_value'} = implode('', $data);
        }
        $model->{'create_on'} = date('Y-m-d H:i:s');
        $model->save();

        Session::set('redirect-url', $this->redirect['url']);
        Session::set('redirect-msg', $this->redirect['msg']);
    }

    /**
     * @return mixed
     */
    protected function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param array $rules
     * @param array $errors
     * @param array|null $inputs
     */
    protected function setValidator(array $rules, $errors = [], array $inputs = null)
    {
        $this->validator = Validator::make($inputs ?: Request::all(), $rules, $errors);
    }
}