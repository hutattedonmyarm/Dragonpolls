<?php

require_once __DIR__ .'/bootstrap.php';

use APnutI\Exceptions\NotFoundException;
use APnutI\Exceptions\HttpPnutForbiddenException;
use APnutI\Exceptions\NotSupportedPollException;
use APnutI\Entities\Poll;

if (empty($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
  die('Invalid poll ID');
}
$poll_id = (int)$_GET['id'];
$poll = null;
try {
  $poll = $api->getPoll($poll_id);
} catch (NotFoundException $nfe) {
  die('Poll not found');
} catch (HttpPnutForbiddenException $fe) {
  die('Poll token required!');
} catch (NotSupportedPollException $nspe) {
  die('Sorry, this poll has a not yet supported type: ' . $nspe->getMessage());
}

#echo json_encode($poll);
#$user_avatar_url = $api->getAvatarUrl($poll->user->id, 50);
$user_avatar_url = $poll->user->getAvatarUrl(50);
$prompt = '@' . $poll->user->username . ' asks: ' . $poll->prompt;
echo '<img src="'.$user_avatar_url.'"/>';
echo $prompt;
