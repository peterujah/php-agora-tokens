<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Builders\RtmToken;

$user = "2882341273";
$expireTimeInSeconds = 3600;

$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);
$client->setExpiration($privilegeExpiredTs);

$user = (new User($userId))
    ->setPrivilegeExpire($expireTimeInSeconds)
    ->setChannel($channelName);

$token = RtmToken::buildToken($client, $user);
echo 'Rtm Token: ' . $token . PHP_EOL;
