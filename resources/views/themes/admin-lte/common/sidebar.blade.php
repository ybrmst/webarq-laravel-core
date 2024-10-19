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
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{URL::asset('vendor/webarq/admin-lte/dist/img/avatar.png')}}" class="img-circle"
                     alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{$admin->username}}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        @if (!isset($shareSearchBox) || false !== $shareSearchBox)
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input
                            type="text" name="q" class="form-control" placeholder="Search..."
                            value="{{Request::input('q')}}" {{!Request::input('q') ?: ' autofocus'}}>
                  <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i
                                class="fa fa-search"></i>
                    </button>
                  </span>
                </div>
            </form>
            @endif
                    <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header">MAIN NAVIGATION</li>
                @if ([] !== Wa::panel()->getMenus())
                    @foreach ( Wa::panel()->getMenus() as $module => $panels)
                        <li class="treeview {{ $module === $shareModule->getTitle() ? ' active' : '' }}">
                            <a>
                                <i class="fa {{ Wa::panel()->getIcon('module', $module, 'fa-dashboard') }} {{$module}}">
                                </i>
                                <span>{{ $module }}</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                            </a>
                            <ul class="treeview-menu">
                                @foreach($panels as $panel => $options)
                                    <li>
                                        <a href="{{$options[1]}}"
                                                {!!
                                                    $sharePanel->getTitle() == $options[0]
                                                            ? ' class="active" ' : ''
                                                !!}
                                                >
                                            <i class="fa {{ Wa::panel()->getIcon('panel', $panel, 'fa-circle-o') }}">
                                            </i> {{ $options[0] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{URL::site('')}}" target="_blank">
                            <i class="fa fa-tv"></i> <span>Go to web</span>
                        </a>
                    </li>
                @endif
            </ul>
    </section>
    <!-- /.sidebar -->
</aside>
