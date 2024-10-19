<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/24/2017
 * Time: 5:40 PM
 */ ?>

@extends('webarq::themes.front-end.layout.index')

@section('content')
    @if ([] !== $shareSections)
        {!! implode('', $shareSections) !!}
    @else
        <div class="callout callout-info">
            <h4>Welcome!</h4>

            <p>
                Welcome. If this is appear on your screen, it does mean the system could not find proper
                content due to missing active menu.
            </p>

            <p>
                Contact your administrator if you think this is an error
            </p>
        </div>
    @endif
@endsection
