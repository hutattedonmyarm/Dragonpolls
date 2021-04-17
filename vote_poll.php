<?php
require_once __DIR__ . '/bootstrap.php';

$voted_options = [];
if (is_array($_POST['options'])) {
  foreach ($_POST['options'] as $option) {
    $voted_options[] = (int)$option;
  }
} else {
  $voted_options[] = (int)$_POST['options'];
}
try {
  $res = $api->voteInPoll((int)$_POST['pollid'], $voted_options, $_POST['polltoken']);
} catch (\Exception $e) {
  $header = get_page_header('Voting error');
  $str = $header
    . 'Sorry, something went wrong while voting! "'
    . $e->getMessage()
    . '"<br>Please yell at <a href="https://pnut.io/@hutattedonmyarm">@hutattedonmyarm</a><br>'
    . '<a href="view_poll.php?id='.$_POST['pollid'].'"s>Go back to the poll</a>';
  quit($str);
}
redirect('view_poll.php?id='.$_POST['pollid'].'&success=1');
