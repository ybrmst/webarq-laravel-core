<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/20/2017
 * Time: 1:32 PM
 */

namespace Webarq\Http\Controllers\Panel\Helper;


use DB;
use Wa;
use Webarq\Http\Controllers\Panel\BaseController;

class IsSystemController extends BaseController
{
    public function actionGetIndex()
    {
        $id = $this->getParam(1);
        $activeness = (int)$this->getParam(2);

        if (is_numeric($id)) {
            $mgr = Wa::table($this->panel->getName());
            $row = DB::table($this->panel->getName())
                    ->select($mgr->primaryColumn()->getName())
                    ->where($mgr->primaryColumn()->getName(), $id)
                    ->get()
                    ->toArray();

            if ([] === $row) {
                return $this->actionGetForbidden();
            } elseif (DB::table($this->panel->getName())
                    ->where($mgr->primaryColumn()->getName(), $id)
                    ->update(['is_system' => 1 === $activeness ? 0 : 1])
            ) {
                $act = $activeness ? 'unset as system' : 'set as system';
// Log history
                Wa::instance('manager.cms.history')->record($this->admin, $act,
                        Wa::table($this->panel->getName()), (array)$row);

                $this->setTransactionMessage(Wa::trans('webarq::core.messages.success-update'), 'success');
            } else {
                $this->setTransactionMessage(Wa::trans('webarq::core.messages.invalid-update'), 'warning');
            }
        }

        return redirect(Wa::panel()->listingURL($this->panel));
    }

}