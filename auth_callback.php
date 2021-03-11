<?php

/*require __DIR__ .'/vendor/autoload.php';

use APnutI\APnutI;

session_start();
$api = new APnutI(
    'DKXEf-a9t6_1wWNnQK-bHijxLCIRXFBk',
    'mmZhbsH4KIoFkqnbIOVMktTSKypZ_QQk',
    'basic,polls,public_messages,stream,update_profile,write_post',
    'Playground',
    'http://localhost/auth_callback.php',
    './log.log'
);*/

require_once __DIR__ .'/bootstrap.php';

if (isset($_GET['code'])) {
  $success = $api->authenticate($_GET['code']);
  if ($success) {
    die('Welcome! <a href="index.php">Back to home</a>');
  } else {
    die('Echo error authenticating');
  }
} else {
    die('error authenticating: ' . $_GET['error_message']);
}
