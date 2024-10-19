<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 11/29/2016
 * Time: 1:56 PM
 */

return [
//Message log
        'log-message' => [
                'create' => ':actor create :group ":item"',
                'update' => ':actor update :group ":item"',
                'delete' => ':actor delete :group ":item"',
                'upload' => ':actor upload :media',
                'assigned' => ':actor assigned :item to :object',
                'unassigned' => ':actor unassigned :item from :object',
        ],

        'messages' => [
                'invalid-update' => 'There is no data to update',
                'success-update' => 'Congratulations. Your data has been updated',
                'success-insert' => 'Congratulations. Your data has been inserted',
                'item-not-found' => 'Item not found',
        ],

        'title' => [
                'id' => '#',
                'no' => '#',
                'welcome' => 'Welcome',
                'title' => 'Title',
                'narration' => 'Narration',
                'description' => 'Description',
                'parent_id' => 'Parent',
                'permalink' => 'Permalink',
                'is_system' => 'Is System',
                'is_active' => 'Is Active',
                'is_admin' => 'Is Admin',
                'create' => 'Create :item',
                'edit' => 'Edit :item',
                'username' => 'User Name',
                'first_name' => 'First Name',
                'password' => 'Password',
                'email' => 'Email',
                'address' => 'Address',
                'actionButton' => 'Action',
                'sequence' => 'Sequence',
                'create_on' => 'Create On',
                'last_update' => 'Last Update',
                'home' => 'Home'
        ]
];