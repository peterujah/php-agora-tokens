<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Privileges;
use \Peterujah\Agora\Tokens\AccessToken;
use \Peterujah\Agora\Services\Rtc;
use \Peterujah\Agora\Services\Rtm;
use \Peterujah\Agora\Services\Chat;

$channelName = "7d72365eb983485397e3e3f9d460bdda";
$uid = 2882341273;
$expireTimeInSeconds = 600;


$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);
//$client->setExpiration($expireTimeInSeconds);


$user = new User($uid);
$user->setPrivilegeExpire($expireTimeInSeconds);
$user->setChannel($channelName);


// $token = \Peterujah\Agora\TokenBuilder\RtmToken::buildToken($client, $user);
$accessToken = new AccessToken();

// grant rtc privileges
$serviceRtc = new Rtc($user);
$serviceRtc->addPrivilege(Privileges::RTC_JOIN_CHANNEL, $expireTimeInSeconds);
$accessToken->addService($serviceRtc);

// grant rtm privileges
$serviceRtm = new Rtm($user);
$serviceRtm->addPrivilege(Privileges::RTM_LOGIN, $expireTimeInSeconds);
$accessToken->addService($serviceRtm);

// grant chat privileges
$serviceChat = new Chat($user);
$serviceChat->addPrivilege(Privileges::CHAT_USER, $expireTimeInSeconds);
$accessToken->addService($serviceChat);

$token = $accessToken->build();
echo 'Token with RTC, RTM, CHAT privileges: ' . $token . PHP_EOL;
