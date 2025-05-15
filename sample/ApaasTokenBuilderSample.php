<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Builders\ApaasToken;

$expire = 600;
$roomUuid = "123";
$userUuid = "2882341273";
$role = 1;

$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);
$client->setExpiration($expire);

$user1 = (new User($userUuid))
    ->setPrivilegeExpire($expire)
    ->setRoom($roomUuid)
    ->setRole($role);

$token = ApaasToken::buildRoomUserToken($client, $user1);
echo 'Apaas room user token: ' . $token . PHP_EOL;

$user2 = (new User($userUuid))
    ->setPrivilegeExpire($expire);
$token = ApaasToken::buildUserToken($client, $user2);
echo 'Apaas user token: ' . $token . PHP_EOL;

$token = ApaasToken::buildAppToken($client->setExpiration($expire));
echo 'Apaas app token: ' . $token . PHP_EOL;
