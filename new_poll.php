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
  echo get_page_header('New Poll', true, ['poll']);
} catch (\Exception $e) {
  die('Something went wrong :( "'.$e->getMessage().'"');
}

if (!$api->isAuthenticated(false, true)) {
  die('You need to be logged in to create a new poll!');
}
