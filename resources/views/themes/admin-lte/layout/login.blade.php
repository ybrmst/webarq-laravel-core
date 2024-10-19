<?php

/**
 * Created by PhpStorm
 * Date: 14/01/2017
 * Time: 14:45
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */ ?>

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Please Login!</title>

    <link rel="shortcut icon" type="images/x-icon" href="{{ URL::asset(Wa::config('system.favicon')) }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/webarq/admin-lte/login/css/main.css') }}"/>

    <!--[if IE 7]>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/webarq/admin-lte/login/css/style_ie7.css') }}"/>
    <![endif]-->
    <!--[if IE 8]>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/webarq/admin-lte/login/css/style_ie8.css') }}"/>
    <![endif]-->
    <!--[if IE 9]>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('vendor/webarq/admin-lte/login/css/style_ie9.css') }}"/>
    <![endif]-->
</head>

<body class="hold-transition login-page">

<div id="app_login">
    <div class="wrapper">
        <div class="head">
            <div style="float:left;" class="logo">
                <img
                        src="{{ URL::asset(Wa::config('system.cms.logo', 'vendor/webarq/admin-lte/login/images/general/logo.png')) }}"
                        width="145" height="70" alt=""/>
            </div>
            <div style="float: right;">
                <img src="{{ URL::asset('vendor/webarq/admin-lte/login/images/general/logo-login.png') }}"
                     width="82" height="40" alt="" class="logo-login"/>
            </div>
        </div>

        @if (isset($messages))
            <div class="alert alert-danger">
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                @foreach ($messages as $groups)
                    <span>{{ current($groups) }}</span>
                @endforeach
            </div>
        @endif

        {!!
        Form::open([
                'url' => isset($url) ? $url : URL::panel('system/admins/auth/login'),
                'class' => 'login']) !!}
        <table border="0" cellpadding="0" class="login">
            <tr>
                <td colspan="2">
                    <div class="txt_input" id="email">
                        <input type="text" name="username" class="form-control" placeholder="Username">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="txt_input" id="password">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {{--<label for="checkbox" class="c_box"><input type="checkbox" name="checkbox" id="checkbox"/>
                        Remember Me</label>--}}
                </td>
            </tr>
            <tr>
                <td width="150px"><a href="{{ URL::panel('system/admins/auth/forgot-password') }}">Forgot Password?</a></td>
                <td class="right"><input type="submit" name="button" id="button" value=" " class="btn-login"/></td>
            </tr>
        </table>
        {!! Form::close() !!}
        <div class="footer">
            <img src="{{ URL::asset('vendor/webarq/admin-lte/login/images/icon/header-icon.png') }}"
                 width="13" height="13" alt=""/> WEBARQ CMS {{ config('webarq.projectInfo.version', '1.0.0') }}
        </div>
    </div>
    <div class="copyright">
        <p>Copyright (c) 2012</p>
        <img src="{{ URL::asset('vendor/webarq/admin-lte/login/images/general/logo-webarq.png') }}" width="55"
             height="23" alt=""/>
    </div>
</div>
</body>
</html>


