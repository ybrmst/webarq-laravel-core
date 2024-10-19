<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/15/2017
 * Time: 4:59 PM
 */

return [
        'listing' => [
                'headers' => [
                        'columns' => [
                                'small_icon' => [
                                        'modifier' => 'thumb'
                                ],
                                'big_icon' => [
                                        'modifier' => 'thumb'
                                ],
                                'permalink'
                        ]
                ],
                'sequence' => ['sequence']
        ],

        'actions' => [
                'create' => [
                        'form' => [
                                'attributes' => [
                                        'enctype' => 'multipart/form-data',
                                ],
                                'contact.socials.title',
                                'contact.socials.small_icon' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 512,
                                                'upload-dir' => 'site/uploads/socials',
                                                'resize' => [
                                                        'width' => 28,
                                                        'height' => 44
                                                ]
                                        ],
                                        'info' => 'Image recommendation size: 28px width and 44px height',
                                ],
                                'contact.socials.float_icon' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 512,
                                                'upload-dir' => 'site/uploads/socials',
                                                'resize' => [
                                                        'width' => 28,
                                                        'height' => 44
                                                ]
                                        ],
                                        'info' => 'Image recommendation size: 28px width and 44px height',
                                ],
                                'contact.socials.big_icon' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 512,
                                                'upload-dir' => 'site/uploads/socials',
                                                'resize' => [
                                                        'width' => 47,
                                                        'height' => 70
                                                ]
                                        ],
                                        'info' => 'Image recommendation size: 47px width and 70px height',
                                ],
                                'contact.socials.permalink' => [
                                        'rules' => 'url'
                                ],
                                'contact.socials.is_active',
                                'contact.socials.sequence'
                        ],
                        'rules' => [
                                'max-row' => 4
                        ]
                ],

                'edit' => [
                        'form' => [
                                'attributes' => [
                                        'enctype' => 'multipart/form-data',
                                ],
                                'contact.socials.title',
                                'contact.socials.small_icon' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 512,
                                                'upload-dir' => 'site/uploads/socials',
                                                'resize' => [
                                                        'width' => 28,
                                                        'height' => 44
                                                ]
                                        ],
                                        'info' => 'Image recommendation size: 28px width and 44px height',
                                        'notnull' => false,
                                        'ignored' => true
                                ],
                                'contact.socials.float_icon' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 512,
                                                'upload-dir' => 'site/uploads/socials',
                                                'resize' => [
                                                        'width' => 28,
                                                        'height' => 44
                                                ]
                                        ],
                                        'info' => 'Image recommendation size: 28px width and 44px height',
                                        'notnull' => false,
                                        'ignored' => true
                                ],
                                'contact.socials.big_icon' => [
                                        'file' => [
                                                'type' => 'image',
                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                'max' => 512,
                                                'upload-dir' => 'site/uploads/socials',
                                                'resize' => [
                                                        'width' => 47,
                                                        'height' => 70
                                                ]
                                        ],
                                        'info' => 'Image recommendation size: 47px width and 70px height',
                                        'notnull' => false,
                                        'ignored' => true
                                ],
                                'contact.socials.permalink' => [
                                        'rules' => 'url'
                                ],
                                'contact.socials.is_active',
                                'contact.socials.sequence'
                        ]
                ],

                'delete',

                'activeness'
        ]
];