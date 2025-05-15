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
use \Peterujah\Agora\Services\Fpa;
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\Privileges;

class FpaToken
{
    /**
     * Build an FPA (Flexible Public Authorization) RTC token with login privilege.
     *
     * @param Agora $client Instance of Agora client initialized with App ID and Certificate.
     *                      The expiration will be set to 24 hours (86400 seconds) from now.
     *
     * @return string The generated FPA RTC token.
     *
     * @example - Example:
     * ```php
     * use Peterujah\Agora\Agora;
     *
     * $client = new Agora('APP_ID', 'APP_CERT');
     * $token = FpaToken::buildToken($client);
     * ```
     */
    public static function buildToken(Agora $client): string
    {
        return (new AccessToken($client->setExpiration(24 * 3600)))
            ->addService((new Fpa())->addPrivilege(Privileges::FPA_LOGIN, 0))
            ->build();
    }
}