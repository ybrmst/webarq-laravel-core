<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 2:28 PM
 */ ?>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- search form -->
        @if (!isset($shareSearchBox) || false !== $shareSearchBox)
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input
                            type="text" name="q" class="form-control" placeholder="Search..."
                            value="{{Request::input('q')}}" {{!Request::input('q') ?: ' autofocus'}}>
                  <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                    </button>
                  </span>
                </div>
            </form>
        @endif
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        {!! Wa::menu()->generate(Wa::menu()->main()) !!}
    </section>
    <!-- /.sidebar -->
</aside>
