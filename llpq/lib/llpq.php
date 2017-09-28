<?php
require(__DIR__.'/web/url.php');
$oburl=new url($_SERVER['REQUEST_URI'],$_CONFIG);
$oburl->url_run();
