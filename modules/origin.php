<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/31/2017
 * Time: 1:41 PM
 */

return [
// Here we will register all alias which will be used over the system.		
        'alias' => [
                'sectionMenu' => [
                        'menus as mn.title as menu' => [
                                'on' => ['section_id', 'like', DB::raw("CONCAT(mn.`id`, '.%')")],
                                'title' => 'Menu'
                        ]
                ],
                'sectionItem' => [
                        'sections as sc.title as section' => [
                                'on' => ['section_id', 'like', DB::raw("CONCAT('%.', sc.id) ")],
                                'title' => 'Section',
                                'guarded' => true
                        ]
                ]
		]
];