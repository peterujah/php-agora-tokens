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
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\Services\Rtc;
use \Peterujah\Agora\Services\Rtm;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Roles;
use \Peterujah\Agora\Privileges;

class RtcToken
{
    /**
     * Builds an RTC token for the user with standard privileges.
     *
     * Grants the user permission to join the channel, and if the role is `RTC_PUBLISHER`, additional privileges
     * to publish audio, video, and data streams are included.
     *
     * @param Agora $client The Agora client instance containing app ID and certificate.
     * @param User $user The user object with role and privilege information.
     * 
     * @return string The generated RTC token.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtcToken;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $client->setExpiration(time() + 600); // Token expiration
     * 
     * $user = new User(2882341273);
     * $user->setChannel('channel-uuid-100');
     * $user->setRole(Roles::RTC_PUBLISHER);
     * $user->setPrivilegeExpire(time() + 3600); // Optional privilege expiration
     * 
     * $token = EducationToken::buildRoomUserToken($client, $user);
     * ```
     */
    public static function buildTokenWithUid(Agora $client, User $user): string
    {
        return self::buildTokenWithUserAccount($client, $user);
    }

    /**
     * Builds an RTC token for the user with standard privileges.
     *
     * Grants the user permission to join the channel, and if the role is `RTC_PUBLISHER`, additional privileges
     * to publish audio, video, and data streams are included.
     *
     * @param Agora $client The Agora client instance containing app ID and certificate.
     * @param User $user The user object with role and privilege information.
     * 
     * @return string The generated RTC token.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtcToken;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $client->setExpiration(time() + 600); // Token expiration
     * 
     * $user = new User('2882341273');
     * $user->setChannel('channel-uuid-100');
     * $user->setRole(Roles::RTC_PUBLISHER);
     * $user->setPrivilegeExpire(time() + 3600); // Optional privilege expiration
     * 
     * $token = EducationToken::buildRoomUserToken($client, $user);
     * ```
     */
    public static function buildTokenWithUserAccount(Agora $client, User $user): string
    {
        $privilegeExpire = $user->getPrivilegeExpiration();
        $serviceRtc = new Rtc($user);
        $serviceRtc->addPrivilege(Privileges::RTC_JOIN_CHANNEL, $privilegeExpire);

        if ($user->getRole() === Roles::RTC_PUBLISHER) {
            $serviceRtc->addPrivilege(Privileges::RTC_PUBLISH_AUDIO_STREAM, $privilegeExpire);
            $serviceRtc->addPrivilege(Privileges::RTC_PUBLISH_VIDEO_STREAM, $privilegeExpire);
            $serviceRtc->addPrivilege(Privileges::RTC_PUBLISH_DATA_STREAM, $privilegeExpire);
        }

        return (new AccessToken($client))
            ->addService($serviceRtc)
            ->build();
    }

    /**
     * Builds an RTC token for the user with standard privileges.
     *
     * Grants the user permission to join the channel, and if the role is `RTC_PUBLISHER`, additional privileges
     * to publish audio, video, and data streams are included.
     *
     * @param Agora $client The Agora client instance containing app ID and certificate.
     * @param User $user The user object with role and privilege information.
     * 
     * @return string The generated RTC token.
     */
    public static function buildTokenWithUidAndPrivilege(Agora $client, User $user): string
    {
        return self::buildTokenWithUserAccountAndPrivilege($client, $user);
    }

    /**
     * Builds an RTC token for the user with explicit privilege expiration settings.
     * 
     * This method supports generating a token with the following privileges:
     * - Joining an RTC channel.
     * - Publishing audio in an RTC channel.
     * - Publishing video in an RTC channel.
     * - Publishing data streams in an RTC channel.
     *
     * The privileges for publishing audio, video, and data streams in an RTC channel apply only if you have
     * enabled co-host authentication.
     *
     * A user can have multiple privileges. Each privilege is valid for a maximum of 24 hours.
     * The SDK triggers the onTokenPrivilegeWillExpire and onRequestToken callbacks when the token is about to expire
     * or has expired. The callbacks do not report the specific privilege affected, and you need to maintain
     * the respective timestamp for each privilege in your app logic. After receiving the callback, you need
     * to generate a new token, and then call renewToken to pass the new token to the SDK, or call joinChannel to re-join
     * the channel.
     *
     * @param Agora $client The Agora client instance containing app ID and certificate.
     * @param User $user The user object providing custom privilege expiration timestamps.
     * @return string The generated RTC token.
     */
    public static function buildTokenWithUserAccountAndPrivilege(Agora $client, User $user): string
    {
        $serviceRtc = (new Rtc($user))
            ->addPrivilege(Privileges::RTC_JOIN_CHANNEL, $user->getChannelPrivilegeExpiration())
            ->addPrivilege(Privileges::RTC_PUBLISH_AUDIO_STREAM, $user->getAudioPrivilegeExpiration())
            ->addPrivilege(Privileges::RTC_PUBLISH_VIDEO_STREAM, $user->getVideoPrivilegeExpiration())
            ->addPrivilege(Privileges::RTC_PUBLISH_DATA_STREAM, $user->getStreamPrivilegeExpiration());

        return (new AccessToken($client))->addService($serviceRtc)->build();
    }

    /**
     * Builds an RTC token with admin privilege control based on the user role.
     *
     * @param Agora $client The Agora client instance.
     * @param User $user The user with per-privilege expiration getters.
     * 
     * @return string The generated RTC token.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\RtcToken;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $client->setExpiration(time() + 600); // Token expiration
     * 
     * $user = new User('2882341273');
     * $user->setChannel('channel-uuid-100');
     * $user->setRole(Roles::RTC_PUBLISHER);
     * $user->setPrivilegeExpire(time() + 3600); // Optional privilege expiration
     * 
     * $token = EducationToken::buildRoomUserToken($client, $user);
     * ```
     */
    public static function buildTokenWithRtm(Agora $client, User $user): string
    {
        $role = $user->getRole();
        $privilegeExpire = $user->getPrivilegeExpiration();
        $token = (new AccessToken($client));
        $serviceRtc = new Rtc($user);

        $serviceRtc->addPrivilege(Privileges::RTC_JOIN_CHANNEL, $privilegeExpire);
        if ($role === Roles::RTC_PUBLISHER) {
            $serviceRtc->addPrivilege(Privileges::RTC_PUBLISH_AUDIO_STREAM, $privilegeExpire);
            $serviceRtc->addPrivilege(Privileges::RTC_PUBLISH_VIDEO_STREAM, $privilegeExpire);
            $serviceRtc->addPrivilege(Privileges::RTC_PUBLISH_DATA_STREAM, $privilegeExpire);
        }
        $token->addService($serviceRtc);

        $serviceRtm = new Rtm($user);
        $serviceRtm->addPrivilege(Privileges::RTM_LOGIN, $client->getExpiration());

        return $token->addService($serviceRtm)->build();
    }
}
