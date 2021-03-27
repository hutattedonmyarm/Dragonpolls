<?php

require_once __DIR__ .'/bootstrap.php';

$is_authenticated = $api->isAuthenticated();
if (!$is_authenticated) {
    echo '<a href="' . $api->getAuthURL() . '">Login with pnut</a>';
} else {
    $u = $api->getAuthorizedUser();
    echo 'Welcome @'.$u->username;
}
