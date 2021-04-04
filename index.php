<?php

require_once __DIR__ .'/bootstrap.php';

// Support the old Dragonpolls links
if (isset($_GET['poll'])) {
  redirect('view_poll.php?id='.$_GET['poll']);
}
echo get_page_header();
echo get_page_footer();
