<?php


require_once __DIR__ .'/bootstrap.php';

if (isset($_GET['code'])) {
  $success = $api->authenticate($_GET['code']);
  if ($success) {
    redirect('index.php');
  } else {
    quit(get_page_header().'Echo error authenticating');
  }
} else {
    quit(get_page_header().'Echo error authenticating: '. $_GET['error_message']);
}
