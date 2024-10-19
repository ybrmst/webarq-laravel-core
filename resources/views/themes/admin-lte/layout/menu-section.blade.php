<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/29/2017
 * Time: 6:53 PM
 */ ?>
@extends('webarq::themes.admin-lte.layout.index')

@section('content')
    @if ([] !== $sections)
        @foreach ($sections as $section)
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h4 class="box-title">
                            <a href="{{ array_get($section, 'link') }}?q={{ array_get($section, 'option.id') }}"
                               target="_blank">
                                {{ array_get($section, 'title') }}
                            </a>
                            <small> (Section ID : {{ array_get($section, 'option.id') }})</small>
                        </h4>
                    </div>

                    <div class="box-body section" data-url="{{ array_get($section, 'link') }}?q={{ array_get($section, 'option.id') }}">

                    </div>
                    <div class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection

@push('view-script')
<script type="text/javascript">
    $('.box-body.section').each(function(){
        $(this).load($(this).data('url'), function() {
            $(this).next('.overlay').remove();
        });
    })
</script>
@endpush