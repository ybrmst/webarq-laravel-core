<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 5:46 PM
 */?>
    <!DOCTYPE html>
<html>
<head>
  <title>Forbidden.</title>

  <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

  <style>
    html, body {
      height: 100%;
    }

    body {
      margin: 0;
      padding: 0;
      width: 100%;
      color: #333;
      display: table;
      font-weight: 100;
      font-family: 'Lato', sans-serif;
    }

    .container {
      text-align: center;
      display: table-cell;
      vertical-align: middle;
    }

    .content {
      text-align: center;
      display: inline-block;
    }

    .title {
      font-size: 72px;
      margin-bottom: 40px;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="content">
    <div class="title">Forbidden.</div>
    <p  style="font-family: 'Microsoft Sans Serif' , Arial, Helvetica, Verdana;">
      Please do not worry, you can head back to <a href="{{URL::site()}}">Home</a> page
    </p>
  </div>
</div>
</body>
</html>