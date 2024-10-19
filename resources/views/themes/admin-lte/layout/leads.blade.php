<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 2:09 PM
 */ ?>
@extends('webarq::themes.admin-lte.layout.index')

@section('content')
    <div class="row">
        <div class="col-md-4">
            {!! Form::open(['url' => URL::panel('system/leads'), 'method' => 'GET']) !!}
            <div class="box-body">
                @if (1 === count($leadGroups))
                    <h3>{{ $leadActive }}</h3>
                @else
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Type</label>

                        <div class="col-sm-8">
                            <select class="form-control" name="type">
                                @foreach ($leadGroups as $group)
                                    <option
                                            {{ $leadActive === $group ? ' selected="selected"' : '' }}
                                            value="{{ $group }}">{{ title_case($group) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label style="text-align: left;" class="col-sm-2 control-label">Perpage</label>

                    <div class="col-sm-8">
                        <input class="form-control" min="1" type="number" name="perpage"
                               value="{{ request()->input('perpage', 20) }}"/>
                        @if (request()->query('page') > 1)
                            <input type="hidden" name="page" value="{{ request()->query('page') }}"/>
                        @endif
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button name="button" value="filter" type="submit" class="btn btn-default">Filter</button>
                @if(Wa::panel()->isAccessible(Wa::module('system'), Wa::module('system')->getPanel('leads'), 'export'))
                    <button name="button" value="export" type="submit" class="btn btn-primary">Advanced Export</button>
                @endif
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    {!! $leadHtml !!}
    {!! $paginate !!}
@endsection