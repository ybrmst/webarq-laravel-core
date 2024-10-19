<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 2:46 PM
 */ ?>
<section class="content-header">
    <h1>
        {{ title_case($shareModule->getTitle()) }}
        <small>{{ title_case($sharePanel->getTitle()) }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">{{ title_case($shareModule->getTitle()) }}</a></li>
        @if (!is_null($shareBreadCrumbAction))
            <li>{{ title_case($sharePanel->getTitle()) }}</li>
            <li class="active">{{ title_case($shareBreadCrumbAction) }}</li>
        @else
            <li class="active">{{ title_case($sharePanel->getTitle()) }}</li>
        @endif
    </ol>
</section>
