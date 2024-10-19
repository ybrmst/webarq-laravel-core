<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/16/2017
 * Time: 6:46 PM
 */ ?>

<div class="box-header with-border">
    <h3 class="box-title">{{ $title }}</h3>
</div>

{!! Form::open($attributes) !!}
<div class="box-body">
    @if([] !== $alerts)
        <div class="alert alert-{{$alerts[1]}}">
            <h4><i class="icon fa fa-warning"></i> Alert!</h4>
            @foreach ($alerts[0] as $alert)
                {!! $alert !!} <br/>
            @endforeach
        </div>
    @endif

    {!! $html ?: '' !!}
</div>

<div class="box-footer">
    <button type="submit" class="btn btn-primary">Submit</button>
</div>
{!! Form::close() !!}