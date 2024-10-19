<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/16/2017
 * Time: 12:06 PM
 */



return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'menu_id'],
        ['master' => 'title', 'name' => 'subject', 'length' => 255, 'multilingual' => true],
        ['master' => 'description', 'multilingual' => true],
        ['master' => 'falseBool', 'name' => 'is_active'],
        ['master' => 'sequence'],
        'timestamps' => true,
        'histories' => [
                'group' => 'pages',
                'item' => 'subject',
        ]
];