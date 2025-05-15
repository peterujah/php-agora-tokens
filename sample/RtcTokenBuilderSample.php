<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Roles;
use \Peterujah\Agora\Builders\RtcTokenLegacy;

$channelName = "7d72365eb983485397e3e3f9d460bdda";
$uid = 2882341273;
$uidStr = "2882341273";
$expireTimeInSeconds = 3600;
$currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);

$user1 = (new User($uid))
    ->setPrivilegeExpire($privilegeExpiredTs)
    ->setChannel($channelName)
    ->setRole(Roles::RTC_PUBLISHER);

$token = RtcTokenLegacy::buildTokenWithUid($client, $user1);
echo 'Token with int uid: ' . $token . PHP_EOL;

$user2 = (new User($uidStr))
    ->setPrivilegeExpire($privilegeExpiredTs)
    ->setChannel($channelName)
    ->setRole(Roles::RTC_PUBLISHER);

$token = RtcTokenLegacy::buildTokenWithUserAccount($client, $user2);
echo 'Token with user account: ' . $token . PHP_EOL;
