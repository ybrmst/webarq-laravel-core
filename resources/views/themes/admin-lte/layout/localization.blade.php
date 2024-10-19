<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 3/23/2017
 * Time: 5:40 PM
 */ ?>

@extends('webarq::themes.admin-lte.layout.index')

@push('view-script')
<script type="text/javascript">
    $(function () {

        function localizationURL(){
            var panelURL = '{{ URL::panel('system/localization') }}';

            if ($('select#lang-selector').length) {
                panelURL += '/' + $('select#lang-selector').val();
            }
            if ($('select#file-selector').length) {
                panelURL += '/' + $('select#file-selector').val();
            }

            return panelURL;
        }

        var panelURL = '{{ URL::panel('system/localization') }}';

        if ($('select#lang-selector').length) {
            $('select#lang-selector, select#file-selector').change(function(){
                panelURL = localizationURL();

                window.location.href = panelURL;
            });
        }
    });
</script>
@endpush
