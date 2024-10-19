<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 6:20 PM
 */

namespace Webarq\Http\Controllers\Panel\Helper;


use DB;
use Wa;
use Webarq\Http\Controllers\Panel\BaseController;

class ActivenessController extends BaseController
{
    public function actionGetIndex()
    {
        $id = $this->getParam(1);
        $activeness = (int)$this->getParam(2);

        if (is_numeric($id)) {
            $mgr = Wa::table($this->panel->getTable());
            $row = DB::table($this->panel->getTable())
                    ->select()
                    ->where($mgr->primaryColumn()->getName(), $id)
                    ->first();

            if (null === $row) {
                return $this->actionGetForbidden();
            } elseif (DB::table($this->panel->getTable())
                    ->where($mgr->primaryColumn()->getName(), $id)
                    ->update(['is_active' => 1 === $activeness ? 0 : 1])
            ) {
                $act = $activeness ? 'deactivated' : 'activated';
// Log history
                Wa::instance('manager.cms.history')->record($this->admin, $act, $mgr, (array)$row);

                $this->setTransactionMessage(Wa::trans('webarq::core.messages.success-update'), 'success');
            } else {
                $this->setTransactionMessage(Wa::trans('webarq::core.messages.invalid-update'), 'warning');
            }
        }

        return redirect(Wa::panel()->listingURL($this->panel));
    }
}