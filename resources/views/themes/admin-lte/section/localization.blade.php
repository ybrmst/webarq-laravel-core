<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/23/2017
 * Time: 1:23 PM
 */ ?>

@if (class_exists('Wl'))
    <div class="col-md-6" style="padding-left: 0; padding-bottom: 10px;">
        <h3 class="box-titlte">Select your locale :</h3>
        <select id="lang-selector" class="form-control col-md-6">
            @foreach (Wl::getCodes() as $code)
                <option value="{{ $code }}"
                        @if ($locale === $code) selected="selected" @endif> {{ strtoupper($code) }}</option>
            @endforeach
        </select>
    </div>
@endif

@if ([] !== $files && count($files) > 1)
    <div class="col-md-6" style="">
        <h3 class="box-titlte">Select your file :</h3>
        <select id="file-selector" class="form-control col-md-6">
            @foreach ($files as $code)
                <option value="{{ $code }}"
                        @if ($locale === $code) selected="selected" @endif> {{ strtoupper($code) }}</option>
            @endforeach
        </select>
    </div>
@endif