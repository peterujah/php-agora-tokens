<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Builders\EducationToken;

$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);

$expire = 600;
$roomUuid = "123";
$userUuid = "2882341273";
$role = 1;

$user1 = (new User($userUuid))
    ->setPrivilegeExpire($expire)
    ->setRoom($roomUuid)
    ->setRole($role);

$token = EducationToken::buildRoomUserToken($client, $user1);
echo 'Education room user token: ' . $token . PHP_EOL;


$user2 = (new User($userUuid))
    ->setPrivilegeExpire($expire);

$token = EducationToken::buildUserToken($client, $user2);
echo 'Education user token: ' . $token . PHP_EOL;

$token = EducationToken::buildAppToken($client->setExpiration($expire));
echo 'Education app token: ' . $token . PHP_EOL;
