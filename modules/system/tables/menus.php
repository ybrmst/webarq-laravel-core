<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/14/2016
 * Time: 10:11 AM
 */

return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'parent_id'],
        ['master' => 'title', 'name' => 'lead', 'notnull' => false],
        ['master' => 'title', 'multilingual' => true],
        ['master' => 'uLongTitle', 'name' => 'permalink', 'multilingual' => true],
        ['master' => 'longTitle', 'name' => 'external_link', 'notnull' => false],
        ['master' => 'title', 'name' => 'template'],
        ['master' => 'longTitle', 'name' => 'meta_title', 'multilingual' => true],
        ['master' => 'shortIntro', 'name' => 'meta_description', 'multilingual' => true],
        ['master' => 'falseBool', 'name' => 'is_active'],
        ['master' => 'falseBool', 'name' => 'is_system'],
        ['master' => 'sequence'],
        'timestamps' => true,
        'histories' => [
                'group' => 'menu',
                'item' => 'title'
        ]
];