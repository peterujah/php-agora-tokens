<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Roles;
use \Peterujah\Agora\Builders\RtcToken;


$channelName = "7d72365eb983485397e3e3f9d460bdda";
$uid = 2882341273;
$uidStr = "2882341273";
$tokenExpirationInSeconds = 3600;
$privilegeExpirationInSeconds = 3600;
$joinChannelPrivilegeExpireInSeconds = 3600;
$pubAudioPrivilegeExpireInSeconds = 3600;
$pubVideoPrivilegeExpireInSeconds = 3600;
$pubDataStreamPrivilegeExpireInSeconds = 3600;

$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);
$client->setExpiration($tokenExpirationInSeconds);

$user1 = (new User($uid))
    ->setPrivilegeExpire($privilegeExpirationInSeconds)
    ->setChannel($channelName)
    ->setRole(Roles::RTC_PUBLISHER);

$token = RtcToken::buildTokenWithUid($client, $user1);
echo 'Token with int uid: ' . $token . PHP_EOL;

$user2 = (new User($uidStr))
    ->setPrivilegeExpire($privilegeExpirationInSeconds)
    ->setChannel($channelName)
    ->setRole(Roles::RTC_PUBLISHER);

$token = RtcToken::buildTokenWithUserAccount($client, $user2);
echo 'Token with user account: ' . $token . PHP_EOL;

$user3 = (new User($uid))
    ->setChannelPrivilegeExpire($joinChannelPrivilegeExpireInSeconds)
    ->setAudioPrivilegeExpire($pubAudioPrivilegeExpireInSeconds)
    ->setVideoPrivilegeExpire($pubVideoPrivilegeExpireInSeconds)
    ->setStreamPrivilegeExpire($pubDataStreamPrivilegeExpireInSeconds)
    ->setChannel($channelName);

$token = RtcToken::buildTokenWithUidAndPrivilege($client, $user3);
echo 'Token with int uid and privilege: ' . $token . PHP_EOL;

$user4 = (new User($uidStr))
    ->setChannelPrivilegeExpire($joinChannelPrivilegeExpireInSeconds)
    ->setAudioPrivilegeExpire($pubAudioPrivilegeExpireInSeconds)
    ->setVideoPrivilegeExpire($pubVideoPrivilegeExpireInSeconds)
    ->setStreamPrivilegeExpire($pubDataStreamPrivilegeExpireInSeconds)
    ->setChannel($channelName);

$token = RtcToken::buildTokenWithUserAccountAndPrivilege($client, $user4);
echo 'Token with user account and privilege: ' . $token . PHP_EOL;

$user5 = (new User($uidStr))
    ->setPrivilegeExpire($privilegeExpirationInSeconds)
    ->setChannel($channelName)
    ->setRole(Roles::RTC_PUBLISHER);

$token = RtcToken::buildTokenWithRtm($client, $user5);
echo 'Token with RTM: ' . $token . PHP_EOL;
