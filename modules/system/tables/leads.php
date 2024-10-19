<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 6/15/2017
 * Time: 11:14 AM
 */

return [
        ['master' => 'id'],
        ['master' => 'title', 'name' => 'lead_type', 'notnull' => true],
        ['master' => 'longTitle', 'name' => 'landing_page', 'notnull' => true],
        ['master' => 'description', 'name' => 'lead_data', 'notnull' => true],
        ['master' => 'description', 'name' => 'lead_value'],
        ['master' => 'falseBool', 'name' => 'statuses'],
        'timestamps' => true
];