<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/16/2017
 * Time: 12:08 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'title', 'name' => 'section_id', 'notnull' => true],
        ['master' => 'title', 'name' => 'path'],
        ['master' => 'title', 'name' => 'path_tab', 'notnull' => false],
        ['master' => 'title', 'name' => 'path_mobile', 'notnull' => false],
        ['master' => 'title', 'name' => 'button', 'multilingual' => true],
        ['master' => 'longTitle', 'name' => 'permalink'],
        ['master' => 'title', 'multilingual' => true],
        ['master' => 'longTitle', 'notnull' => false, 'name' => 'description', 'multilingual' => true],
        'timestamps' => true,
        'history' => [
            'group' => 'slides',
            'item' => 'title'
        ],
];