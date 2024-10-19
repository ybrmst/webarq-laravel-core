<?php
/**
 * Created by PhpStorm
 * Date: 15/02/2017
 * Time: 21:37
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */
return [
        'icon' => 'fa-bars',
        'type' => 'listing',
        'listing' => [
                'headers' => [
                        'columns' => [
                                'parent_id',
                                'is_active',
                                'sequence',
                                'title',
                                'permalink',
                                'external_link',
                                'template'
                        ]
                ],
                'sequence' => [
                        'parent_id', 'sequence'
                ],
                'tree' => [
                        'root' => 'id',
                        'branch' => 'parent_id'
                ],
                'data-tables' => true
        ],
        'actions' => [
                'create' => [
                        'form' => [
                                'system.menus.parent_id' => [
                                        'name' => 'parental',
// Allow system to build select input, and get options from mentioned table
                                        'type' => 'select table',
                                        'title' => 'Parent Menu',
// For select table we need to define this
                                        'sources' => [
// Table name, while not set will get current input table
                                                'table' => 'menus',
// Column for select option value, and select option label
                                                'column' => ['id', 'title']
                                        ],
                                        'trees' => true,
// These items should be exist in our option
                                        'needs' => 'This is a parent menu',
                                        'blank-option' => [0 => 'This is a parent menu'],
// Set another <option> tags attributes besides value and select
// Note to do this, we have to extends laravel collective through macro
                                        'option-attributes' => [
                                                'data-link' => ':permalink'
                                        ]

                                ],
                                'system.menus.title' => [
                                        'referrer' => '.seo-url'
                                ],
                                'system.menus.template' => [
                                        'type' => 'select template',
                                        'class' => 'show-hide-element',
                                        'data-show' => json_encode(['leads' => ['.menu-lead'], 1]),
                                        'data-hide' => json_encode(['x-000' => ['.menu-lead']])
                                ],
                                'system.menus.lead' => [
                                        'class' => 'menu-lead',
                                        'type' => 'select',
                                        'options' => array_combine(
                                                array_keys(config('webarq.leads', [])),
                                                array_keys(config('webarq.leads', [])))
                                ],
                                'system.menus.permalink' => [
                                        'type' => 'text',
                                        'class' => 'seo-url'
                                ],
                                'system.menus.meta_title',
                                'system.menus.meta_description',
                                'system.menus.is_active' => [
                                        'value' => 1
                                ],
                                'system.menus.sequence' => [
                                        'grouping-column' => 'parental'
                                ],
                                'system.menu_positions.position' => [
                                        'title' => 'Position',
                                        'type' => 'select',
                                        'options' => config('webarq.menu.positions'),
                                        'multiple',
                                        'rules' => 'required|array',
                                        'length' => null,
                                        'info' => 'Hold on ctrl while selecting, to add multiple item'
                                ]
                        ]
                ],
                'edit' => [
                        'form' => [
                                'system.menus.parent_id' => [
                                        'name' => 'parental',
// Allow system to build select input, and get options from mentioned table
                                        'type' => 'select table',
                                        'title' => 'Parent Menu',
                                        'sources' => [
// Table name, while not set will get current input table
                                                'table' => 'menus',
// Column for select option value, and select option label
                                                'column' => ['id', 'title']
                                        ],
                                        'blank-option' => [0 => 'This is a parent menu'],
                                        'trees' => true

                                ],
                                'system.menus.title' => [
                                        'referrer' => '.seo-url'
                                ],
                                'system.menus.template' => [
                                        'type' => 'select template',
                                        'class' => 'show-hide-element',
                                        'data-show' => json_encode(['leads' => ['.menu-lead'], 1]),
                                        'data-hide' => json_encode(['x-000' => ['.menu-lead']])
                                ],
                                'system.menus.lead' => [
                                        'class' => 'menu-lead',
                                        'type' => 'select',
                                        'options' => array_combine(
                                                array_keys(config('webarq.leads', [])),
                                                array_keys(config('webarq.leads', [])))
                                ],
                                'system.menus.permalink' => [
                                        'type' => 'text',
                                        'class' => 'seo-url'
                                ],
                                'system.menus.meta_title',
                                'system.menus.meta_description',
                                'system.menus.is_active',
                                'system.menus.sequence' => [
                                        'grouping-column' => 'parental'
                                ],
                                'system.menu_positions.position' => [
                                        'title' => 'Position',
                                        'type' => 'select',
                                        'options' => config('webarq.menu.positions'),
                                        'multiple',
                                        'rules' => 'required|array',
                                        'length' => null,
                                        'info' => 'Hold on ctrl while selecting, to add multiple item'
                                ]
                        ]
                ],
                'activeness',
                'delete',
                'section' => [
                        'permalink' => true
                ]
        ]
];