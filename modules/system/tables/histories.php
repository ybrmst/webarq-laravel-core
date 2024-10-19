<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/5/2017
 * Time: 3:56 PM
 */

return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'parent_id', 'notnull' => true],
        ['master' => 'smallInt', 'name' => 'role_level', 'notnull' => true],
        ['master' => 'shortTitle', 'name' => 'action', 'notnull' => true],
        ['master' => 'shortTitle', 'name' => 'actor', 'notnull' => true],
        ['master' => 'description', 'name' => 'properties', 'notnull' => true],
        ['master' => 'createOn']
];