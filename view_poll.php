<?php

require_once __DIR__ .'/bootstrap.php';

use APnutI\Exceptions\NotFoundException;
use APnutI\Exceptions\HttpPnutForbiddenException;
use APnutI\Exceptions\NotSupportedPollException;
use APnutI\Exceptions\NotAuthorizedException;
use APnutI\Exceptions\PollAccessRestrictedException;
use APnutI\Entities\Poll;

if (empty($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
  die('Invalid poll ID');
}
$poll_id = (int)$_GET['id'];
$poll = null;

if ($api->isAuthenticated()) {
  $user = $api->getAuthorizedUser();
  echo 'Welcome ' . ($user->name ?? $user->username) . '<br>';
}

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

#echo json_encode($poll);
#$user_avatar_url = $api->getAvatarUrl($poll->user->id, 50);
$user_avatar_url = $poll->user->getAvatarUrl(50);
#$prompt = '@' . $poll->user->username . ' asks: ' . $poll->prompt;
$username = '@' . $poll->user->username;
#echo '<img src="'.$user_avatar_url.'"/>';
#echo $prompt;
$disabled = $poll->canVote() ? '' : 'disabled';
?>
<div class="poll">
  <div class="header">
    <img src="<?= $user_avatar_url ?>" />
    <span class="username"><?= $username ?></span> asks:
    <span class="prompt"><?= $poll->prompt ?></span>
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
</div>
<?= json_encode($poll);?>
