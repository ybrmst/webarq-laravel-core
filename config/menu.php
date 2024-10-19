<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/22/2017
 * Time: 1:47 PM
 */

return [
        'wrapper' => [
                'outer-level-0' => '<ul class="sidebar-menu"></ul>',
                'outer-level-n' => '<ul class="treeview-menu"></ul>',
                'inner-no-child' => '<li></li>',
                'inner-with-child' => '<li class="treeview"></li>',
                'inner-no-child-active' => '<li class="active"></li>',
                'inner-with-child-active' => '<li class="treeview active"></li>',
                'anchor-attributes' => [],
                'anchor-active-attributes' => ['class' => 'active']
        ],
        'positions' => [
                'main' => 'Main',
                'footer' => 'Footer'
        ],
// URL segment which is use to separate menu permalink with item permalink
        'markup-url' => [
                'read'
        ],
// Non cms url
        'bypass-url' => [
                'thank-you'
        ],
        'home-menu' => false,
];