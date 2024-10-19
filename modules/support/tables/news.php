<?php
/**
 * Created by PhpStorm
 * Date: 19/02/2017
 * Time: 12:56
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'menu_id'],
        ['master' => 'postDate'],
        ['master' => 'title', 'name' => 'banner'],
        ['master' => 'title', 'name' => 'author'],
        ['master' => 'title', 'multilingual' => true],
        ['master' => 'title', 'name' => 'permalink'],
        ['master' => 'intro', 'multilingual' => true],
        ['master' => 'description', 'multilingual' => true],
        ['master' => 'bool', 'name' => 'is_active'],
        'timestamps' => true
];