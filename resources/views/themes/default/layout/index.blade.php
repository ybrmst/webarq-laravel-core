<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/30/2017
 * Time: 3:20 PM
 */ ?>
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ Wa::config('system.site.meta.title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ Wa::config('system.site.meta.description') }}">
    <meta name="author" content="{{ Wa::config('system.site.meta.author', 'Webarq') }}">

</head>
<body>
{!! Wa::getThemesView('front-end', 'common.header') !!}
{!! Wa::getThemesView('front-end', 'common.footer') !!}
</body>
</html>
