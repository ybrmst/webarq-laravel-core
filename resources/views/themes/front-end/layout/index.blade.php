<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 2:08 PM
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
    <link rel="shortcut icon" type="images/x-icon" href="{{ URL::asset(Wa::config('system.favicon')) }}"/>
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
        <!-- iCheck -->
        <link rel="stylesheet" href="{{URL::asset('vendor/webarq/admin-lte/plugins/iCheck/flat/blue.css')}}">
        <!-- Morris chart -->
        <link rel="stylesheet" href="{{URL::asset('vendor/webarq/admin-lte/plugins/morris/morris.css')}}">
        <!-- jvectormap -->
        <link rel="stylesheet"
              href="{{URL::asset('vendor/webarq/admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}">
        <!-- Date Picker -->
        <link rel="stylesheet" href="{{URL::asset('vendor/webarq/admin-lte/plugins/datepicker/datepicker3.css')}}">
        <!-- Daterange picker -->
        <link rel="stylesheet"
              href="{{URL::asset('vendor/webarq/admin-lte/plugins/daterangepicker/daterangepicker.css')}}">
        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet"
              href="{{URL::asset('vendor/webarq/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">

        @stack('view-style')

                <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    {!! Wa::getThemesView($shareThemes, 'common.header') !!}

    {!! Wa::getThemesView($shareThemes, 'common.sidebar') !!}

    <div class="content-wrapper">
        <section class="content-header">
            @if (class_exists('Wl') && !empty(Wa::menu()->getActive()))
                @foreach (Wl::getCodes() as $code)
                    {{--@if ($code !== app()->getLocale())--}}
                    @set(activeURL, Wa::menu()->getActive()->eloquent()->trans('permalink', $code))
                    @if (isset($shareDetail) && $shareDetail instanceof \Illuminate\Database\Eloquent\Model && $shareDetail->count())
                        @if (null !== Wa::menu()->getSegment('markup.key'))
                            @set(activeURL, $activeURL . '/' . Wa::menu()->getSegment('markup.key'))
                        @endif
                        @set(activeURL, $activeURL . '/' . $shareDetail->trans('permalink', $code))
                    @else
                    @endif
                    <a href="{{ URL::trans($activeURL, [], null, $code) }}"> {{ strtoupper($code) }}</a>
                    {{--@endif--}}
                @endforeach
            @endif

            <div class="breadcrumb">
                <span class="fa fa-folder-open"></span>
                {!! Wa::menu()->breadcrumb() !!}
            </div>
        </section>

        <section class="content">
            @yield('content')
        </section>
    </div>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
        </div>
        {!! Wa::config('system.site.copyright', '&copy; ' . date('Y') . ' WEBARQ. All Rights Reserved') !!}
    </footer>

    <!-- Control Sidebar -->
    {{--{!! Wa::getThemesView('admin-lte', 'common.control-sidebar') !!}--}}
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{URL::asset('vendor/webarq/admin-lte/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/morris/morris.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<!-- jvectormap -->
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/knob/jquery.knob.js')}}"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- datepicker -->
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>
<!-- Slimscroll -->
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{URL::asset('vendor/webarq/admin-lte/plugins/fastclick/fastclick.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{URL::asset('vendor/webarq/admin-lte/dist/js/app.min.js')}}"></script>
{{-- AdminLTE dashboard demo (This is only for demo purposes) --}}
{{--<script src="{{URL::asset('vendor/webarq/admin-lte/dist/js/pages/dashboard.js')}}"></script>--}}
{{-- AdminLTE for demo purposes --}}
{{--<script src="{{URL::asset('vendor/webarq/admin-lte/dist/js/demo.js')}}"></script>--}}
@stack('view-script')
</body>
</html>

