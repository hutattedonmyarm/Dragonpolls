<?php


require_once __DIR__ .'/bootstrap.php';

if (isset($_GET['code'])) {
  $success = $api->authenticate($_GET['code']);
  $from = empty($_GET['from']) ? null : $_GET['from'];
  $page = 'index.php';
  if (!empty($_GET['from'])) {
    $page = $_GET['from'];
  }
  if ($success) {
    redirect($page);
  } else {
    quit(get_page_header().'Echo error authenticating');
  }
} else {
    quit(get_page_header().'Echo error authenticating: '. $_GET['error_message']);
}
