<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 6/15/2017
 * Time: 11:23 AM
 */

namespace Webarq\Http\Controllers\Site\Templates;


use Wa;
use Webarq\Http\Controllers\Site\BaseController;

class LeadsController extends BaseController
{
    public function actionPostIndex()
    {
        $index = $this->actionGetIndex();

        if (null !== ($redirect = \Session::pull('redirect-url'))) {
            return redirect($redirect)->with('redirect-msg', \Session::pull('redirect-msg'));
        }

        return $index;
    }
}