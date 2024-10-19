<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/29/2016
 * Time: 12:53 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'smallInt', 'name' => 'role_level', 'notnull' => true],
        ['master' => 'uShortTitle'],
        ['master' => 'falseBool', 'name' => 'is_admin'],
        ['master' => 'falseBool', 'name' => 'is_system'],
        ['master' => 'bool', 'name' => 'is_active'],
// This table has create & edit timestamp column. Add it automatically
        'timestamps' => true,
// For log admin
        'histories' => [
                'group' => 'role',
                'item' => 'title'
        ]
];