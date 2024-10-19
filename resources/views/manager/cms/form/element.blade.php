<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 10:57 AM
 */ ?>
<div{!! Html::attributes($attribute) !!}>
    @if (isset($group))
        <h3 class="box-title" style="    border-radius: 3px;border-top: 3px solid #3c8dbc;padding-top: 10px;">
            {{ $group }}
        </h3>
    @endif

    <label for="{{ $title or '' }}">{{ $title or '' }}</label>
    {!! $input or '...' !!}
    @if (!empty($info))
        <span class="help-block">{{ $info }}</span>
    @endif
</div>