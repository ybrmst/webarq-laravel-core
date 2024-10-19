<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/16/2017
 * Time: 10:02 AM
 */

return [
        ['master' => 'id'],
        ['master' => 'title', 'name' => 'section_id', 'notnull' => true],
        ['master' => 'longTitle', 'multilingual' => true, 'length' => 500],
        ['master' => 'intro', 'multilingual' => true],
        ['master' => 'description', 'multilingual' => true],
        'timestamps' => true,
        'histories' => [
                'group' => 'pages',
                'item' => 'title',
        ]
];