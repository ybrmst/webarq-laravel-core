<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 2:27 PM
 */ ?>

<header class="main-header">
    <!-- Logo -->
    <a href="{{ URL::trans('/') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>W</b>A</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <img src="{{ URL::asset(Wa::config('system.site.logo')) }}" style="width:80px;"/>
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
    </nav>
</header>