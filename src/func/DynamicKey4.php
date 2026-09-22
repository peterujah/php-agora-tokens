<?php
namespace Peterujah\Agora\func;

function generate_recording_key(
    string $appID, 
    string $appCertificate, 
    string $channelName, 
    int $ts, 
    int $randomInt, 
    int $uid, 
    int $expiredTs,
    string $serviceType = 'ARS'
)
{
    return generate_dynamic_key(
        $appID, 
        $appCertificate, 
        $channelName, 
        $ts, 
        $randomInt, 
        $uid, 
        $expiredTs,
        $serviceType
    );
}

function generate_media_channel_key(
    string $appID, 
    string $appCertificate, 
    string $channelName, 
    int $ts, 
    int $randomInt, 
    int $uid, 
    int $expiredTs,
    string $serviceType = 'ACS'
)
{
    return generate_dynamic_key(
        $appID, 
        $appCertificate, 
        $channelName, 
        $ts, 
        $randomInt, 
        $uid, 
        $expiredTs,
        $serviceType
    );
}

function generate_dynamic_key(
    string $appID, 
    string $appCertificate, 
    string $channelName, 
    int $ts, 
    int $randomInt, 
    int $uid, 
    int $expiredTs,
    string $serviceType
) : string
{
    $version = "004";

    $randomStr = "00000000" . dechex($randomInt);
    $randomStr = substr($randomStr,-8);

    $uidStr = "0000000000" . $uid;
    $uidStr = substr($uidStr,-10);
    
    $expiredStr = "0000000000" . $expiredTs;
    $expiredStr = substr($expiredStr,-10);

    $signature = generate_signature(
        $appID, 
        $appCertificate, 
        $channelName, 
        $ts,
        $randomStr, 
        $uidStr, 
        $expiredStr,
        $serviceType
    );

    return $version 
        . $signature 
        . $appID 
        . $ts 
        . $randomStr 
        . $expiredStr;
}

function generate_signature(
    string $appID, 
    string $appCertificate, 
    string $channelName, 
    int $ts, 
    int $randomStr, 
    string $uidStr, 
    string $expiredStr,
    string $serviceType
): string
{
    $concat = $serviceType 
        . $appID 
        . $ts 
        . $randomStr 
        . $channelName 
        . $uidStr 
        . $expiredStr;

    return hash_hmac('sha1', $concat, $appCertificate);
}
