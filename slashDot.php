<?php
header('Content-type: text/plain; charset=utf-8');

$team_token = $_ENV['TEAM_TOKEN'];
$command_token = $_ENV['COMMAND_TOKEN'];
$command = $_POST['command'];
$text = $_POST['text'];
$token = $_POST['token'];
$user_id = $_POST['user_id'];
$channel_id = $_POST['channel_id'];
$stash = 'https://slashdot.now.sh/stash/';

// exit if the token doesn't match
if ($token != $command_token){
    die ("The token for the slash command doesn't match.");
}

// keywords array
$keywords = [
    'fail' => [ $stash.'fail.gif' ],
    'ghosts' => [ 'http://i.imgur.com/5VPxKHI.jpg'],
    'huff' => [ 'http://cdn.makeagif.com/media/10-28-2015/edaaQK.gif'],
    'sweet1' => [ 'http://imgur.com/51j3kvR.jpg'],
    'swivel' => [ 'http://cdn.makeagif.com/media/12-02-2015/k2OO6M.gif'],
    'the purple one' => [ 'http://i.imgur.com/nhHSVOd.gifv']
];

// check if keyword exists or leave
$img_url = array_key_exists($text, $keywords) ? $keywords[$text][0] : '';
if (!$img_url) die ("Invalid option. Please reference <http://kevindavi5.github.io/slashDot>");

// get the user's info
$api_user_url = 'https://slack.com/api/users.info?token='.$team_token.'&user='.$user_id.'&pretty=1';
$api_user_ch = curl_init($api_user_url);
curl_setopt($api_user_ch, CURLOPT_RETURNTRANSFER, true);
$user_response = json_decode(curl_exec($api_user_ch), true);
curl_close($api_user_ch);

// create url parameters
$data = [
    "token" => $team_token,
    "channel" => $channel_id,
    "username" => $user_response['user']['real_name'],
    "icon_url" => $user_response['user']['profile']['image_48'],
    "text" => '/. ' . $text
];

// create message attachment
$attachments = [
    "image_url" => $img_url,
    "title" => $text,
    "title_link" => $img_url
];

// post to slack api
$api_post_url = 'https://slack.com/api/chat.postMessage?' . http_build_query($data) .'&attachments=['. json_encode($attachments) .']';
$api_post_ch = curl_init($api_post_url);
curl_setopt($api_post_ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($api_post_ch);
curl_close($api_post_ch);

?>