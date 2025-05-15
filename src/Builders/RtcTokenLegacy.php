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
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Roles;
use \Peterujah\Agora\Privileges;

class RtcTokenLegacy
{
    /**
     * Build RTC token using user UID (internally delegates to user account).
     *
     * @param Agora $client Agora client instance configured with App ID and App Certificate.
     * @param User $user User object containing UID, channel name, role, and expiration details.
     *
     * @return string The generated RTC token using the user's UID.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtcTokenLegacy;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $user = new User(2882341273); // User UID
     * $user->setRole(Roles::RTC_ATTENDEE);
     * $user->setChannel('user-channel-100');
     * $user->setPrivilegeExpire(time() + 3600);
     * 
     * $token = RtcTokenLegacy::buildTokenWithUserAccount($client, $user);
     * ```
     */
    public static function buildTokenWithUid(Agora $client, User $user): string
    {
        return self::buildTokenWithUserAccount($client, $user);
    }

    /**
     * Build RTC token using user account.
     * 
     * Required fields:
     * - App ID: Issued by Agora via the Dashboard.
     * - App Certificate: Issued alongside the App ID.
     * - Channel Name: Unique string identifying the RTC session.
     * - UID/User Account: Must be unique within the channel.
     * - Role:
     *   - `Roles::RTC_ATTENDEE` (broadcaster)
     *   - `Roles::RTC_PUBLISHER` (broadcaster)
     *   - `Roles::RTC_ADMIN` (admin with full privileges)
     *   - Other roles may get join-only access.
     * - Privilege Expiration: A Unix timestamp (e.g., `time() + 600`) determining token validity.
     *
     * @param Agora $client Agora client instance configured with App ID and App Certificate.
     * @param User $user User object containing channel name, role, user ID/account,
     *                   and token privilege expiration time.
     *
     * @return string The generated RTC token using the user account.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtcTokenLegacy;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $user = new User('2882341273'); // User account
     * $user->setRole(Roles::RTC_ATTENDEE);
     * $user->setChannel('user-channel-100');
     * $user->setPrivilegeExpire(time() + 3600);
     * 
     * $token = RtcTokenLegacy::buildTokenWithUserAccount($client, $user);
     * ```
     */
    public static function buildTokenWithUserAccount(Agora $client, User $user): string
    {
        $role = $user->getRole();
        $privilegeExpireTs = $user->getPrivilegeExpiration();
 
        $token = AccessTokenLegacy::init($client, $user);
        $Privileges = Privileges::PERMISSIONS;
        $token->addPrivilege($Privileges["kJoinChannel"], $privilegeExpireTs);

        if (($role == Roles::RTC_ATTENDEE) ||
            ($role == Roles::RTC_PUBLISHER) ||
            ($role == Roles::RTC_ADMIN)
        ) {
            $token->addPrivilege($Privileges["kPublishVideoStream"], $privilegeExpireTs);
            $token->addPrivilege($Privileges["kPublishAudioStream"], $privilegeExpireTs);
            $token->addPrivilege($Privileges["kPublishDataStream"], $privilegeExpireTs);
        }
        return $token->build();
    }
}
