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

use \Peterujah\Agora\Tokens\AccessTokenLegacy;
use \Peterujah\Agora\Privileges;
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;

class RtmTokenLegacy
{
    /**
     * Build a legacy RTM token using Agora's older `AccessTokenLegacy` format.
     *
     * @param Agora $client The Agora client instance containing the App ID and Certificate.
     * @param User|null $user The user object containing UID, role, and privilege expiration.
     *
     * Requirements:
     * - App ID: The App ID issued by Agora. See the [Agora Dashboard](https://console.agora.io/).
     * - App Certificate: Secret key from the Agora Dashboard used for token signing.
     * - Account: The unique user identifier.
     * - Role: Role_Rtm_User (typically 1).
     * - Expire Timestamp: Unix timestamp for when the token should expire.
     *
     * @return string The generated legacy RTM token.
     *
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtmTokenLegacy;
     * 
     * $client = new Agora('yourAppId', 'yourAppCertificate');
     * $user = new User('2882341273');
     * $user->setRole(Roles::RTM_USER); // Typically 1
     * $user->setPrivilegeExpiration(time() + 600); // Valid for 10 minutes
     *
     * $token = RtmTokenLegacy::buildToken($client, $user);
     * echo "Legacy RTM Token: " . $token;
     * ```
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtmTokenLegacy;
     * 
     * $client = new Agora('yourAppId', 'yourAppCertificate');
     * $client->setIdentifier('2882341273'); // User Account
     * $client->setExpiration(time() + 600); // Valid for 10 minutes
     *
     * $token = RtmTokenLegacy::buildToken($client);
     * echo "Legacy RTM Token: " . $token;
     * ```
     */
    public static function buildToken(Agora $client, ?User $user = null): string
    {
        $clone = clone $client;
        $userAccount = $clone->getIdentifier();
        $privilegeExpireTs = $clone->getExpiration();

        if($user instanceof User){
            $userAccount = $user->getAccount();
            $privilegeExpireTs = $user->getPrivilegeExpiration();
        }

        $clone->setChannel($userAccount); // Treat User Account as the channel for RTM
        //$clone->setRole($user->getRole());
        $clone->setRole(''); // Unused by agora
        $clone->setIdentifier(''); // Legacy RTM tokens use UID only

        return AccessTokenLegacy::init($clone)
            ->addPrivilege(Privileges::PERMISSIONS["kRtmLogin"], $privilegeExpireTs)
            ->build();
    }
}