<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/24/2017
 * Time: 5:09 PM
 */

return [
        'icon' => 'fa-sliders',
        'type' => 'listing',
        'listing' => [
                'headers' => [
                        'columns' => [
                                ':sectionMenu',
                                'section_id',
                                'path' => ['modifier' => 'thumb:90%', 'style' => 'width:30%'],
                                'title' => ['modifier' => 'html_entity_decode|strip_tags'],
                                'permalink'
                        ]
                ],
                'searchable' => 'title, section_id, mn.title'
        ],
        'actions' => [
                'create' => [
                        'form' => [
                                'attributes' => [
                                        'enctype' => 'multipart/form-data',
                                ],
                                'section.slides.section_id' => [
                                        'type' => 'select template',
                                        'title' => 'Menu Section',
                                        'section' => 'slide',
                                        'notnull' => true
                                ],
                                'section.slides.title',
                                'section.slides.permalink',
                                'section.slides.description' => [
                                        'type' => 'textarea',
                                        'class' => 'ckeditor'
                                ],
                                'section.slides.path' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 1024,
                                                'upload-dir' => 'site/uploads/slides',
                                                'resize' => [
                                                        'width' => 1920,
                                                        'height' => 990,
                                                ]
                                        ],
                                ],
                                'section.slides.path_tab' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 1024,
                                                'upload-dir' => 'site/uploads/slides',
                                                'resize' => [
                                                        'width' => 480,
                                                        'height' => 396,
                                                ]
                                        ],
                                ],
                                'section.slides.path_mobile' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 1024,
                                                'upload-dir' => 'site/uploads/slides',
                                                'resize' => [
                                                        'width' => 480,
                                                        'height' => 396,
                                                ]
                                        ],
                                        'required' => null,
                                        'default' => ''
                                ],
                                'section.slides.button' => [
                                        'type' => 'hidden',
                                        'value' => 'on'
                                ]
                        ]
                ],


                'edit' => [
                        'form' => [
                                'attributes' => [
                                        'enctype' => 'multipart/form-data',
                                ],
                                'section.slides.section_id' => [
                                        'type' => 'select template',
                                        'title' => 'Menu Section',
                                        'section' => 'slide',
                                        'notnull' => true
                                ],
                                'section.slides.title',
                                'section.slides.permalink',
                                'section.slides.description' => [
                                        'type' => 'textarea',
                                        'class' => 'ckeditor'
                                ],
                                'section.slides.path' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 1024,
                                                'upload-dir' => 'site/uploads/slides',
                                                'resize' => [
                                                        'width' => 1440,
                                                        'height' => 878,
                                                ]
                                        ],
                                        'required' => false,
                                        'ignored' => true
                                ],
                                'section.slides.path_tab' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 1024,
                                                'upload-dir' => 'site/uploads/slides',
                                                'resize' => [
                                                        'width' => 1440,
                                                        'height' => 878,
                                                ]
                                        ],
                                        'required' => false,
                                        'ignored' => true
                                ],
                                'section.slides.path_mobile' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 1024,
                                                'upload-dir' => 'site/uploads/slides',
                                                'resize' => [
                                                        'width' => 1440,
                                                        'height' => 878,
                                                ]
                                        ],
                                        'required' => false,
                                        'ignored' => true
                                ],
                                'section.slides.button' => [
                                        'type' => 'hidden',
                                        'value' => 'on'
                                ]
                        ]
                ],
                'delete'
        ]
];