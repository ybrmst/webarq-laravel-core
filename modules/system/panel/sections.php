<?php
/**
 * Created by PhpStorm
 * Date: 05/03/2017
 * Time: 10:39
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */
return [
        'icon' => 'fa-object-group',
        'listing' => [
                'headers' => [
                        'columns' => [
                                'template',
                                'object',
                                'title'
                        ]
                ],
                'sequence' => [
                        'template' => 'asc', 'sequence' => 'asc'
                ]
        ],
        'actions' => [
                'create' => [
                        'form' => [
                                'system.sections.template' => [
                                        'type' => 'select template',
                                        'blank-option' => true
                                ],
                                'system.sections.object' => [
                                        'type' => 'select template',
                                        'blank-option' => true,
                                        'section' => true
                                ],
                                'system.sections.title',
                                'system.sections.sequence' => [
                                        'grouping-column' => 'template'
                                ]
                        ]
                ],
                'edit' => [
                        'form' => [
                                'system.sections.template' => [
                                        'type' => 'select template',
                                        'blank-option' => true
                                ],
                                'system.sections.object' => [
                                        'type' => 'select template',
                                        'blank-option' => true,
                                        'section' => true
                                ],
                                'system.sections.title',
                                'system.sections.sequence' => [
                                        'grouping-column' => 'template'
                                ]
                        ]
                ],
                'delete'
        ]
];