<?php
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\Builders\FpaToken;

$client = new Agora(
    getenv("AGORA_APP_ID"), // Need to set environment variable AGORA_APP_ID
    getenv("AGORA_APP_CERTIFICATE"), // Need to set environment variable AGORA_APP_CERTIFICATE
);

$token = FpaToken::buildToken($client);
echo 'Token with FPA service: ' . $token . PHP_EOL;
