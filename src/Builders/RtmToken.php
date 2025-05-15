<?php
/**
 * Modified version of Agora PHP Token Builder.
 * Original source: https://github.com/AgoraIO/Tools
 * License: MIT
 * 
 * Changes made to support Composer autoloading and improved structure.
 * @link https://github.com/AgoraIO/Tools/tree/master/DynamicKey/AgoraDynamicKey/php
 * @author Ujah Chigozie Peter
 */
namespace Peterujah\Agora\Builders;

use \Peterujah\Agora\Tokens\AccessToken;
use \Peterujah\Agora\Services\Rtm;
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Privileges;

class RtmToken
{
    /**
     * Build the RTM (Real-Time Messaging) token.
     *
     * @param Agora $client Instance of the Agora client configured with App ID and App Certificate.
     * @param User $user    User object containing the user ID/account and privilege expiration.
     *
     * @return string The generated RTM token.
     * 
     * @example - Example:
     * 
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtmToken;
     * 
     * $client = new Agora('yourAppId', 'yourAppCertificate');
     * $user = new User('2882341273'); // User's account, max length is 64 Bytes.
     * $user->setPrivilegeExpiration(time() + 600); // 10 minutes from now
     *
     * $token = RtmToken::buildToken($client, $user);
     * echo "RTM Token: " . $token;
     * ```
     * 
     * @example - Example:
     * 
     * ```php
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtmToken;
     * 
     * $client = new Agora('yourAppId', 'yourAppCertificate');
     * $client->setIdentifier('2882341273'); // User's account, max length is 64 Bytes.
     * $client->setExpiration(time() + 600); // 10 minutes from now
     *
     * $token = RtmToken::buildToken($client);
     * echo "RTM Token: " . $token;
     * ```
     */
    public static function buildToken(Agora $client, ?User $user = null): string
    {
        if(!$user instanceof User){
            $user = (new User($client->getIdentifier()))
                ->setPrivilegeExpire($client->getExpiration());
        }

        $service = (new Rtm($user))
            ->addPrivilege(Privileges::RTM_LOGIN, $user->getPrivilegeExpiration());

        return (new AccessToken($client))
            ->addService($service)
            ->build();
    }
}