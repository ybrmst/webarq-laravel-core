<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/18/2018
 * Time: 12:20 PM
 */ ?>
        <!DOCTYPE html>
<html>
<head><title>{{ $subject or '' }}</title>
<body style="color: #3E372E;">
<div style="width:300px;margin: 0 auto;text-align:left;color:#003d79;">
    <div style=" margin-left: 26px;">
        {!! $body or '' !!}
        <p style="font-size: 10px;">This email send automatically by system, and you do not need to
            reply.</p>
    </div>
</div>
</body>
</html>
