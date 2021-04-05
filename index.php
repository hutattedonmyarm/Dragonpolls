<?php

require_once __DIR__ .'/bootstrap.php';

// Support the old Dragonpolls links
if (isset($_GET['poll'])) {
  redirect('view_poll.php?id='.$_GET['poll']);
}
echo get_page_header();
?>
<p>
  Welcome to the new Dragonpolls! In <a href="https://github.com/hutattedonmyarm/Dragonpolls/milestone/3">the future</a>
  you will find a stream of polls here, right now it's fairly empty!
  You can <a href="new_poll.php">create a new poll</a>, look at
  <a href="https://github.com/hutattedonmyarm/Dragonpolls/milestones?direction=asc&sort=title&state=open">
    my roadmap
  </a>,
  or <a href="https://github.com/hutattedonmyarm/Dragonpolls/issues/new/choose">make a feature request</a>.
</p>
<p>
  Once all planned features for version 1.0.0 are done, and the 0.9.0 bugs are squashed,
  I will replace the current version over at
  <a href="https://wedro.online/dragonpolls">https://wedro.online/dragonpolls</a>.<br>
  You may see that this uses a slightly different url format, polls are at <em>view_poll.php?id=XXX</em>
  instead of <em>index.php?poll=YYY</em>. The old links will continue to work of course, however I encourage everyone
  to share the new style once this is has replaced the previous incarnation.
</p>
<p>
  My current plan is to keep this domain up as a beta-channel, feel free to use it instead of
  <a href="https://wedro.online/dragonpolls">https://wedro.online/dragonpolls</a>,
  and update the productive installation with each milestone!
</p>

<?= get_page_footer() ?>
