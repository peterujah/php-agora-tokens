<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Builders\ChatToken;

$userUuid = "2882341273";
$expireTimeInSeconds = 3600;

$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);
$client->setExpiration($expireTimeInSeconds);

$user = (new User($userUuid))->setPrivilegeExpire($expireTimeInSeconds);

$token = ChatToken::buildUserToken($client, $user);
echo 'Chat user token: ' . $token . PHP_EOL;

$token = ChatToken::buildAppToken($client->setExpiration($expireTimeInSeconds));
echo 'Chat app token: ' . $token . PHP_EOL;
