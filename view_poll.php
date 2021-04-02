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
jslog($poll);
$user_avatar_url = $poll->user->getAvatarUrl(50);
$username = '@' . $poll->user->username;
$disabled = $poll->canVote() ? '' : 'disabled';
$user_name = $poll->user->name ?? '';
$created_at = $poll->created_at;
$closed_at = $poll->closed_at;
$user_votes = $poll->getMyVotes();

$votes_remaining = $poll->max_options - count($user_votes);
$votes_remaining_plural = $votes_remaining === 1 ? '' : 's';
$votes_remaining_text = "$votes_remaining Vote$votes_remaining_plural remaining";
$votes_remaining_hidden = $poll->canVote() ? '' : ' hidden';
$data_can_vote = $poll->canVote() ? 'true' : 'false';
$disabled_button = ($poll->canVote() && count($user_votes) > 0) ? '' : 'disabled';

if (array_key_exists('success', $_GET) && $_GET['success'] == 1) { ?>
  <div class="banner-wrapper">
    <div class="success-banner"><span>✔</span> Your vote has been saved, thank you!</div>
  </div>
<?php } ?>
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
    <span class="prompt">
      <em><?= $poll->prompt ?></em><br>
      <span
        class="votes-remaining <?= $votes_remaining_hidden ?>"
        data-max-votes="<?= $poll->max_options ?>"><?= $votes_remaining_text ?>
      </span>
    </span>
  </div>
  <div class="options">
    <form method="POST" action="vote_poll.php">
      <input type="hidden" name="pollid" value="<?= $poll->id ?>"/>
      <input type="hidden" name="polltoken" value="<?= $poll->token ?>"/>
    <?php
    $row = 1;
    $user_args = [
      'include_html' => false,
      'include_counts' => false,
    ];
    foreach ($poll->options as $option) {
      $checked = $option->is_your_response ? 'checked' : ''; ?>
        <div class="option" style="grid-row: <?= $row ?>;">
          <input type="checkbox" <?= $checked.' '.$disabled ?> value="<?= $option->position ?>" name="options[]"/>
          <span class="option-text"><?= $option->text . ' (' . $option->respondents . ')'?></span>
        </div>
        <div class="option-responses" style="grid-row: <?= $row++ ?>;grid-column: 2;">
        <?php foreach ($option->respondent_ids as $res_id) {
          $u = $api->getUser($res_id, $user_args); ?>
          <img src="<?= $u->getAvatarUrl(20) ?>" class="avatar" title="@<?= $u->username ?>">
        <?php } ?>
        </div>
    <?php } ?>
    <button
      type="submit"
      name="submit_vote"
      value="submit" <?= $disabled_button?>
      data-can-vote="<?= $data_can_vote ?>">
      Vote
    </button>
    </form>
  </div>
</div>

