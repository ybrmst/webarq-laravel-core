<?php
/**
 * Created by PhpStorm
 * Date: 19/03/2017
 * Time: 16:06
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

return [
        'icon' => 'fa-user',
        'permalink' => null,
        'listing' => [
                'headers' => [
                        'columns' => ['username', 'email', 'is_system', 'is_active' => ['guarded' => true]]
                ],
                'where' => function ($query) {
                        if (Auth::user() && !Auth::user()->isDaemon()) {
                                $query
                                        ->join('admin_roles as dmnr', 'dmnr.admin_id', 'dmns.id')
                                        ->join('roles as rls', 'dmnr.role_id', 'rls.id')
                                        ->where('rls.role_level', '>=', Auth::user()->getLevel(true))
                                        ->groupBy('dmns.id')
                                ;
                        }
                }
        ],
// Panel allowed action
        'actions' => [
                'activeness',
                'create' => [
// Actions rules if any. This will be checking on routes while possible, or on admin base controller, or on
// the related controller it self
                        'rules' => [],
// Transaction form if any
                        'form' => [
                                'title' => 'Create Admins',
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                'system.admins.username' => [
                                        'length' => '100',
// Input rules:
//                                                        'rules' => ''
                                ],
                                'system.admins.password' => [
                                        'type' => 'password',
                                        'modifier' => 'password'
                                ],
                                'system.admins.email' => [
                                        'class' => 'email',
                                        'rules' => 'email'
                                ],
                                'system.admin_roles.role_id' => [
                                        'title' => 'Role',
                                        'type' => 'select table',
                                        'sources' => [
                                                'table' => 'roles',
                                                'column' => ['id', 'title'],
                                                'where' => function ($query) {
                                                        if (Auth::user() && !Auth::user()->isDaemon()) {
                                                                $query->where('role_level', '>=', Auth::user()->getLevel(true))
                                                                        ->whereIsAdmin(1);
                                                        }
                                                }
                                        ],
                                        'multiple',
                                        'rules' => 'required|array'
                                ]
                        ]
                ],
                'edit' => [
                        'form' => [
                                'title' => 'Edit Admins',
                                'system.admins.username' => [
                                        'length' => '100',
                                        'name' => 'user-id',
                                        'readonly'
                                ],
                                'system.admins.password' => [
                                        'type' => 'password',
                                        'modifier' => 'password',
                                        'notnull' => false,
                                        'ignored' => true
                                ],
                                'system.admins.email' => [
                                        'class' => 'email',
                                        'rules' => 'email'
                                ],
                                'system.admin_roles.role_id' => [
                                        'title' => 'Role',
                                        'type' => 'select table',
                                        'sources' => [
                                                'table' => 'roles',
                                                'column' => ['id', 'title'],
                                                'where' => function ($query) {
                                                        if (Auth::user() && !Auth::user()->isDaemon()) {
                                                                $query->where('role_level', '>=', Auth::user()->getLevel(true))
                                                                        ->whereIsAdmin(1);
                                                        }
                                                }
                                        ],
                                        'multiple',
                                        'rules' => 'required|array'
                                ]
                        ]
                ],
                'delete',
                'is_system'
        ]
];