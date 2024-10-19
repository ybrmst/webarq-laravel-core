<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 6/19/2017
 * Time: 3:26 PM
 */

namespace Webarq\Http\Controllers\Site;


class ThankYouController extends BaseController
{
    protected $layout = 'thank-you';

    public function escape()
    {

    }

    public function actionGetIndex()
    {
        if (null === ($msg = \Session::pull('redirect-msg'))) {
            return redirect('');
        } else {
            $this->layout->with(['message' => $msg]);
        }
    }
}