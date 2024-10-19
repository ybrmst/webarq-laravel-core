<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/2/2017
 * Time: 10:14 AM
 */

/**
 * Storing history remote data both of the old and latest data
 */

return [
        ['master' => 'id'],
        ['master' => 'int', 'name' => 'history_id', 'notnull' => true],
        ['master' => 'longDescription', 'name' => 'oldest', 'notnull' => true],
        ['master' => 'longDescription', 'name' => 'latest', 'notnull' => true]
];