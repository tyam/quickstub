<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
<head>
<meta charset="utf-8">
<title><?= eh($title) ?> | QUICKSTUB</title>
<link rel="stylesheet" href="/assets/semantic.css">
<link rel="stylesheet" href="/assets/site.css">
<script src="/assets/jquery-3.2.1.js"></script>
<script src="/assets/semantic.js"></script>
<script src="/assets/Sortable.js"></script>
<link href="https://fonts.googleapis.com/css?family=Teko:500" rel="stylesheet">
</head>
<body>
<div class="ui borderless text menu top-fit">
<span class="header item"><span class="logo">QUICKSTUB</span></span>
<span class="item"><h1 class="ui medium header"><?= eh($title) ?></h1></span>
<span class="item"><?php $renderer->yield('headerExtra') ?></span>
</div><!-- /.menu -->
<div class="ui grid bottom-fit">
<div class="nine wide column">
<div class="main">
<?php $renderer->content(); ?>
</div><!-- /.main -->
</div>
<div class="seven wide column">
<div class="side">
<div class="ui styled accordion" id="sideIn">
</div><!-- /.accordion -->
</div><!-- /.side -->
</div>
</div><!-- /.grid.container -->
<script>
function e(x) {
    return $('<span/>').text(x).html();
}
function showSession(req, res) {
    var rv = '';
    rv += '<div class="title">';
    rv += '<i class="dropdown icon"></i>' + e(req.method) + ' ' + e(req.target) + ' -> ' + res.status;
    rv += '</div>';
    rv += '<div class="content">';
    rv += '<div class="request">' + showRequest(req) + '</div>';
    rv += '<div class="ui divider"></div>';
    rv += '<div class="response">' + showResponse(res) + '</div>';
    rv += '</div>';
    return rv;
}
function showRequest(req) {
    var rv = e(req.method) + ' ' + e(req.target) + '<br>';
    for (let h of req.headers) {
        rv += e(h.name) + ': ' + e(h.value) + '<br>';
    }
    if (req.hasOwnProperty('body')) {
        rv += '<br>';
        rv += e(req.body).replace(/\r?\n/g, "<br>");
    }
    return rv;
}
function showResponse(res) {
    var rv = e(res.status) + '<br>';
    for (let h of res.headers) {
        rv += e(h.name) + ': ' + e(h.value) + '<br>';
    }
    if (res.hasOwnProperty('body')) {
        rv += '<br>';
        rv += e(res.body).replace(/\r?\n/g, "<br>");
    }
    return rv;
}
function fillSide(etag) {
    $.ajax(location.href+'/accesses', 
           {headeres: {}, 
            statusCode: {
                200: function (json) {
                    var content = '';
                    for (let sess of json) {
                        content += showSession(sess.request, sess.response);
                    }
                    $('#sideIn').html(content);
                }
            }});
    //setTimeout(function () {fillSide(etag)}, 10000);
}
$(document).ready(function () {
    $('.ui.checkbox').checkbox();
    $('.message').transition('fade in');
    $('.message .close').on('click', function() {
        $(this).closest('.message').transition('fade');
    });
    $('.accordion').accordion({
      selector: {
        trigger: '.title .icon'
      }, 
      exclusive: false});
    fillSide('0');
});
</script>
</body>
</html>