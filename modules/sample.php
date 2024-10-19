<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 3:03 PM
 */

return [
        'title' => 'Samples',
        'tables' => ['samples', 'slides', 'faqs'],
        'panels' => [
// How to name our panels key member:
// 1. tableName => [settings]
// 2. title => ["table" => "tableName"], ... another settings]
// 3. title:tableName => [settings]
// and we use the third way on this example
                'sample:samples' => [
// Instead of using key as the title, just set the name that fitting your need
//                        'title' => 'Sample',
                        'listing' => [
//                                'table' => 'samples',
                                'headers' => [
                                        'columns' => [
                                                'id',
                                                'sequence',
                                                'parent_id',
                                                'title',
                                                'file' => [
// Modify value before rendering. All modifier should be
// registering in class Webarq\Manager\ValueModifierManager
                                                        'modifier' => 'thumb',
// Assign head column title
                                                        'title' => 'File',
// Column is selected, but not shown on the list
//                                                        'guarded' => true
                                                ]
                                        ],
// Add container head (normally is thead)
                                        'container' => 'thead'
                                ],
// Default listing sequence, give array for multiple column sequence
                                'sequence' => 'sequence',
// Searchable column, give array for multiple column sequence
                                'searchable' => 'title',
// Set as an array in [limit, view file name] format
                                'pagination' => 100
// Enable data driver
//                                'driver' => ['json'],
                        ],
// Is panel guarded and check if current admin has the permissions
// to accessing the panel it self
// By default it will be set in to true
                        'guarded' => true,
// Panel allowed action
                        'actions' => [
                                'activeness' => [
// Button permission, if multiple permissions is needed, then set it as an numeric array
// When multiple permissions is given, and we needed all permissions to be granted,
// then add boolean true as the last item
                                        'permissions' => ['activeness'],
// Button rules, for callback item will use two parameter (Object Admin, Array Items)
// To get row (item) value, then key should be prefixed with "item.", and "admin." to get
// admin attributes.
// To change logic operator (eg. "=, !=") then set the keys value as an array in
// [known logic operator, value] format
//                                        'rules' => [
//                                                'item.is_system' => 0
//                                        ],
// Button position location, automatically registered to the listing when not set
                                        'placement' => 'listing',
// Button HTML attributes
                                        'attributes' => [],
// Button container view
                                        'container-view' => ''
                                ],
                                'create' => [
// Transaction form if any
                                        'form' => [
// Add attribute form
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data'
                                                ],
// Add form title
                                                'title' => 'Create Sample',
                                                'sample.samples.parent_id' => [
                                                        'name' => 'parental',
// Allow system to build select input, and get options from mentioned table
                                                        'type' => 'select table',
                                                        'title' => 'Parent Menu',
                                                        'sources' => [
// Table name, while not set will get current input table
                                                                'table' => 'samples',
// Column for select option value, and select option label
                                                                'column' => ['id', 'title']
                                                        ],
// Enable option tree
// String column name which using for traversing or "true" as an alias for "parent_id"
                                                        'trees' => true,
// Default input value
//                                                        'default' => 0,
// Enable blank select option
                                                        'blank-option' => [0 => 'This is a parent sample'],

                                                ],
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                                'sample.samples.title' => [
                                                        'length' => '100',
                                                        'error-messages' => [
                                                                'required' => 'Please fill the title'
                                                        ]
// Multilingual input
// 1      : will inherited source input rules
// true   : will ignored source input rules
// string : in laravel format
// array  : will overwrite property
//                                                        'multilingual' => 1,
// Added input information
//                                                        'info' => 'Some info here'
                                                ],
                                                'sample.samples.file' => [
                                                        'permissions' => 'upload',
// Value for impermissible input
                                                        'impermissible' => '',
                                                        'file' => [
                                                                'type' => 'image',
                                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                                'max' => 1024,
                                                                'upload-dir' => 'site/uploads/media',
                                                                'resize' => [
                                                                        'width' => 200,
                                                                        'height' => 200,
                                                                ]
                                                        ],
                                                        'notnull' => true,
                                                        'default' => ''
                                                ],
                                                'sample.samples.description',
                                                'sample.samples.sequence' => [
// Do not show input on the form
//                                                        'invisible' => true,
// Uncomment this when sequence column depending on another input value
                                                        'grouping-column' => 'parental'
                                                ],
                                        ]
                                ],
                                'edit' => [
// Transaction form if any
                                        'form' => [
// Data remote getter model
//                                                'model' => true,
// Add attribute form
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data'
                                                ],
// Add form title
                                                'title' => 'Edit Sample',
                                                'sample.samples.parent_id' => [
                                                        'name' => 'parental',
// Allow system to build select input, and get options from mentioned table
                                                        'type' => 'select table',
                                                        'title' => 'Parent Sample',
                                                        'sources' => [
// Table name, while not set will get current input table
                                                                'table' => 'samples',
// Column for select option value, and select option label
                                                                'column' => ['id', 'title']
                                                        ],
// Enable option tree
// String column name which using for traversing or "true" as an alias for "parent_id"
                                                        'trees' => true,
// Default input value
//                                                        'default' => 0,
// Enable blank select option
                                                        'blank-option' => [0 => 'This is a parent sample'],
                                                        'rules' => 'not_in:' . Request::segment(7)

                                                ],
// Following by input key => attributes
// Input key should be following "moduleName.tableName.columnName" format name
                                                'sample.samples.title' => [
                                                        'length' => '100',
                                                        'value' => 'Jack'
                                                ],
                                                'sample.samples.file' => [
                                                        'permissions' => 'upload',
// Un-required input, "required" key could be replaced with "notnull"
                                                        'required' => false,
// Value for impermissible input
//                                                        'impermissible' => 'some-value',
                                                        'file' => [
                                                                'type' => 'image',
                                                                'mimes' => ['jpg', 'jpeg', 'png'],
                                                                'max' => 1024,
                                                                'upload-dir' => 'site/uploads/media',
                                                                'resize' => [
                                                                        'width' => 200,
                                                                        'height' => 200,
                                                                ]
                                                        ],
// Ignored when field is empty
                                                        'ignored' => true
                                                ],
                                                'sample.samples.description' => [
                                                        'class' => 'ckeditor',
                                                        'id' => 'editor1'
                                                ],
                                                'sample.samples.sequence' => [
// Do not show input on the form
//                                                        'invisible' => true,
// Uncomment this when sequence column depending on another input value
                                                        'grouping-column' => 'parental'
                                                ]
                                        ]
                                ],
                                'delete' => [
// Rules as callback.
// Accept two parameters which is object $admin and array $row
//                                        'rules' => function($admin, $row) {
//                                                return 1 !== array_get($row, 'id');
//                                        },
                                        'rules' => [
// How to filter an item
// Delete item when has no child, and parent column would be "parent_id"
//                                                'has-child' => false,
//                                                'parent-column' => 'parent_id',
// Using callback which is accept two parameters which is object $admin and array $row
// Delete item only when item id not identical with 1
//                                                'item.id' => function($admin, $row) {
//                                                        return 1 !== array_get($row, 'id');
//                                                },
// Delete item only when item title identical with "some title"
// or item id identical with 1
//                                                'item.title' => ['===', 'some title'],
//                                                'item.id' => ['===', 1],
// Delete item only when item id not identical with 1, and item title is not identical   "some title"
//                                                ['item.id' => ['!==', 1], 'item.title' => ['!==', 'some title']],
// Set last item to make sure all rules is valid
//                                                true
                                        ],
                                        'tables' => [
                                                'samples' => [
// The table has column(s) contain file path, and we need to delete the file(s) as well
                                                        'mime-column' => 'file',
// The table has a sequence column, fixed the others sequence while deleting the row
                                                        'sequence-column' => 'sequence:parent_id',
                                                ]
                                        ]
                                ],
                                'export' => [
                                        'placement' => ['header', 'listing'],
// Limit options, number of limit or array of [offset, number of limit]
//                                        'limit' => 10,
//                                        'columns' => ['id'],
//                                        'where' => ['id' => '3'],
// Raw data getter model (string model name or a callback)
//                                        'model' => 'modelName',
                                ],
                                'upload' => [
                                        'placement' => []
                                ]
                        ]
                ],

                'slides' => [
                        'type' => 'listing',
                        'listing' => [
                                'headers' => [
                                        'columns' => [
                                                'path' => ['modifier' => 'thumb'],
                                                'label',
                                                'permalink'
                                        ]
                                ]
                        ],
                        'actions' => [
                                'create' => [
                                        'form' => [
                                                'attributes' => [
                                                        'enctype' => 'multipart/form-data',
                                                ],
                                                'sample.slides.label',
                                                'sample.slides.permalink',
                                                'sample.slides.description' => [
                                                        'type' => 'textarea'
                                                ],
                                                'sample.slides.path' => [
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
                                                ],
                                                'sample.slides.path_tab' => [
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
                                                ],
                                                'sample.slides.path_mobile' => [
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
                                                ],
                                                'sample.slides.button' => [
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
                                                'sample.slides.label',
                                                'sample.slides.permalink',
                                                'sample.slides.description' => [
                                                        'type' => 'textarea'
                                                ],
                                                'sample.slides.path' => [
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
                                                'sample.slides.path_tab' => [
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
                                                'sample.slides.path_mobile' => [
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
                                                'sample.slides.button' => [
                                                        'type' => 'hidden',
                                                        'value' => 'on'
                                                ]
                                        ]
                                ]
                        ]
                ],
                'faqs' => [
                        'type' => 'listing',
                        'listing' => [
                                'headers' => [
                                        'columns' => [
                                                'subject',
                                                'description' => [
                                                        'modifier' => 'words:10'
                                                ]
                                        ]
                                ]
                        ],
                        'actions' => [
                                'create' => [
                                        'form' => [
                                                'sample.faqs.menu_id' => [
// Allow system to build select input, and get options from mentioned table
                                                        'type' => 'select table',
                                                        'title' => 'Menu',
                                                        'sources' => [
// Table name, while not set will get current input table
                                                                'table' => 'menus',
// Column for select option value, and select option label
                                                                'column' => ['id', 'title'],
// Where condition
                                                                'where' => [
                                                                        'template' => 'faq'
                                                                ]
                                                        ],
                                                        'trees' => true

                                                ],
                                                'sample.faqs.subject',
                                                'sample.faqs.description' => [
                                                        'class' => 'ckeditor'
                                                ],
                                                'sample.faqs.is_active',
                                                'sample.faqs.sequence' => [
                                                        'grouping-column' => 'menu_id'
                                                ]
                                        ]
                                ],
                                'edit' => [
                                        'form' => [
                                                'sample.faqs.menu_id' => [
// Allow system to build select input, and get options from mentioned table
                                                        'type' => 'select table',
                                                        'title' => 'Menu',
                                                        'sources' => [
// Table name, while not set will get current input table
                                                                'table' => 'menus',
// Column for select option value, and select option label
                                                                'column' => ['id', 'title'],
// Where condition
                                                                'where' => [
                                                                        'template' => 'faq'
                                                                ]
                                                        ],
                                                        'trees' => true

                                                ],
                                                'sample.faqs.subject',
                                                'sample.faqs.description' => [
                                                        'class' => 'ckeditor'
                                                ],
                                                'sample.faqs.is_active',
                                                'sample.faqs.sequence' => [
                                                        'grouping-column' => 'menu_id'
                                                ]
                                        ]
                                ],
                        ]
                ]
        ]
];