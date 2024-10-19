<?php
/**
 * Created by PhpStorm
 * Date: 15/02/2017
 * Time: 21:29
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

return [
        'names' => [
                'static' => [
                        'name' => 'Static',
                        'thumb' => ''
                ],
                'news' => [
                        'name' => 'News',
                        'thumb' => ''
                ],
                'leads' => [
                        'name' => 'Leads'
                ]
        ],
        'sections' => [
                'slide' => [
                        'name' => 'Slide',
                        'model' => 'slide',
                        'limit' => 0,
                        'panel' => 'slides'

                ],
                'static-pages' => [
                        'name' => 'Static Pages',
                        'view' => 'static',
//                        'model' => 'page',
                        'table' => [
                                'name' => 'pages',
                                'translate' => ['title', 'intro', 'description']
                        ],
                        'panel' => 'pages',
                        'limit' => 0,
                        'paginate' => 1
                ],
                'leads' => [
                        'name' => 'Leads Form',
                        'raw' => function() {
                                return Wa::manager('site.lead', Wa::menu()->getActive()->lead)->toHtml();
                        }
                ]
        ],
        'modals' => [
                'default' => 'Are you sure you want to do this?',
                'delete' => 'This action will remove selected item from database, and cannot be undone. Do you want to continue ?',
        ]
];