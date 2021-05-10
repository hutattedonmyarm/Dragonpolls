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
    $user_avatar_url = $u->getAvatarUrl(30);
    $user_avatar_url_srcset = get_source_set($u, 30);
    $avatar = '<img src="' . $user_avatar_url . '" class="avatar" srcset="' . $user_avatar_url_srcset . '"/>';
    $greeting = $avatar . '<span class="greeting">Welcome, ' . ($u->name ?? '@'.$u->username) . '</span>';
    $logout_link = '<a href="logout.php" class="logout">Logout</a>';
  } else {
    $newpoll_class = 'disabled';
    $greeting = '<a href="' . $api->getAuthURL('?from=' . $_SERVER['REQUEST_URI']) . '">Login with pnut</a>';
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

  $home_link = get_icon_link(
      'index.php',
      'Home',
      'home',
      'homelink'
  );
  $new_poll_link = get_icon_link(
      'new_poll.php',
      'New Poll',
      'plus',
      'newpolllink'
  );

  $favicons = '<link rel="icon" href="icons/favicon/favicon-32.png" sizes="32x32">'
  . '<link rel="icon" href="icons/favicon/favicon-96.png" sizes="96x96">'
  . '<link rel="icon" href="icons/favicon/favicon-128.png" sizes="128x128">'
  . '<link rel="icon" href="icons/favicon/favicon-192.png" sizes="192x192">'
  . '<link rel="icon" href="icons/favicon/favicon-228.png" sizes="228x228">'
  . '<link rel="shortcut icon" sizes="196x196" href=“icons/favicon/favicon-196.png">'
  . '<link rel="apple-touch-icon" href="icons/favicon/favicon-120.png" sizes="120x120">'
  . '<link rel="apple-touch-icon" href="path/to/favicon-152.png" sizes="152x152">'
  . '<link rel="apple-touch-icon" href="path/to/favicon-180.png" sizes="180x180">'
  . '<link rel="icon" type="image/svg+xml" href="icons/favicon/favicon.svg">';

  return '<html><head><meta charset="utf-8">'
  . $favicons
  . '<title>'.$title.'</title><link rel="stylesheet" href="styles/style.css">'
  . $script_str
  . '<meta name="viewport" content="width=device-width,initial-scale=1">'
  . '</head><body><header>'
  . $home_link
  . $new_poll_link
  . $greeting
  . '<div class="spacer"></div>'
  . $logout_link
  . '</header><main>';
}

function get_icon_link(string $href, string $label, string $icon, string $class)
{
  return '<a href="'.$href.'" title="'.$label.'" class="'.$class.'">'
  . '<div class="linkcontents">'
  . file_get_contents(__DIR__.'/icons/'.$icon.'.svg')
  . '<span class="linklabel">'.$label.'</span></div></a>';
}

function get_page_footer()
{
  $version = json_decode(file_get_contents(__DIR__ . '/composer.json'), true)['version'];
  $repo_link = get_icon_link(
      'https://phlaym.net/git/phlaym/Dragonpolls',
      'Source Code',
      'src',
      'sourcecode'
  );
  $issues_link = get_icon_link(
      'https://github.com/hutattedonmyarm/Dragonpolls/issues',
      'Issues',
      'issues',
      'issues'
  );
  return '</main><footer>'
  . '<a href="https://phlaym.net/git/phlaym/Dragonpolls/releases/tag/'.$version.'">Version ' . $version . '</a>'
  . $repo_link
  . $issues_link
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

function make_banner(string $type, string $content, string $custom_symbol = null): string
{
  if (empty($custom_symbol)) {
    $custom_symbol = $type === 'success' ? '&#2713;' : '&#x00D7;';
  }

  return '<div class="banner-wrapper">'
  . ' <div class="'
  . $type
  . ' banner"><span>'
  . $custom_symbol
  . '</span>'
  . $content
  . '</div></div>';
}

function quit(string $error, string $error_details = '')
{
  die(make_banner('error', $error) . $error_details . get_page_footer());
}
