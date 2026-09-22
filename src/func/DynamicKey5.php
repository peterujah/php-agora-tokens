<?php
namespace Peterujah\Agora\func;

// Globals

$version = "005";
$NO_UPLOAD = "0";
$AUDIO_VIDEO_UPLOAD = "3";

// InChannelPermissionKey
$ALLOW_UPLOAD_IN_CHANNEL = 1;

// Service Type
$MEDIA_CHANNEL_SERVICE = 1;
$RECORDING_SERVICE = 2;
$PUBLIC_SHARING_SERVICE = 3;
$IN_CHANNEL_PERMISSION = 4;

function generate_recording_key(
    string $appID, 
    string $appCertificate, 
    string $channelName, 
    int $ts, 
    int $randomInt, 
    int $uid, 
    int $expiredTs
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
        $GLOBALS["RECORDING_SERVICE"]
    );
}

function generate_media_channel_key(
    string $appID, 
    string $appCertificate, 
    string $channelName, 
    int $ts, 
    int $randomInt, 
    int $uid, 
    int $expiredTs
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
        $GLOBALS["MEDIA_CHANNEL_SERVICE"]
    );
}

function generate_in_channel_permission_key(
    string $appID, 
    string $appCertificate, 
    string $channelName, 
    int $ts, 
    int $randomInt, 
    int $uid, 
    int $expiredTs, 
    int $permission
): string
{
    $extra[$GLOBALS["ALLOW_UPLOAD_IN_CHANNEL"]] = $permission;

    return generate_dynamic_key(
        $appID, 
        $appCertificate, 
        $channelName, 
        $ts, 
        $randomInt, 
        $uid, 
        $expiredTs, 
        $GLOBALS["IN_CHANNEL_PERMISSION"],
        $extra
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
    string $serviceType, 
    array $extra = []
): string
{
    $signature = generate_signature(
        $serviceType, 
        $appID, 
        $appCertificate, 
        $channelName, 
        $uid, 
        $ts, 
        $randomInt, 
        $expiredTs, 
        $extra
    );

    $content = pack_content(
        $serviceType, 
        $signature, 
        hex2bin($appID), 
        $ts, 
        $randomInt, 
        $expiredTs, 
        $extra
    );

    return $GLOBALS['version'] . base64_encode($content);
}

function generate_signature(
    string $serviceType, 
    string $appID, 
    string $appCertificate, 
    string $channelName, 
    int $uid, 
    int $ts, 
    int $salt, 
    int $expiredTs, 
    array $extra = []
): string
{
    $rawAppID = hex2bin($appID);
    $rawAppCertificate = hex2bin($appCertificate);
    
    $buffer = pack("S", $serviceType);
    $buffer .= pack("S", strlen($rawAppID)) . $rawAppID;
    $buffer .= pack("I", $ts);
    $buffer .= pack("I", $salt);
    $buffer .= pack("S", strlen($channelName)) . $channelName;
    $buffer .= pack("I", $uid);
    $buffer .= pack("I", $expiredTs);

    $buffer .= pack("S", count($extra));

    foreach ($extra as $key => $value) {
        $buffer .= pack("S", $key);
        $buffer .= pack("S", strlen($value)) . $value;
    } 

    return strtoupper(hash_hmac('sha1', $buffer, $rawAppCertificate));
}

function pack_string(string $value): string
{
    return pack("S", strlen($value)) . $value;
}

function pack_content(
    string $serviceType, 
    string $signature, 
    string $appID, 
    int $ts, 
    int $salt, 
    int $expiredTs, 
    array $extra = []
): string
{
    $buffer = pack("S", $serviceType);
    $buffer .= pack_string($signature);
    $buffer .= pack_string($appID);
    $buffer .= pack("I", $ts);
    $buffer .= pack("I", $salt);
    $buffer .= pack("I", $expiredTs);

    $buffer .= pack("S", count($extra));

    foreach ($extra as $key => $value) {
        $buffer .= pack("S", $key);
        $buffer .= pack_string($value);
    } 

    return $buffer;
}
