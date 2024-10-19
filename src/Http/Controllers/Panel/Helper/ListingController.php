<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 7:06 PM
 */

namespace Webarq\Http\Controllers\Panel\Helper;


use Webarq\Http\Controllers\Panel\BaseController;

class ListingController extends BaseController
{
    /**
     * @var \Webarq\Manager\Cms\HTML\TableManager
     */
    protected $builder;

    public function actionGetIndex()
    {
        if (null !== ($v = array_get($this->panel->getListing(), 'view'))) {
            $this->layout = view($v);
        }

        $this->builder = \Wa::manager('cms.HTML!.table', $this->admin, $this->module, $this->panel);
    }

    public function after()
    {
        view()->share('shareSearchBox', null !== array_get($this->panel->getListing(), 'searchable'));

        if (\Request::ajax()) {
            return $this->builder->toHtml();
        }

        $this->layout = $this->builder->toHtml();

        return parent::after();
    }
}