<?php

require_once __DIR__ .'/bootstrap.php';

use APnutI\Exceptions\NotFoundException;
use APnutI\Exceptions\HttpPnutForbiddenException;
use APnutI\Exceptions\NotSupportedPollException;
use APnutI\Exceptions\NotAuthorizedException;
use APnutI\Exceptions\PollAccessRestrictedException;
use APnutI\Entities\Poll;

echo get_page_header('Poll', true, ['poll']);

if (empty($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
  die('Invalid poll ID');
}
$poll_id = (int)$_GET['id'];
$poll = null;

try {
  $poll_token = array_key_exists('polltoken', $_GET) ? $_GET['polltoken'] : null;
  $poll = $api->getPoll($poll_id, $poll_token);
} catch (NotFoundException $nfe) {
  die('Poll not found');
} catch (NotSupportedPollException $nspe) {
  die('Sorry, this poll has a not yet supported type: ' . $nspe->getMessage());
} catch (PollAccessRestrictedException $are) {
  $message = array_key_exists('polltoken', $_GET)
    ? 'Sorry, your poll token is invalid! Please enter a valid token: '
    : ('Sorry, this poll is private! If you have found this poll on a post, '
       . 'please enter a link to the post, the post ID or the access token for the poll: ');
  die(
      $message
      . '<form><input type="text" name="polltoken">'
      . '<input type="hidden" name="id" value="'.$poll_id.'"><button type="submit">Access poll</button></form>'
  );
}

$user_avatar_url = $poll->user->getAvatarUrl(50);
$username = '@' . $poll->user->username;
$disabled = $poll->canVote() ? '' : 'disabled';
$user_name = $poll->user->name ?? '';
$created_at = $poll->created_at;
$closed_at = $poll->closed_at;
jslog('teyt');
?>
<div class="poll">
  <div class="header">
    <div class="user">
      <img src="<?= $user_avatar_url ?>" class="avatar"/>
      <div class="usernames">
        <b><?= $user_name.'<br>' ?></b>
        <span class="username"><a href="http://pnut.io/<?= $username ?>"><?= $username ?></a></span>
      </div>
      <div class="spacer"></div>
      <div class="datewrapper">
        <span class="created_at">Created</span>
        <span class="closed_at"><?= $poll->isClosed() ? 'Closed' : 'Closing' ?></span>
        <time class="created_at" datetime="<?= $created_at->format(\DateTime::ISO8601) ?>">
          <?= $created_at->format('Y-m-d, H:i:s e') ?>
        </time>
        <time class="closed_at" datetime="<?= $closed_at->format(\DateTime::ISO8601) ?>">
          <?= $closed_at->format('Y-m-d, H:i:s e') ?>
        </time>
      </div>
    </div>
    <span class="prompt"><em><?= $poll->prompt ?></em></span>
  </div>
  <div class="options">
    <?php
    foreach ($poll->options as $option) {
      $checked = $option->is_your_response ? 'checked' : ''; ?>
      <div class="options">
      <input type="checkbox" <?= $checked.' '.$disabled ?>/>
      <span class="option-text"><?= $option->text ?></span>
      </div>
    <?php } ?>
  </div>
</div>

