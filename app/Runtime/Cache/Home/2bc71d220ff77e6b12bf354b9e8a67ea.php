<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ThinkPHP Study</title>
    <link href="/lib/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="/lib/dotku/dist/css/general-cn.css" rel="stylesheet"/>
    <script src="/lib/angular/angular.js"></script>
    <script src="/lib/jquery/dist/jquery.min.js"></script>
    <script src="/lib/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.rawgit.com/showdownjs/showdown/1.3.0/dist/showdown.min.js"></script>
    
  </head>
  <body>
    <div class="container">
      <h1>TITLE</h1>
      <p>content</p>
    </div>
    <script>
      
      var converter = new showdown.Converter();
      $.ajax({
        url: './readme.md',
      }).done(function(data){
        //sText      = '#hello, markdown!',
        sHtml      = converter.makeHtml(data);
        //console.log(data);
        $(".container").html(sHtml);
      });
      
    </script>
  </body>
</html>