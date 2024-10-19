<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 3:23 PM
 */

return [
    /*
     * Site functionality
     */
        'menus' => [
                [
                        'id' => 1,
                        'parent_id' => 0,
                        'title' => 'Home',
                        'permalink' => '/',
                        'template' => 'static',
                        'meta_title' => 'Home {Title}',
                        'is_active' => 1,
                        'sequence' => 1,
                        'create_on' => '2016-12:21 10:00'
                ]
        ],

        'menu_positions' => [
                [
                        'menu_id' => 1,
                        'position' => 'main',
                        'create_on' => '2016-12:21 10:00'
                ]
        ],

        'sections' => [
                [
                        'id' => 1,
                        'template' => 'static',
                        'object' => 'static-pages',
                        'title' => 'Static Page',
                        'sequence' => 1,
                        'create_on' => '2016-12:21 10:00'
                ]
        ],

        'pages' => [
                [
                        'id' => 1,
                        'section_id' => '1.1',
                        'title' => 'Welcome',
                        'intro' => 'Inspire your self',
                        'description' => 'Do not be anxious about tomorrow, for tomorrow will be anxious for itself. '
                                . ' Let the day\'s own trouble be sufficient for the day',
                        'create_on' => '2016-12:21 10:00'
                ]
        ],

        'configurations' => [
                [
                        'id' => 2,
                        'module' => 'system',
                        'key' => 'siteLogo',
                        'setting' => 'site/uploads/logo/58d4ed26b9a8c-logo.png',
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 3,
                        'module' => 'system',
                        'key' => 'favicon',
                        'setting' => 'favicon.ico',
                        'create_on' => '2016-12:21 10:00'
                ]
        ],

    /*
     * Panel functionality
     */
        'roles' => [
                [
                        'id' => 1,
                        'title' => 'superadmin',
                        'role_level' => 10,
                        'is_system' => 1,
                        'is_admin' => 1,
                        'is_active' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 2,
                        'title' => 'administrator',
                        'role_level' => 20,
                        'is_admin' => 1,
                        'is_active' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 3,
                        'title' => 'support',
                        'role_level' => 30,
                        'is_admin' => 1,
                        'is_active' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 4,
                        'title' => 'visitor',
                        'role_level' => 999,
                        'is_active' => 1,
                        'create_on' => '2016-12:21 10:00'
                ]
        ],
        'admins' => [
                [
                        'id' => 1,
                        'username' => 'superadmin',
                        'password' => Hash::make('superadmin'),
                        'email' => 'su@webmail.com', 'is_system' => 0,
                        'is_system' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 2, 'username' => 'administrator',
                        'password' => Hash::make('administrator'),
                        'email' => 'ad@webmail.com',
                        'is_system' => 1,
                        'create_on' => '2016-12:21 10:00'
                ],
                [
                        'id' => 3,
                        'username' => 'support',
                        'password' => Hash::make('support'),
                        'email' => 'sr@webmail.com',
                        'is_system' => 1,
                        'create_on' => '2016-12:21 10:00'
                ]
        ],
        'admin_roles' => [
                ['admin_id' => 1, 'role_id' => 1],
                ['admin_id' => 2, 'role_id' => 2],
                ['admin_id' => 3, 'role_id' => 3]
        ],
        'permissions' => [
                ['role_id' => 1, 'module' => 'system', 'panel' => 'configurations', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],

                ['role_id' => 1, 'module' => 'system', 'panel' => 'admins', 'permission' => 'create', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'admins', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'admins', 'permission' => 'activeness', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'admins', 'permission' => 'delete', 'create_on' => '2016-12:21 10:00'],

                ['role_id' => 1, 'module' => 'system', 'panel' => 'roles', 'permission' => 'create', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'roles', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'roles', 'permission' => 'activeness', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'roles', 'permission' => 'permission', 'create_on' => '2016-12:21 10:00'],

                ['role_id' => 1, 'module' => 'system', 'panel' => 'menus', 'permission' => 'create', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'menus', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'menus', 'permission' => 'activeness', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'menus', 'permission' => 'delete', 'create_on' => '2016-12:21 10:00'],

                ['role_id' => 1, 'module' => 'system', 'panel' => 'pages', 'permission' => 'create', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'pages', 'permission' => 'edit', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'pages', 'permission' => 'activeness', 'create_on' => '2016-12:21 10:00'],
                ['role_id' => 1, 'module' => 'system', 'panel' => 'pages', 'permission' => 'delete', 'create_on' => '2016-12:21 10:00'],
        ]
];