<?php
$config = include(__DIR__.'/config.php');
$version = json_decode(file_get_contents(__DIR__.'/composer.json'), true)['version'];

if (count($argv) < 2) {
  die('Usage: ' . $argv[0] . ' <release_name>');
}
$release_name = $argv[1];
$release_name_arg = escapeshellarg($release_name);
print('Creating tag'.PHP_EOL);
$output = shell_exec("git tag -s $version -m $release_name_arg");
print($output.PHP_EOL);
print(''.PHP_EOL);

print('Pusing tag'.PHP_EOL);
$output = shell_exec('git push --tags');
print($output.PHP_EOL);
print(''.PHP_EOL);

print('Creating release on Github'.PHP_EOL);
$gh_user = $config['github_username'];
$gh_repo = $config['github_repo'];
$gh_token = $config['github_token'];
$payload = escapeshellarg('{"tag_name":"'.$version.'", "name":"'.$release_name.'"}');
$ct_header = '"Content-Type: application/json"';
$output = shell_exec("curl -X POST -H $ct_header -u $gh_user:$gh_token https://api.github.com/repos/$gh_user/$gh_repo/releases -d $payload");
print($output.PHP_EOL);
print(''.PHP_EOL);

print('Creating release on Gitea'.PHP_EOL);
$g_url = $config['gitea_url'];
$g_token = $config['gitea_token'];
$token_header = '"Authorization: token ' . $g_token . '"';
$output = shell_exec("curl -X POST -H $ct_header -H $token_header $g_url -d $payload");
print($output.PHP_EOL);
