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
  if ($api->isAuthenticated(false, true)) {
    $u = $api->getAuthorizedUser();
    $greeting = 'Welcome, ' . ($u->name ?? '@'.$u->username);
    $logout_link = '<a href="logout.php" class="logout">Logout</a>';
  } else {
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
    //$script_str .= '<link rel="script" href="scripts/' . $script . '.js">';
    $script_str .= '<script src="scripts/' . $script . '.js">';
  }
  return '<html><head><meta charset="utf-8"><title>'.$title.'</title><link rel="stylesheet" href="styles/style.css">'
  . $script_str
  . '</head><body><header>'
  . '<a href="index.php" class="homelink" title="Home"><div class="linkcontents">'
  . file_get_contents(__DIR__.'/icons/home.svg')
  . '<span class="linklabel">Home</span></div></a>'
  . $greeting
  . '<div class="spacer"></div>'
  . $logout_link
  . '</header>';
}
