<?php

require_once __DIR__ .'/bootstrap.php';

use APnutI\Exceptions\NotFoundException;
use APnutI\Exceptions\HttpPnutForbiddenException;
use APnutI\Exceptions\NotSupportedPollException;
use APnutI\Exceptions\NotAuthorizedException;
use APnutI\Exceptions\PollAccessRestrictedException;
use APnutI\Entities\Poll;
use APnutI\Entities\User;

// Support the old Dragonpolls links
if (isset($_GET['poll'])) {
  redirect('view_poll.php?id='.$_GET['poll']);
}
echo get_page_header();
?>
<p>
  Welcome to the new Dragonpolls!<br>
  You can <a href="new_poll.php">create a new poll</a>, look at
  <a href="https://github.com/hutattedonmyarm/Dragonpolls/milestones?direction=asc&sort=title&state=open">
    my roadmap
  </a>,
  or <a href="https://github.com/hutattedonmyarm/Dragonpolls/issues/new/choose">make a feature request</a>.
</p>
<p>
  <strike>Once all planned features for version 1.0.0 are done, and the 0.9.0 bugs are squashed,
  I will replace the current version over at
  <a href="https://wedro.online/dragonpolls">https://wedro.online/dragonpolls</a></strike>.<br>
  Version 1.0.0 is done, and if the next Thememonday goes well,
  it will be installed over at <a href="https://wedro.online/dragonpolls">https://wedro.online/dragonpolls</a>.
  Considering, that 1.1.0 is done as well. I will just go ahead and release 1.1.0!
  <br/>
  You may see that this uses a slightly different url format, polls are at <em>view_poll.php?id=XXX</em>
  instead of <em>index.php?poll=YYY</em>. The old links will continue to work of course, however I encourage everyone
  to share the new style once this is has replaced the previous incarnation.
</p>
<p>
  My current plan is to keep this domain up as a beta-channel, feel free to use it instead of
  <a href="https://wedro.online/dragonpolls">https://wedro.online/dragonpolls</a>,
  and update the productive installation with each milestone!
</p>
<div class="poll-grid">
<?php
$polls = $api->getPolls();

foreach ($polls as $poll) {
  $user_avatar_url = $poll->user->getAvatarUrl(50);
  $user_avatar_url_srcset = get_source_set($poll->user, 50);

  $username = '@' . $poll->user->username;
  $disabled = $poll->canVote() ? '' : 'disabled';
  $user_name = $poll->user->name ?? '';
  $created_at = $poll->created_at;
  $closed_at = $poll->closed_at;
  ?>
  <div class="poll" onclick="location.href='view_poll.php?id=<?= $poll->id ?>'">
    <div class="header">
      <div class="user">
        <div class="usernamewrapper">
          <img src="<?= $user_avatar_url ?>" class="avatar" srcset="<?= $user_avatar_url_srcset ?>"/>
          <div class="usernames">
            <b><?= $user_name.'<br>' ?></b>
            <span class="username"><?= $username ?></span>
          </div>
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
      </span>
    </div>
    <a href="view_poll.php?id=<?= $poll->id ?>">View poll</a>
  </div>
<?php } ?>
</div>
<?= get_page_footer() ?>
