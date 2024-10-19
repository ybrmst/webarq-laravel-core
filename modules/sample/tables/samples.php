<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/25/2017
 * Time: 3:48 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'parent_id', 'notnull' => true],
        ['master' => 'title', 'name' => 'title', 'multilingual' => true],
        ['master' => 'title', 'name' => 'file', 'multilingual' => [
                'notnull' => false
        ]],
        ['master' => 'description', 'multilingual' => true],
        ['master' => 'sequence'],
        ['master' => 'bool', 'name' => 'is_active'],
        'timestamps' => true,
// Use this as class model, i.o the generated one
// By default model name is singularisation of table name
//        'model' => 'media',
// Model directory
        'model-dir' => 'sample',
        'foreign' => [
/**
 * @todo print foreign syntax on generated migration file
 * Format: column_name => references table name:references column name (by default using id)
 * Also these foreign keys will be using while updating table
 **/
//                'admin_id' => 'admins:id',
//                'role_id' => 'roles:id'
        ],
// Table histories, will be useful while do some database transaction like create, edit, etc
        'histories' => [
                'item' => 'title'
        ],
// Delete row by master table references key before updating table
//        'flush-update' => true
];