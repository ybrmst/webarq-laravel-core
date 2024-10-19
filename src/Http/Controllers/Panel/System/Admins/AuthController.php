<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 12:59 PM
 */

namespace Webarq\Http\Controllers\Panel\System\Admins;


use Auth;
use Illuminate\Validation\Rule;
use Request;
use URL;
use Webarq\Http\Controllers\Panel\BaseController;
use Webarq\Model\AdminModel;
use Webarq\Model\AdminPasswordResetModel;

class AuthController extends BaseController
{
    protected $layout = 'login';

    protected $expiration = '24 hours';

    public function escape()
    {
        if (isset($this->admin) && 'logout' !== $this->action) {
            return redirect(URL::panel('system/dashboard'));
        }
    }

    /**
     * Action handler for login request, accept get method only
     */
    public function actionGetLogin()
    {

    }

    /**
     * Action handler for login request, accept post method only
     */
    public function actionPostLogin()
    {
        $validator = \Validator::make(
                ['username' => Request::input('username'), 'password' => Request::input('password'),],
                ['username' => 'required', 'password' => 'required']
        );

        if ($validator->fails()) {
            $this->layout->messages = $validator->errors()->getMessages();
        } else {
            Auth::attempt(['username' => Request::input('username'), 'password' => Request::input('password')]);

            if (Auth::user()) {
                return redirect(URL::panel('system/dashboard'));
            } else {
                $this->layout->messages = [['Please check your username and password']];
            }
        }
    }

    /**
     * Action handler for forgot password request, accept get method only
     */
    public function actionGetForgotPassword()
    {
        $this->setLayout('forgot-password');
    }

    /**
     * Action handler for forgot password request, accept post method only
     * @return mixed
     */
    public function actionPostForgotPassword()
    {
        $this->setLayout('forgot-password');

        $validator = \Validator::make(['email' => Request::input('username')], [
                        'email' => [
                                'required',
                                Rule::exists('admins')->where(function ($query) {
                                    $query->whereIsActive(1);
                                })
                        ]
                ]
        );

        if ($validator->fails()) {
            $this->layout->{'messages'} = $validator->errors()->getMessages();
        } else {
// Insert token reset in to database
            $token = $this->insertPasswordResetToken();
// Send email request
            $this->sendEmailPasswordResetToken($token, Request::input('username'));
// Mark as submit
            \Session::flash('status-password-reset', 'Success. Follow the link we emailed to you. <br/> <br/> Thank You!');
// Go back to previous page
            return redirect(URL::panel('system/admins/auth/forgot-password'));
        }
    }

    protected function insertPasswordResetToken()
    {
        $i = 0;
        do {
// Unexpected error
            if ($i > 100) {
                abort(501, 'Internal server error');
            }
            $token = str_random(30);
            $i++;
        } while (AdminPasswordResetModel::whereToken($token)->first() instanceof AdminPasswordResetModel);

// Get admin id by email
        $pr = new AdminPasswordResetModel();
        $pr->{'admin_id'} = AdminModel::whereEmail(Request::input('username'))->limit(1)->get(['id'])->first()->id;
        $pr->{'token'} = $token;
        $pr->{'expiration'} = date('Y-m-d H:i:s', strtotime('+' . $this->expiration));
        $pr->{'create_on'} = date('Y-m-d H:i:s');

        if ($pr->save()) {
            return $token;
        }
    }

    /**
     * @param $token
     * @param $email
     */
    protected function sendEmailPasswordResetToken($token, $email)
    {
        $data = [
                'url' => URL::panel('system/admins/auth/reset-password?token=' . encrypt(serialize([$token, $email]))),
                'expiration' => $this->expiration,
                'title' => \Wa::config('system.cms.title', config('webarq.projectInfo.name', 'WEBARQ'))
        ];

        \Mail::send('webarq::template.email.reset-password', $data, function ($m) use ($email, $data) {
            $m
                    ->to($email, $email)
                    ->from(
                            \Wa::config('system.email.sender', 'noreply@info.com'),
                            Wa::config('system.email.name', strip_tags($data['title']))
                    )
                    ->subject('Reset your ' . strip_tags($data['title']) . ' password!');
        });
    }

    /**
     * Action handler for reset password request, accept get method only
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function actionGetResetPassword()
    {
        $this->setLayout('reset-password');

        if ($this->getRealToken(Request::input('token'))) {
            $this->layout->with('token', Request::input('token'));
        } else {
// Mark as submit
            \Session::flash('status-password-reset', 'Invalid token!');
// Go back to previous page
            return redirect(URL::panel('system/admins/auth/forgot-password'));
        }
    }

    /**
     * Check if given token is valid
     *
     * @param $token
     * @return false|array
     */
    protected function getRealToken($token)
    {
        try {
            $token = unserialize(decrypt($token));
            $admin = AdminPasswordResetModel::whereToken($token[0])
                    ->where('expiration', '>=', date('Y-m-d H:i:s'))
                    ->get(['admin_id'])
                    ->first();

            if ($admin) {
                return [$token[0], $admin];
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Action handler for reset password request, accept post method only
     */
    public function actionPostResetPassword()
    {
        $this->setLayout('reset-password');

        if ($token = $this->getRealToken(Request::input('reset-token'))) {
            $this->layout->with('token', Request::input('reset-token'));

            $validator = \Validator::make(Request::input(), [
                    'password' => 'required|min:6|complexity|confirmed',
                    'reset-token' => 'required'
            ]);

            if ($validator->fails()) {
                $this->layout->{'messages'} = $validator->errors()->getMessages();
            } else {
// Change admin password
                $this->updateAdminPassword(Request::input('password'), $token[1]->admin_id);
// Set token as expired
                $this->tokenExpiration($token[0]);
// Mark as submit
                \Session::flash('status-password-reset', 'Success. Your password has been changed!');
// Go back to previous page
                return redirect(URL::panel('system/admins/auth/forgot-password'));
            }
        } else {
// Mark as submit
            \Session::flash('status-password-reset', 'Invalid token!');
// Go back to previous page
            return redirect(URL::panel('system/admins/auth/forgot-password'));
        }
    }

    /**
     * @param $password
     * @param $adminId
     */
    protected function updateAdminPassword($password, $adminId)
    {
        $admin = AdminModel::find($adminId);
        $admin->password = \Hash::make($password);
        $admin->save();
    }

    /**
     * @param $token
     */
    protected function tokenExpiration($token)
    {
        AdminPasswordResetModel::whereToken($token)
                ->where('expiration', '>=', date('Y-m-d H:i:s'))
                ->update(['expiration' => date('Y-m-d H:i:s', strtotime('- ' . $this->expiration))]);
    }

    public function actionGetLogout()
    {
        Auth::logout();
    }

    protected function hasPermission()
    {
        return true;
    }
}