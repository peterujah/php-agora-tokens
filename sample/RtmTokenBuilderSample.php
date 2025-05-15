<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Roles;
use \Peterujah\Agora\Builders\RtmTokenLegacy;

$userId = "test_user_id";
$expireTimeInSeconds = 3600;
$currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);
$client->setExpiration($privilegeExpiredTs);

$user = (new User($userId))
    ->setPrivilegeExpire($privilegeExpiredTs)
    ->setChannel($channelName)
    ->setRole(Roles::RTM_USER);

$token = RtmTokenLegacy::buildToken($client, $user);
echo 'Rtm Token: ' . $token . PHP_EOL;
