<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/8/2017
 * Time: 10:15 AM
 */

return [
        'title' => 'Contact',
        'tables' => [
                'submitted_forms',
                'socials'
        ],
        'panels' => [
                'config' => [
                        'type' => 'configuration',
                        'title' => 'Configuration',
                        'actions' => [
                                'edit' => [
                                        'form' => [
                                                'officeHq' => [
                                                        'title' => 'Address',
                                                        'type' => 'textarea',
                                                        'class' => 'ckeditor'
                                                ],
                                                'officePhone' => [
                                                        'type' => 'text',
                                                        'title' => 'Phone Number'
                                                ],
                                                'officeFax' => [
                                                        'type' => 'text',
                                                        'title' => 'Fax Number'
                                                ],
                                                'officeLat' => [
                                                        'type' => 'text',
                                                        'info' => 'Please input your google map latitude point',
                                                        'title' => 'Latitude',
                                                        'notnull' => true,
                                                ],
                                                'officeLong' => [
                                                        'type' => 'text',
                                                        'info' => 'Please input your google map longitude point',
                                                        'title' => 'Longitude',
                                                        'notnull' => true,
                                                ],
                                                'emailReceiver' => [
                                                        'type' => 'text',
                                                        'rules' => 'email',
                                                        'title' => 'Contact Form Email',
                                                        'error-messages' => [
                                                                'email' => 'Contact form email must be a valid email address.'
                                                        ]
                                                ],
                                                'saveSubmitted' => [
                                                        'type' => 'select',
                                                        'title' => 'Save Submitted Form',
                                                        'value' => '0',
                                                ],
                                                'callback' => function (array $post = []) {
//                                                        if ('1' === array_get($post, 'saveSubmitted')) {
//                                                                exec('php ../artisan migrate', $ouput);
//                                                        }
                                                }
                                        ]
                                ]
                        ]
                ],

                'submitted_forms' => [
                        'listing' => [
                                'headers' => [
                                        'columns' => [
                                                'person',
                                                'email',
                                                'wedding_date',
                                                'phone',
                                                'message' => [
                                                        'modifier' => 'strip_tags|words:10'
                                                ],
                                                'create_on' => [
                                                        'title' => 'Post Date',
                                                        'modifier' => 'wa-date:M d, Y'
                                                ]
                                        ]
                                ]
                        ]
                ],

                'socials'
        ]
];