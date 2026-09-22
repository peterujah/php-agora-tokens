<?php
namespace Peterujah\Agora\func;
$SDK_VERSION = "1";

function get_token(
    string $appid, 
    string $appCertificate, 
    string|int $account, 
    int $validTimeInSeconds
): string 
{
    global $SDK_VERSION;
    $expiredTime = time() + $validTimeInSeconds;

    $items = [];
    $items[] = $SDK_VERSION;
    $items[] = $appid;
    $items[] = $expiredTime;
    $items[] = md5($account.$appid.$appCertificate.$expiredTime);
    $items[] = $SDK_VERSION;

    return join(":", $items);
}
