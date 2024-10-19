<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 6/19/2017
 * Time: 2:23 PM
 */ ?>
        <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('meta-title', $metaTitle )</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Favicon -->
    <link rel="shortcut icon" type="images/x-icon" href="{{ Wa::config('system.favicon') }}"/>
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{URL::asset('vendor/webarq/admin-lte/bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->

    <!-- Enable/Disabled browser system cache -->
    @if (1 === (int) Wa::config('system.site.cache'))
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="expires" content="{{ date('D, d M Y H:i:s e') }}"/>
        <meta http-equiv="pragma" content="no-cache"/>
    @endif

    @if ('locale' === getenv('APP_ENV'))
        <link rel="stylesheet" href="{{URL::asset('vendor/webarq/admin-lte/dev-only/font-awesome.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('vendor/webarq/admin-lte/dev-only/ionicons.min.css')}}">
    @else
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        @endif
                <!-- Theme style -->
        <link rel="stylesheet" href="{{URL::asset('vendor/webarq/admin-lte/dist/css/AdminLTE.min.css')}}">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{URL::asset('vendor/webarq/admin-lte/dist/css/skins/_all-skins.min.css')}}">
        <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    {!! Wa::getThemesView($shareThemes, 'common.header') !!}

    {!! Wa::getThemesView($shareThemes, 'common.sidebar') !!}

    <div class="content-wrapper">
        <section class="content-header">
            &nbsp;
            <div class="breadcrumb">
                <span class="fa fa-folder-open"></span>
                {!! Wa::menu()->breadcrumb() !!}
            </div>
        </section>

        <section class="content">
            <div class="box box-default">
                <div class="box-body">{{ $message or '' }}</div>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
        </div>
        {!! Wa::config('system.site.copyright', '&copy; ' . date('Y') . ' WEBARQ. All Rights Reserved') !!}
    </footer>
    <div class="control-sidebar-bg"></div>

</div>
</body>
</html>
