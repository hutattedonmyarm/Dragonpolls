<?php


require_once __DIR__ .'/bootstrap.php';

if (isset($_GET['code'])) {
  $success = $api->authenticate($_GET['code']);
  if ($success) {
    redirect('index.php');
  } else {
    die('Echo error authenticating');
  }
} else {
    die('error authenticating: ' . $_GET['error_message']);
}
