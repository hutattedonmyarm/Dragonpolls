<?php

require __DIR__ .'/vendor/autoload.php';

use APnutI\APnutI;
use Monolog\Logger;

session_start();
$config = include(__DIR__.'/config.php');
$api = new APnutI(
    $config['client_secret'],
    $config['client_id'],
    $config['permission_scopes'],
    $config['app_name'],
    $config['callback_url'],
    __DIR__.'/'.$config['log_file'],
    $config['log_level']
);

require_once __DIR__ . '/globals.php';
