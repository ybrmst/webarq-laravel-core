<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 6/13/2017
 * Time: 4:25 PM
 */ ?>
<html>
<head>
    <title>Reset your {{ $title }} password!</title>
</head>

<body>

<table style="margin:0 auto;padding:0;max-width:612px" cellspacing="0" cellpadding="0" border="0" align="center">
    <tbody>
    <tr>
        <td valign="top" align="center">
            <table style="margin:0;padding:0;width:100%;border-bottom:1px solid #eeeeee" cellspacing="0" cellpadding="0"
                   border="0">
                <tbody>
                <tr>
                    <td style="padding:0 20px">
                        <img
                                src="{{ URL::asset(Wa::config('system.cms.logo', 'vendor/webarq/admin-lte/login/images/general/logo.png')) }}"
                                width="145" height="70" alt=""/>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:30px 0 20px 20px">
            <p>Dear Sir/Madam</p>

            <p>
                Did you forget your password?
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding:0">
            <table style="margin:0;padding:0;max-width:612px;border:1px solid #eeeeee;color:#5f5f5f" width="100%"
                   cellspacing="0" cellpadding="0" border="0">
                <tbody>
                <tr style="background:#eeeeee">
                    <td style="text-align:center;vertical-align:top;font-size:0;padding:15px 0">
                        <div style="width:300px;display:inline-block;vertical-align:middle">
                            <table width="80%" align="center">
                                <tbody>
                                <tr>
                                    <td style="background:#00548e;padding:13px 0" align="center">
                                        <a href="{{ $url }}"
                                           style="text-decoration:none;color:#fff;font-size:16px;font-weight:bold;display:block;text-align:center"
                                           target="_blank">Reset Password</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div style="width:300px;display:inline-block;vertical-align:middle"
                             class="m_5596487074500273192gray-small-text">
                            <table width="100%">
                                <tbody>
                                <tr>
                                    <td style="font-size:14px;vertical-align:middle;text-align:left"
                                        class="m_5596487074500273192gray-text-wrap">The button will expire in
                                        {{ $expiration }} and can be used only once.
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </td>
                </tr>

                </tbody>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:20px 20px 0">If you don't want to change your <span class="il">password</span> or didn't
            request this, please ignore and delete this message.
        </td>
    </tr>

    <tr>
        <td style="padding:16px 20px 25px">Thank you, <br>
            {!! $title !!}
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>
