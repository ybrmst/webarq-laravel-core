<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/17/2017
 * Time: 2:09 PM
 */ ?>
@extends('webarq::themes.admin-lte.layout.index')

@push('view-script')
<script src="{{URL::asset('vendor/webarq/default/js/form.js')}}"></script>

@if ('local' !== env('CK_EDITOR_SOURCE', 'local'))
    <script src="https://cdn.ckeditor.com/4.6.2/full/ckeditor.js?v={{ time() }}"></script>
    <script type="text/javascript">
        $('.ckeditor').each(function () {
            CKEDITOR.replace($(this).attr('name'), {
                customConfig: '{{ URL::asset('vendor/webarq/admin-lte/plugins/ckeditor/config.js') }}'
            });
        });
        /**
         * Force the ckeditor to reload it's asset files
         */
        CKEDITOR.timestamp = '{{ env('CK_EDITOR_TIMESTAMP', 'binduraja') }}';
    </script>
@else
    <script src="{{ URL::asset('vendor/webarq/admin-lte/plugins/ckeditor/ckeditor.js') . '?v=' . time() }}"></script>
    <script type="text/javascript">
        /**
         * Force the ckeditor to reload it's asset files
         */
        CKEDITOR.timestamp = '{{ env('CK_EDITOR_TIMESTAMP', 'binduraja') }}';
    </script>
    <script src="{{ URL::asset('vendor/webarq/admin-lte/plugins/ckeditor/styles.js') . '?v=' . time() }}"></script>
    <script src="{{ URL::asset('vendor/webarq/admin-lte/plugins/ckeditor/samples/js/sample.js') . '?v=' . time() }}"></script>
@endif

@if ('create' === $strAction)
    <script>
        $(function () {
            if ($('form input[type="text"].sequence').length) {
                var form = $('form input[type="text"].sequence').closest('form');
                var group = $('form input[type="text"].sequence').attr('grouping-column');

                if ('string' === typeof group) {
                    $.each($.parseJSON(group), function (e, c) {
                        if ('select' === $('[name="' + e + '"]').prop('tagName').toLowerCase()) {
                            $('[name="' + e + '"]').change(sequenceAjax);
                        }
                    });
                }

                sequenceAjax();

                function sequenceAjax() {
                    var data = form.serializeArray();
                    data.push({
                        name: 'varAction',
                        value: 'create'
                    });
                    data.push({
                        name: 'varSecret',
                        value: $('form input[type="text"].sequence').attr('data-table')
                    });


                    $.ajax({
                        type: "GET",
                        url: baseUrl + '/helper/form/sequence/{{ $strModule }}/{{ $strPanel }}',
                        data: data,
                        success: function (response) {
                            if ('false' !== response) {
                                $('form input[type="text"].sequence').val(response);
                            }
                        }
                    });
                }
            }
        });
    </script>
@endif
@endpush

