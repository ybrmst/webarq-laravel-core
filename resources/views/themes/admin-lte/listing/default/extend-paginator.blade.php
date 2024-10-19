<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/9/2018
 * Time: 6:22 PM
 */
$queries = request()->query();
array_forget($queries, ['page', 'perpage']); ?>

<div class="top" style="/*border-bottom: 1px solid #d2d6de;*/">
    <div class="row">
        <div class="col-md-12">
            {!! $action or '' !!}
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            {!! Form::open(['url' => Request::url(), 'class' => 'form-horizontal', 'method' => 'GET']) !!}
            <div class="box-body">
                <div class="form-group">
                    <label style="text-align: left;" class="col-sm-2 control-label">Perpage</label>

                    <div class="col-sm-2">
                        <input class="form-control" min="20" type="number" name="perpage"
                               value="{{ request()->input('perpage', 20) }}"/>
                        @if (request()->query('page') > 1)
                            <input type="hidden" name="page" value="{{ request()->query('page') }}"/>
                        @endif
                    </div>

                    <div class="col-sm-2">
                        @foreach ($queries as $k => $v)
                            @if (is_array($v))
                                @foreach ($v as $k1 => $v1)
                                    <input type="hidden" name="{{ $k1 }}" value="{{ $v1 }}"/>
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}"/>
                            @endif
                        @endforeach
                        <button type="submit" class="btn btn-default">Filter</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>