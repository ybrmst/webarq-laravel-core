<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/14/2017
 * Time: 3:39 PM
 */

namespace Webarq\Http\Controllers\Panel\System\Roles;


use Illuminate\Support\Arr;
use Wa;
use Webarq\Http\Controllers\Panel\BaseController;
use Webarq\Model\PermissionModel;

class PermissionController extends BaseController
{
    public function actionGetIndex()
    {
        $find = $this->panel->getModel()->find($this->getParam(1));
        $permissions = $assigns = [];

        if (null !== $find) {
// Get current editing role permissions
            if ($find->permissions->count()) {
                foreach ($find->permissions as $item) {
                    $permissions[$item->module][$item->panel][$item->permission] = 1;
                }
            }
// Get current login admin permissions
            $find = PermissionModel::whereIn('role_id', $this->admin->getRoleId())
                    ->select('module', 'panel', 'permission')
                    ->get();

            if ($find->count()) {
                foreach ($find as $item) {
                    $assigns[$item->module][$item->panel][$item->permission] = 1;
                }
            }
        }

        $this->layout->{'rightSection'} = view('webarq::system.roles.permission', compact('permissions', 'assigns'));
    }

    public function actionPostIndex()
    {
        $post = \Request::input();
// Remove token
        array_forget($post, ['_token', 'toggle']);
// Remote value should be exist
        if (null === ($remote = array_pull($post, 'remote-value'))) {
            return $this->actionGetForbidden();
        }
        $remote = Arr::deserialize($remote);

        $new = $this->assignPermission($remote, $post);

        $del = $this->unAssignPermission($remote, $post);

        if (!empty($new) || !empty($del)) {
            $this->setTransactionMessage(Wa::trans('webarq::core.messages.success-update'), 'success');
        } else {
            $this->setTransactionMessage(Wa::trans('webarq::core.messages.invalid-update'), 'warning');
        }

        return redirect(\Request::url());
    }

    /**
     * @param array $remote
     * @param array $post
     * @return bool
     */
    protected function assignPermission(array $remote, array $post)
    {
        if ([] !== $post) {
            $rows = [];
            foreach ($post as $module => $panels) {
                foreach ($panels as $panel => $actions) {
                    foreach ($actions as $action => $value) {
                        if (null === array_get($remote, $module . '.' . $panel . '.' . $action)) {
                            $rows[] = [
                                    'role_id' => $this->getParam(1),
                                    'panel' => $panel,
                                    'module' => $module,
                                    'permission' => $action,
                                    'create_on' => Wa::modifier('wa-datetime')
                            ];
                        }
                    }
                }
            }
            if ([] !== $rows) {
                return PermissionModel::insert($rows);
            }
        }

        return false;
    }

    /**
     * @param array $remote
     * @param array $post
     * @return bool
     */
    protected function unAssignPermission(array $remote, array $post)
    {
        if ([] === $post) {
            return PermissionModel::whereRoleId($this->getParam(1))->delete();
        }

        $items = [];

        foreach ($remote as $module => $panels) {
            foreach ($panels as $panel => $actions) {
                foreach ($actions as $action => $value) {
                    if (null === array_get($post, $module . '.' . $panel . '.' . $action)) {
                        $items[$module][$panel][] = $action;
                    }
                }
            }
        }

        if ([] !== $items) {
            $query = PermissionModel::where('role_id', $this->getParam(1))
                    ->where(function ($builder) use ($items) {
                        foreach ($items as $module => $panels) {
                            foreach ($panels as $panel => $actions) {
                                $builder->orWhere(function ($builder) use ($panel, $actions) {
                                    $builder->wherePanel($panel)->whereIn('permission', $actions);
                                });
                            }
                        }

                    });
            return $query->delete();
        }

        return false;
    }
}