<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 6/15/2017
 * Time: 11:10 AM
 */

return [
        'Contact Us' => [
                'first_name' => [
                        'label' => 'First Name',
                        'form' => [
                                'type' => 'text',
                                'options' => [
                                        'class' => 'form-control'
                                ],
                                'rule' => 'required',
                        ]
                ],
                'last_name' => [
                        'label' => 'Last Name',
                        'form' => [
                                'type' => 'text',
                                'options' => [
                                        'class' => 'form-control'
                                ],
                                'rule' => 'required'
                        ]
                ],
                'phone' => [
                        'label' => 'Phone',
                        'form' => [
                                'type' => 'text',
                                'options' => [
                                        'class' => 'form-control'
                                ],
                                'rule' => 'required'
                        ]
                ],
                'utm_source' => [
                        'label' => 'Utm Source'
                ],
                'utm_medium' => [
                        'label' => 'Utm Medium'
                ],
                'utm_campaign' => [
                        'label' => 'Utm Campaign'
                ],
                'utm_term' => [
                        'label' => 'Utm Term'
                ],
                'utm_content' => [
                        'label' => 'Utm Content'
                ],
                'landing_page' => [
                        'label' => 'Landing Page'
                ]
        ]
];