<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/29/2016
 * Time: 12:57 PM
 */

return [
        'icon' => 'fa-users',
        'permalink' => null,
// When not set, will translate group name
        'label' => 'Roles',
        'listing' => [
                'headers' => [
                        'columns' => [
                                'role_level' => ['title' => 'Level'],
                                'title',
                                'is_admin',
                                'is_active',
                                'is_system' => [
// Column is selected, but not shown on the list
                                        'guarded' => true
                                ]
                        ],
                ],
// Default listing sequence, give array for multiple column sequence
                'sequence' => 'role_level',
// Searchable column, give array for multiple column sequence
                'searchable' => 'title',
        ],
// Panel allowed action
        'actions' => [
                'activeness' => [
// Button permission, if multiple permissions is needed, then set it as an numeric array
// When multiple permissions is given, and we needed all permissions to be granted,
// then add boolean true as the last item
                        'permissions' => ['activeness'],
// Button rules, for callback item will use two parameter (Object Admin, Array Items)
// To get row (item) value, then key should be prefixed with "item.", and "admin." to get
// admin attributes.
// To change logic operator (eg. "=, !=") then set the keys value as an array in
// [known logic operator, value] format
                        'rules' => function() {
                                return false;
                        },
// Button position location, automatically registered to the listing when not set
                        'placement' => 'listing',
// Button HTML attributes
                        'attributes' => [],
// Button container view
                        'container-view' => ''
                ],
                'create' => [
// Transaction form if any
                        'form' => [
// Form title
// @todo default value when not set
                                'title' => 'Create Role',
// Extend your form attributes
                                'attributes' => [],
// Input key should be following "moduleName.tableName.columnName" format name
                                'system.roles.role_level' => [
                                        'name' => 'level',
                                        'type' => 'text',
// Input title
                                        'title' => 'Role Level',
                                        'rules' => 'max:255|min:10',
                                        'error-messages' => [
                                                'required' => 'Role level should not be empty'
                                        ],
                                        'info' => 'For best practice, please use simple number which is easy '
                                                . 'to remember. Eg 10, 20, ...',
                                ],
                                'system.roles.title',
                                'system.roles.is_admin' => [
                                        'required'
                                ],
                                'system.roles.is_active' => [
// Mean current login admin must have activeness permission
                                        'permissions' => 'activeness',
// Impermissible value, used when input permission not fulfilled
                                        'impermissible' => 0,
                                ],
                                'system.roles.is_system' => [
                                        'permissions' => 'is_system'
                                ]
                        ]
                ],
                'edit' => [
// Allowing parameter in permalink "?" mean null as for permalink while "." mean true
//                        'permalink' => '?id,role_level',
// Button rules
                        'rules' => [
                                'admin.level' => ['<', 'item.role_level']
                        ],
// Transaction form if any
                        'form' => [
                                'title' => 'Edit Role',
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                'system.roles.role_level' => [
                                        'name' => 'level',
                                        'type' => 'text',
                                        'label' => 'Level',
                                        'rules' => 'max:255|min:10',
                                        'error-messages' => [
                                                'required' => 'Role level should not be empty'
                                        ],
                                        'info' => 'For best practice, please use simple number which is easy '
                                                . 'to remember. Eg 10, 20, ...',
                                ],
                                'system.roles.title',
                                'system.roles.is_admin' => [
                                        'required'
                                ],
                                'system.roles.is_active' => [
// Mean current login admin must have activeness permission
                                        'permissions' => 'activeness',
// Guarded value, use when input permission not fulfilled
                                        'guarded-value' => 0
                                ],
                                'system.roles.is_system' => [
                                        'permissions' => 'is_system'
                                ]
                        ]
                ],
                'delete' => [
                        'rules' => [
                                ['admin.level' => ['<', 'item.role_level']]
                        ]
                ],
                'is_system',
                'permission' => [
                        'permalink' => true,
                        'rules' => [
                                ['admin.level' => ['<','item.role_level']]
                        ]
                ]
        ]
];