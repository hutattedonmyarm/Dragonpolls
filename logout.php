<?php

require_once __DIR__ .'/bootstrap.php';

$api->logout();
$url = empty($_SERVER['HTTP_REFERER']) ? 'index.php' : $_SERVER['HTTP_REFERER'];
redirect($url);
