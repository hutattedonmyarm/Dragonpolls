<?php

require_once __DIR__ .'/bootstrap.php';

use APnutI\Entities\Poll;

try {
  echo get_page_header('Post Poll', true, []);
} catch (\Exception $e) {
  quit('Something went wrong :( "'.$e->getMessage().'"');
}

if (!$api->isAuthenticated(false, true)) {
  quit('You need to be logged in to create a new post!');
}

if (!empty($_POST['submit'])) {
  if (empty($_POST['poll_id']) || !is_numeric($_POST['poll_id']) || $_POST['poll_id'] <= 0) {
    quit('Invalid poll ID');
  }
  if (empty($_POST['poll_token'])) {
    quit('Invalid poll token');
  }
  if (empty($_POST['post_text'])) {
    quit('Invalid text');
  }
  try {
    $params = [
      'raw' => Poll::makePollNoticeRaw($_POST['poll_id'], $_POST['poll_token'])
    ];
    $api->createPostWithParameters($_POST['post_text'], $params);
    redirect('view_poll.php?poll_created=1&id=' . $_POST['poll_id']);
  } catch (\Exception $e) {
    quit('Something went wrong creating your post: "' . $e->getMessage() . '"');
  }
}

if (empty($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
  quit('Invalid poll ID');
}

if (empty($_GET['poll_token'])) {
  quit('Invalid poll token');
}
if (empty($_GET['prompt'])) {
  quit('Invalid prompt');
}

$poll_id = (int)$_GET['id'];
$poll_token = $_GET['poll_token'];
$prompt = $_GET['prompt'];
$dir_name = dirname($_SERVER['SCRIPT_NAME']);
if ($dir_name === '.' || $dir_name === '/') {
  $dir_name = '';
}


$scheme = empty($_SERVER['REQUEST_SCHEME']) ? 'http' : $_SERVER['REQUEST_SCHEME'];
$url = $scheme
  . '://'
  . $_SERVER['HTTP_HOST']
  . $dir_name
  . '/view_poll.php?id='
  . $poll_id;
?>
Do you want to post about your poll?
<form method="POST" class="post-poll">
  <textarea rows="4" cols="50" name="post_text" maxlength="256">I created a new poll:
<?= $prompt ?>

<?= $url ?>
  </textarea><br>
  <input type="hidden" name="poll_id" value="<?= $poll_id ?>">
  <input type="hidden" name="poll_token" value="<?= $poll_token ?>">
  <button type="submit" name="submit" value="submit">Post to pnut</button>
</form>
<a href="/view_poll.php?id=<?= $poll_id ?>">Take me straight to the poll</a>
<p>
Note, that if your poll is set to private, you will either need to share your poll with a post,
or give the poll's access token to everyone who should be able to vote in your poll. Your access token is:
<pre><?= $poll_token ?></pre>
</p>
<?= get_page_footer() ?>