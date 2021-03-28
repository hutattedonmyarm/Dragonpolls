<?php
require_once __DIR__ . '/bootstrap.php';

$voted_options = [];
foreach ($_POST['options'] as $option) {
  $voted_options[] = (int)$option;
}
try {
  $res = $api->voteInPoll((int)$_POST['pollid'], $voted_options, $_POST['polltoken']);
} catch (\Exception $e) {
  get_page_header('Voting error');
  $str = 'Sorry, something went wrong while voting!'
    . 'Please yell at <a href="https://pnut.io/@hutattedonmyarm">@hutattedonmyarm></a><br>'
    . '<a href="view_poll.php?id="'.$_POST['pollid'].'>Go back to the poll</a>';
  die($str);
}
redirect('view_poll.php?id='.$_POST['pollid'].'&success=1');
