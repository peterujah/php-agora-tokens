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

class ApaasToken
{
    /**
     * Generates a token for a user to access a specific room.
     * 
     * This token allows a user to join an APAAS room, use RTM (Real-Time Messaging),
     * and participate in chat functionality. The privileges are granted based on
     * the provided expiration time.
     * 
     * @param Agora $client The Agora client instance containing App ID and App Certificate.
     * @param User $user The user instance with a unique ID, room ID, role, and expiration settings.
     * 
     * @return string The generated token for the user within the specified room.
     * 
     * @example
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\ApaasToken;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * 
     * $user = new User(2882341273); // Unique user ID
     * $user->setRoom('user-room-100'); // Unique room ID
     * $user->setRole(Roles::RTC_PUBLISHER); // Set user's role
     * $user->setPrivilegeExpire(time() + 3600); // Token valid for 1 hour
     * 
     * echo ApaasToken::buildRoomUserToken($client, $user);
     * ```
     */
    public static function buildRoomUserToken(Agora $client, User $user): string
    {
        $expire = $user->getPrivilegeExpiration();

        return (new AccessToken($client))
            ->addService((new Apaas($user))->addPrivilege(Privileges::APAAS_ROOM_USER, $expire))
            ->addService((new Rtm($user))->addPrivilege(Privileges::RTM_LOGIN, $expire))
            ->addService((new Chat($user->hash()))->addPrivilege(Privileges::CHAT_USER, $expire))
            ->build();
    }

    /**
     * Generates a user-level APAAS token without room association.
     * 
     * This token grants access to APAAS services without binding the user to a specific room.
     * Useful for scenarios where a general application-level user token is needed.
     * 
     * @param Agora $client The Agora client instance containing App ID and App Certificate.
     * @param User $user The user instance with a unique ID and expiration settings.
     *
     * @return string The generated user-level APAAS token.
     * 
     * @example
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\ApaasToken;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * 
     * $user = new User('user-uid'); // Unique user ID
     * $user->setPrivilegeExpire(time() + 3600); // Token valid for 1 hour
     * 
     * echo ApaasToken::buildUserToken($client, $user);
     * ```
     */
    public static function buildUserToken(Agora $client, User $user): string
    {
        $clone = clone $user;
        $clone->setRoom(''); // Unset the room association

        $service = (new Apaas($clone))
            ->addPrivilege(Privileges::APAAS_USER, $clone->getPrivilegeExpiration());

        return (new AccessToken($client))
            ->addService($service)
            ->build();
    }

    /**
     * Generates an application-level APAAS token.
     * 
     * This token grants general access to the APAAS service for the entire application,
     * without being associated with any specific user or room.
     * 
     * @param Agora $client The Agora client instance containing App ID and App Certificate.
     *
     * @return string The generated application-level APAAS token.
     * 
     * @example
     * ```php
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\ApaasToken;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $client->setExpiration(time() + 3600); // Token valid for 1 hour
     * 
     * echo ApaasToken::buildAppToken($client);
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