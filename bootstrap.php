<?php

require __DIR__ .'/vendor/autoload.php';

use APnutI\APnutI;

session_start();
$config = include(__DIR__.'/config.php');
$api = new APnutI(
    $config['client_id'],
    $config['client_secret'],
    $config['permission_scopes'],
    $config['app_name'],
    $config['callback_url'],
    __DIR__.'/'.$config['log_file']
);
