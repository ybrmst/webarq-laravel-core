<?php
/**
 * Created by PhpStorm
 * Date: 19/02/2017
 * Time: 12:54
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

return [
        'tables' => ['news'],
        'panels' => [
                'news' => [
                        'listing' => [
                                'headers' => [
                                        'columns' => [
                                                'banner' => [
                                                        'modifier' => 'thumb'
                                                ],
                                                'title',
                                                'intro' => [
                                                        'modifier' => 'words:10'
                                                ],
                                                'is_active'
                                        ]
                                ]
                        ],
                        'actions' => [
                                'create' => [
                                        'form' => [
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data'
                                                ],
                                                'support.news.banner' => [
                                                        'file' => [
                                                                'type' => 'image',
                                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                                'max' => 1024,
                                                                'upload-dir' => 'site/uploads/news',
                                                                'resize' => [
                                                                        'width' => 770,
                                                                        'height' => 270,
                                                                ]
                                                        ],
                                                        'info' => 'Please use image in 770px X 270px dimension',
                                                ],
                                                'support.news.post_date',
                                                'support.news.author',
                                                'support.news.title' => [
                                                        'referrer' => '.seo-url'
                                                ],
                                                'support.news.permalink' => [
                                                        'class' => 'seo-url'
                                                ],
                                                'support.news.intro',
                                                'support.news.description' => [
                                                        'class' => 'ckeditor'
                                                ],
                                                'support.news.is_active' => [
                                                        'value' => 1
                                                ]
                                        ]
                                ],
                                'edit' => [
                                        'form' => [
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data'
                                                ],
                                                'support.news.banner' => [
                                                        'file' => [
                                                                'type' => 'image',
                                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                                'max' => 1024,
                                                                'upload-dir' => 'site/uploads/news',
                                                                'resize' => [
                                                                        'width' => 770,
                                                                        'height' => 270,
                                                                ]
                                                        ],
                                                        'info' => 'Please use image in 770px X 270px dimension',
                                                        'notnull' => false,
                                                        'ignored' => true
                                                ],
                                                'support.news.post_date',
                                                'support.news.author',
                                                'support.news.title' => [
                                                        'referrer' => '.seo-url'
                                                ],
                                                'support.news.permalink' => [
                                                        'class' => 'seo-url'
                                                ],
                                                'support.news.intro',
                                                'support.news.description' => [
                                                        'class' => 'ckeditor'
                                                ],
                                                'support.news.is_active' => [
                                                        'value' => 1
                                                ]
                                        ]
                                ],
                                'activeness',
                                'delete'
                        ]
                ]
        ]
];