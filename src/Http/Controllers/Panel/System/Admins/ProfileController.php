<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/27/2017
 * Time: 1:21 PM
 */

namespace Webarq\Http\Controllers\Panel\System\Admins;


use Hash;
use Request;
use Validator;
use Wa;
use Webarq\Http\Controllers\Panel\BaseController;
use Webarq\Model\AdminModel;

class ProfileController extends BaseController
{
    public function actionGetIndex()
    {
        $this->layout->{'rightSection'} = Wa::getThemesView($this->themes, 'common.admin-profile');
    }

    public function actionPostChangePassword()
    {
        // $2y$10$FtznqephGwqL8fE.ocTr6.lFed3CMRMKPkbpppxzC79BQK5C.cxGm
//        dd(\Hash::check('rubik-cube', $this->admin->getAuthPassword()), $this->admin->getAuthPassword());
        $validator = Validator::make(Request::all(), [
                'old-password' => 'required|matched_hash:' . $this->admin->getAuthPassword() . ', Please input your old password',
                'new-password' => 'required|min:3',
                'confirm-password' => 'required|min:3|same:new-password'
        ]);

        if ($validator->fails()) {
            $messages = [];
            foreach ($validator->errors()->getMessages() as $element => $errors) {
                $messages[] = current($errors);
            }

            $this->setTransactionMessage([$messages], 'warning');
        } elseif ($this->admin->isDaemon()) {
            $c = Wa::model('configuration')->select('setting', 'id')
                    ->whereKey('48d35125f4a3c2c005d5b0697463c4651704b427')
                    ->first();
            if ($c) {
                $c = Wa::model('configuration')->find($c->id);
                $c->{'setting'} = encrypt(base64_encode(serialize(['secret' => Request::input('new-password')]
                        + unserialize(base64_decode(decrypt($c->setting))))));
                $c->save();
            }
        } else {
            $get = AdminModel::find($this->admin->id);

            if (!is_null($get)) {
                if (Hash::check(Request::input('old-password'), $get->password)) {
                    $get->password = Hash::make(Request::input('new-password'));
                    $get->update();

                    $this->setTransactionMessage('Your password already change', 'success');
                } else {
                    $this->setTransactionMessage('Please check your old password', 'warning');
                }
            }
        }

        return $this->actionGetChangePassword();
    }

    public function actionGetChangePassword()
    {
        $form = Wa::html('form', ['class' => 'box-body'])
                ->setTitle('Change Password', '<div class="box-header with-border"><h3 class="box-title"></h3></div>');

        $form->addCollection('password', 'old-password', ['class' => 'form-control']);
        $form->addCollection('password', 'new-password', ['class' => 'form-control']);
        $form->addCollection('password', 'confirm-password', ['class' => 'form-control']);
        $form->submit('<button type="submit" class="btn btn-primary">Submit</button>');

        $this->layout->{'rightSection'} = $form->toHtml();
    }

    protected function isAccessible()
    {
        return true;
    }
}