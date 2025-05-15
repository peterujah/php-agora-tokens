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
use \Peterujah\Agora\Services\Apaas;
use \Peterujah\Agora\Services\Rtm;
use \Peterujah\Agora\Services\Chat;
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Privileges;

class EducationToken
{
    /**
     * Build a token for a user to join a specific room.
     *
     * @param Agora $client Instance of Agora client initialized with App ID and Certificate.
     * @param User $user Instance of user, must include UUID, room UUID, role, and privilege expiration.
     * 
     * @return string The generated room user token.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $user = new User(2882341273);
     * $user->setRoom('room-uuid');
     * $user->setRole(Roles::RTC_PUBLISHER);
     * $user->setPrivilegeExpire(time() + 3600);
     * 
     * $token = EducationToken::buildRoomUserToken($client, $user);
     * ```
     */
    public static function buildRoomUserToken(Agora $client, User $user): string
    {
        $expire = $user->getPrivilegeExpiration();

        return (new AccessToken($client))
            ->addService((new Apaas($user))->addPrivilege(Privileges::APAAS_ROOM_USER, $expire))
            ->addService((new Rtm($user))->addPrivilege(Privileges::RTM_LOGIN, $expire))
            ->addService((new Chat($user->hash()))->addPrivilege(Privileges::CHAT_USER, $expire))->build();
    }

    /**
     * Build a general token for a user without room context.
     *
     * @param Agora $client Instance of Agora client initialized with App ID and Certificate.
     * @param User $user Instance of user, must include UUID and privilege expiration.
     * 
     * @return string The generated user token.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $user = new User('2882341273');
     * $user->setPrivilegeExpire(time() + 3600);
     * 
     * $token = EducationToken::buildUserToken($client, $user);
     * ```
     */
    public static function buildUserToken(Agora $client, User $user): string
    {
        $clone = clone $user;
        $clone->setRoom('');

        $service = (new Apaas($clone))
            ->addPrivilege(Privileges::APAAS_USER, $clone->getPrivilegeExpiration());

        return (new AccessToken($client))
            ->addService($service)
            ->build();
    }

    /**
     * Build a token for app-level access (not tied to a specific user).
     *
     * @param Agora $client Instance of Agora client initialized with App ID and Certificate.
     * 
     * @return string The generated app token.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\Agora;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $client->setExpiration(time() + 3600);
     * 
     * $token = EducationToken::buildAppToken($client);
     * ```
     */
    public static function buildAppToken(Agora $client): string
    {
        $service = (new Apaas())
            ->addPrivilege(Privileges::APAAS_APP, $client->getExpiration());
        
        return (new AccessToken($client))
            ->addService($service)
            ->build();
    }
}
