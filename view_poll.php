<?php

require_once __DIR__ .'/bootstrap.php';

use APnutI\Exceptions\NotFoundException;
use APnutI\Exceptions\HttpPnutForbiddenException;
use APnutI\Exceptions\NotSupportedPollException;
use APnutI\Exceptions\NotAuthorizedException;
use APnutI\Exceptions\PollAccessRestrictedException;
use APnutI\Entities\Poll;
use APnutI\Entities\User;

try {
  echo get_page_header('Poll', true, ['poll']);
} catch (\Exception $e) {
  die('Something went wrong :( "' . $e->getMessage() . '"' . get_page_footer());
}

if (empty($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
  die('Invalid poll ID'.get_page_footer());
}
$poll_id = (int)$_GET['id'];
$poll = null;

try {
  $poll_token = array_key_exists('polltoken', $_GET) ? $_GET['polltoken'] : null;
  $poll = $api->getPoll($poll_id, $poll_token);
} catch (NotFoundException $nfe) {
  die('Poll not found'.get_page_footer());
} catch (NotSupportedPollException $nspe) {
  die('Sorry, this poll has a not yet supported type: ' . $nspe->getMessage() . get_page_footer());
} catch (PollAccessRestrictedException $are) {
  $message = array_key_exists('polltoken', $_GET)
    ? 'Sorry, your poll token is invalid! Please enter a valid token: '
    : ('Sorry, this poll is private! If you have found this poll on a post, '
       . 'please enter a link to the post, the post ID or the access token for the poll: ');
  die(
      $message
      . '<form><input type="text" name="polltoken">'
      . '<input type="hidden" name="id" value="'.$poll_id.'"><button type="submit">Access poll</button></form>'
      . get_page_footer()
  );
} catch (\Exception $e) {
  die('Something went wrong :( "'.$e->getMessage().'"' . get_page_footer());
}

$user_avatar_url = $poll->user->getAvatarUrl(50);
$user_avatar_url_srcset = get_source_set($poll->user, 50);

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
    <div class="success-banner"><span>✓</span> Your vote has been saved, thank you!</div>
  </div>
<?php }
if (array_key_exists('poll_created', $_GET) && $_GET['poll_created'] == 1) { ?>
  <div class="banner-wrapper">
    <div class="success-banner"><span>✓</span> Your poll and post have been created, thank you!</div>
  </div>
<?php } ?>
<div class="poll">
  <div class="header">
    <div class="user">
      <img src="<?= $user_avatar_url ?>" class="avatar" srcset="<?= $user_avatar_url_srcset ?>"/>
      <div class="usernames">
        <b><?= $user_name.'<br>' ?></b>
        <span class="username"><?= User::getProfileLinkForUsername($username) ?></span>
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
    $input_type = $poll->max_options === 1 ? 'radio' : 'checkbox';
    $input_name = $poll->max_options === 1 ? 'options' : 'options[]';
    foreach ($poll->options as $option) {
      $checked = $option->is_your_response ? 'checked' : '';
      $num_respondents_text = $option->respondents > 0 ? ' (' . $option->respondents . ')' : '';
      ?>
        <div class="option" style="grid-row: <?= $row ?>;">
          <input
            type="<?= $input_type ?>" <?= $checked.' '.$disabled ?>
            value="<?= $option->position ?>"
            name="<?= $input_name ?>"/>
          <span class="option-text"><?= $option->text . $num_respondents_text?></span>
        </div>
        <div class="option-responses" style="grid-row: <?= $row++ ?>;grid-column: 2;">
        <?php foreach ($option->respondent_ids as $res_id) {
          $u = $api->getUser($res_id, $user_args); ?>
          <a href="https://pnut.io/@<?= $u->username ?>">
            <img
              src="<?= $u->getAvatarUrl(20) ?>"
              srcset="<?= get_source_set($u, 20) ?>"
              class="avatar"
              title="@<?= $u->username ?>">
          </a>
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
<?= get_page_footer(); ?>
