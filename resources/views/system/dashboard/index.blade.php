<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/17/2017
 * Time: 5:49 PM
 */ ?>
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-xs-12" style="padding-bottom: 10px;">
            <a href="{{ URL::panel('system/dashboard/detail') }}">All Activities</a>
        </div>
    </div>

    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-first">
                <div class="inner">
                    <h3>{{ $dailyActivity }}</h3>

                    <p>Daily activity</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ URL::panel('system/dashboard/detail/daily') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-second">
                <div class="inner">
                    <h3>{{ $weeklyActivity }}</h3>

                    <p>Weekly activity</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ URL::panel('system/dashboard/detail/weekly') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-third">
                <div class="inner">
                    <h3>{{ $monthlyActivity }}</h3>

                    <p>Monthly activity</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ URL::panel('system/dashboard/detail/monthly') }}" class="small-box-footer">
                    More info <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <!-- ./col -->
    </div>
</section>
