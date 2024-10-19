<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/29/2016
 * Time: 1:40 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'admin_id', 'reference' => 'admins.id', 'uniques' => true],
        ['master' => 'int', 'name' => 'role_id', 'uniques' => true],
        'histories' => [
                'create' => ['assigned', 'roles.title', 'admins.username'],
                'update' => ['unsigned', 'roles.title', 'admins.username'],
                'delete' => [
                        'action' => 'unassigned',
                        'item' => 'roles.title',
                        'from' => 'admins.username'
                ]
        ],
        'foreign' => [
// @todo print foreign syntax on generated migration file
// Format: column_name => references table name:references column name (by default using id)
                'admin_id' => 'admins:id',
                'role_id' => 'roles:id'
        ],
//
//        'flush-update' => true
];