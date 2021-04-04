<?php

function jslog($obj)
{
  global $config;
  if (!($config['log_level'] == 'DEBUG' || $config['log_level'] === Logger::DEBUG)) {
    return;
  }
  echo '<script>console.log('.json_encode($obj).');</script>';
}

function get_page_header(
    ?string $page_title = null,
    bool $include_app_name = true,
    array $scripts = []
): string {
  global $api;
  $greeting = '';
  $logout_link = '';
  $newpoll_class = '';
  if ($api->isAuthenticated(false, true)) {
    $u = $api->getAuthorizedUser();
    $greeting = 'Welcome, ' . ($u->name ?? '@'.$u->username);
    $logout_link = '<a href="logout.php" class="logout">Logout</a>';
  } else {
    $newpoll_class = 'disabled';
    $greeting = '<a href="' . $api->getAuthURL() . '">Login with pnut</a>';
  }
  $title = '';
  if ($include_app_name) {
    $title = $api->app_name;
  }
  if (!empty($page_title)) {
    $title .= $include_app_name ? ' > ' : '';
    $title .= $page_title;
  }
  $script_str = '';
  foreach ($scripts as $script) {
    $script_str .= '<script src="scripts/' . $script . '.js"></script>';
  }

  return '<html><head><meta charset="utf-8"><title>'.$title.'</title><link rel="stylesheet" href="styles/style.css">'
  . $script_str
  . '</head><body><header>'
  . '<a href="index.php" class="homelink" title="Home"><div class="linkcontents">'
  . file_get_contents(__DIR__.'/icons/home.svg')
  . '<span class="linklabel">Home</span></div></a>'
  . '<a href="new_poll.php" class="newpolllink '.$newpoll_class.'" title="New Poll"><div class="linkcontents">'
  . file_get_contents(__DIR__.'/icons/plus.svg') //TODO
  . '<span class="linklabel">New Poll</span></div></a>'
  . $greeting
  . '<div class="spacer"></div>'
  . $logout_link
  . '</header><main>';
}

function get_page_footer()
{
  $version = json_decode(file_get_contents(__DIR__ . '/composer.json'), true)['version'];
  return '</main><footer>'
  . '<a href="https://phlaym.net/git/phlaym/Pfadlock/releases/tag/'.$version.'">Version ' . $version . '</a>'
  . '<a href="https://phlaym.net/git/phlaym/Dragonpolls" title="Source"><div class="linkcontents">'
  . file_get_contents(__DIR__.'/icons/home.svg')
  . '<span class="linklabel">Source Code</span></div></a>'
  . '</footer></body></html>';
}

function redirect($to)
{
  header('Location: '.$to);
  die('<html><meta http-equiv="refresh" content="0;url='.$to.'">'
    .'<script>window.location.replace("'.$to.'");</script></html>');
}

function get_source_set($user, int $base_size, int $max_scale = 3): string
{
  $srcset_entries = [$user->getAvatarUrl($base_size)];
  for ($s = 2; $s <= $max_scale; $s++) {
    $srcset_entries[] = $user->getAvatarUrl($base_size * $s) . ' ' . $s . 'x';
  }
  return implode(', ', $srcset_entries);
}
