<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 12:18 PM
 */

include_once "function.php";

$bypass = false;
foreach (config('webarq.system.bypass-url', []) as $group) {
    if ((ends_with($group, '*') && substr($group, 0, -1) === Request::segment(1))
            || ends_with(Request::fullUrl(), $group)
    ) {
        $bypass = true;
        break;
    }
}

if (!$bypass) {
    Route::group(['prefix' => config('webarq.system.panel-url-prefix', 'admin-cp'), 'middleware' => 'web'], function () {
        webarqAutoRoute('Panel', 'system', 'dashboard');
    });

    Route::group(['middleware' => 'web'], function () {
//    foreach (config('webarq.menu.routes', []) as $method => $collections) {
//        switch (strtolower($method)) {
//            case 'get':
//                foreach ($collections as $url => $path) {
//                    Route::get($url, $path);
//                }
//                break;
//            case 'post':
//                foreach ($collections as $url => $path) {
//                    Route::post($url, $path);
//                }
//                break;
//        }
//    }

        webarqAutoRoute('Site', 'home');
    });
}