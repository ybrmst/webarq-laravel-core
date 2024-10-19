<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/24/2017
 * Time: 6:09 PM
 */ ?>

@foreach ($shareData as $data)
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">{{ $data->title }}</h3>
        </div>
        <div class="box-body">
            <p>{{ $data->intro }}</p>
            {!! $data->description !!}
        </div>
    </div>
    @if ($shareData instanceof \Illuminate\Pagination\LengthAwarePaginator)
        {!! $shareData->render() !!}
    @endif
@endforeach
