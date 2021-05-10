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
  echo get_page_header('New Poll', true, ['new_poll']);
} catch (\Exception $e) {
  quit('Something went wrong :( "'.$e->getMessage().'"');
}

if (!$api->isAuthenticated(false, true)) {
  quit('You need to be logged in to create a new poll!');
}

if (!empty($_POST['submit'])) {
  $prompt = $_POST['prompt'];
  $options = $_POST['option'];
  $is_anonymous = !empty($_POST['anonymous']);
  $is_public = !empty($_POST['public']);
  $max_options = (int)$_POST['max_options'];
  $duration_days = (int)$_POST['duration_days'];
  $duration_hours = (int)$_POST['duration_hours'];
  $duration_minutes = (int)$_POST['duration_minutes'];
  $duration_total_minutes = $duration_days*60*24 + $duration_hours * 60 + $duration_minutes;
  try {
    $poll = Poll::create($api, $prompt, $options, $max_options, $duration_total_minutes, $is_anonymous, $is_public);
    redirect('post_poll.php?poll_token='.$poll->token.'&id='.$poll->id);
  } catch (\Exception $e) {
    quit('Something went wrong creating the poll: "' . $e->getMessage() . '"');
  }
}
?>

<form method="POST" class="create-poll">
  <label for="prompt">Prompt</label>
  <input
    type="text"
    name="prompt"
    placeholder="What would you like to poll about?"
    id="prompt" required
    maxlength="<?= $api->getMaxPostLength() ?>"/>
  <label for="options">Options</label>
  <div id="options">
    <?php
    for ($i = 0; $i < 10; $i++) { ?>
      <input
        type="text"
        name="option[]"
        placeholder="This will be option #<?= $i+1 ?>" <?= $i < 2 ? 'required' : '' ?>
        maxlength="64"/>
    <?php } ?>
  </div>
  <label for="anonymous">Anonymous</label>
  <input type="checkbox" name="anonymous" id="anonymous" />
  <label for="public">Public</label>
  <input type="checkbox" name="public" id="public" />
  <label for="max_options">Max Options</label>
  <input type="number" name="max_options" id="max_options" min="1" max="10" value="1" inputmode="numeric" required/>
  <label for="duration">Duration</label>
  <div id="duration">
    <input type="number" name="duration_days" value="1" min="0" inputmode="numeric" required/><span>day(s)</span>
    <input type="number" name="duration_hours" value="0"  min="0" inputmode="numeric" required/><span>hour(s)</span>
    <input type="number" name="duration_minutes" value="0"  min="0" inputmode="numeric" required/><span>minute(s)</span>
    <br>
    <span id="openUntil"></span>
  </div>
  <span class="error"></span>
  <button type="submit" name="submit" value="submit">Create poll</button>
  </form>
</form>
<?= get_page_footer() ?>