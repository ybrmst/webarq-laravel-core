<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 10:40 AM
 */

namespace Webarq\Http\Controllers\Panel;


use Auth;
use DB;
use URL;
use Wa;
use Webarq\Http\Controllers\Webarq;
use Webarq\Info\ModuleInfo;
use Webarq\Info\PanelInfo;

class BaseController extends Webarq
{
    /**
     * @var \Webarq\Manager\AdminManager
     */
    protected $admin;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->themes = config('webarq.system.themes', 'default');

        parent::__construct($params);

        $this->admin = Auth::user();

        view()->share(['admin' => $this->admin, 'shareBreadCrumbAction' => $this->getParam('action')]);
    }

    /**
     * Called from routing file
     *
     * @return mixed
     */
    public function escape()
    {
        if (null === $this->admin) {
            if ('login' !== $this->action && 'auth' !== $this->controller) {
                return redirect(URL::panel('system/admins/auth/login'));
            }
        } elseif ([] === $this->admin->getLevel() && !$this->admin->isDaemon()) {
            return $this->actionGetNoMethod();
        } elseif (null !== \Request::segment(2)
                && (!$this->isAccessible() || !is_object($this->module) || !is_object($this->panel))
        ) {
            return $this->actionGetForbidden();
        } elseif (null !== ($escape = $this->escapeRules())) {
            return $escape;
        }

        view()->share(['shareModule' => $this->module, 'sharePanel' => $this->panel]);

        return parent::escape();
    }

    /**
     * @return mixed
     */
    protected function isAccessible()
    {
        return $this->module instanceof ModuleInfo && $this->panel instanceof PanelInfo
        && Wa::panel()->isAccessible($this->module, $this->panel, $this->action);
    }

    /**
     * Called from routing file
     *
     * Do validation over current action method rules. Return forbidden view when invalid
     */
    public function escapeRules()
    {
        $act = $this->action;
        if ([] !== ($rules = $this->panel->getAction($act . '.rules', [])) && null !== $act) {
            if (!$this->preRule($rules)) {
                $row = DB::table($this->panel->getTable())
                        ->where(Wa::table($this->panel->getTable())->primaryColumn()->getName(), $this->getParam(1))
                        ->first();

                if (null === $row
                        || !Wa::manager('cms.rule', $this->admin, $rules, (array)$row, $this->panel->getTable(), $act)
                                ->isValid()
                ) {
                    return $this->actionGetForbidden();
                }
            }
        }
    }

    protected function preRule(array &$rules)
    {
        if (null !== ($count = array_pull($rules, 'max-row'))) {
            return DB::table($this->panel->getTable())
                    ->count('id') < (int)$count;

        }

        return false;
    }

    /**
     * @return string
     */
    public function actionGetIndex()
    {
        return $this->actionGetForbidden();
    }

    public function after()
    {
// Send session transaction in to layout
        $this->layout->with('alerts', \Session::get('transaction', []));

// Unset redirect url session whenever "FormController" not instantiate
        if (false === strpos(get_called_class(), 'FormController')
                && 'menus' !== $this->controller && 'section' !== $this->action
        ) {
            \Session::pull('redirect-url');
        }

        return parent::after();
    }

    protected function setTransactionMessage($message, $type = 'warning')
    {
        \Session::flash('transaction', is_array($message) ? $message : [$message, $type]);
    }
}