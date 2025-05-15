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
use \Peterujah\Agora\Services\Chat;
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Privileges;

class ChatToken
{
    /**
     * Generates a user-level Chat token.
     * 
     * This method builds a Chat token for a specific user, granting them permission
     * to use the Agora Chat service for a limited duration.
     * 
     * @param Agora $client An instance of the Agora client containing the App ID and App Certificate.
     * @param User $user The user instance representing the identity and privilege expiration time.
     * 
     * @return string The generated Chat token for the specified user.
     * 
     * @example - Example:
     * ```php
     * use Peterujah\Agora\User;
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\ChatToken;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * 
     * $user = new User('2882341273'); // Unique identifier for the user
     * $user->setPrivilegeExpire(time() + 3600); // Token valid for 1 hour
     * 
     * echo ChatToken::buildUserToken($client, $user);
     * ```
     */
    public static function buildUserToken(Agora $client, User $user): string
    {
        $serviceChat = (new Chat($user))
            ->addPrivilege(Privileges::CHAT_USER, $user->getPrivilegeExpiration());

        return (new AccessToken($client))
            ->addService($serviceChat)
            ->build();
    }

    /**
     * Generates an application-level Chat token.
     * 
     * This method builds a Chat token granting access to the entire application scope
     * rather than a specific user. Useful for backend services or system-level messaging.
     * 
     * @param Agora $client An instance of the Agora client containing the App ID and App Certificate.
     * 
     * @return string The generated Chat token for application-level access.
     * 
     * @example - Example:
     * 
     * ```php
     * use Peterujah\Agora\Agora;
     * use Peterujah\Agora\Builders\ChatToken;
     * 
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $client->setPrivilegeExpire(time() + 3600); // Token valid for 1 hour
     * 
     * echo ChatToken::buildAppToken($client);
     * ```
     */
    public static function buildAppToken(Agora $client): string
    {
        $serviceChat = (new Chat())
            ->addPrivilege(Privileges::CHAT_APP, $client->getExpiration());

        return (new AccessToken($client))
            ->addService($serviceChat)
            ->build();
    }
}