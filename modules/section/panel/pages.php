<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/16/2017
 * Time: 10:19 AM
 */

return [
        'icon' => 'fa-sticky-note',
        'type' => 'listing',
        'listing' => [
                'headers' => [
                        'columns' => [
                                ':sectionMenu',
                                'section_id',
                                'title' => [
                                        'modifier' => 'html_entity_decode|strip_tags'
                                ],
                                'description' => [
                                        'modifier' => 'words:10'
                                ]
                        ]
                ],
                'searchable' => 'title, description, section_id, mn.title'
        ],
        'actions' => [
                'create' => [
                        'form' => [
                                'section.pages.section_id' => [
                                        'type' => 'select template',
                                        'title' => 'Menu Section',
                                        'section' => 'static-pages'
                                ],
                                'section.pages.title',
                                'section.pages.intro',
                                'section.pages.description' => [
                                        'class' => 'ckeditor'
                                ],
                        ]
                ],

                'edit' => [
                        'form' => [
                                'section.pages.section_id' => [
                                        'type' => 'select template',
                                        'title' => 'Menu Section',
                                        'section' => 'static-pages'
                                ],
                                'section.pages.title',
                                'section.pages.intro',
                                'section.pages.description' => [
                                        'class' => 'ckeditor'
                                ],
                        ]
                ]
        ]
];