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
namespace Peterujah\Agora;

use \Peterujah\Agora\Exceptions\AgoraException;

/**
 * Class Agora
 *
 * Core configuration class for managing Agora credentials and global app context.
 * This class prepares data needed to generate access tokens for Agora services.
 */
class Agora
{
    /** 
     * Agora App ID.
     * 
     * @var string $appId
     */
    private string $appId = '';

    /** 
     * Agora App Certificate.
     * 
     * @var string $appCertificate
     */
    private string $appCertificate = '';

    /** 
     * Target channel name for RTC/RTM services.
     * 
     * @var string $channelName
     */
    private string $channelName = '';

    /**
     * User account or UID used in token generation.
     * 
     * @var string|int $account
     */
    private string|int $account = '';

    /** 
     * Token expiration duration in seconds.
     * 
     * @var int $expireTime
     */
    private int $expireTime = 0;

    /** 
     * User role for RTC tokens (e.g., publisher, subscriber).
     * 
     * @var int $role
     */
    private int $role = -1;

    /**
     * Agora constructor.
     *
     * Initializes the Agora service configuration with the required credentials.
     * Throws an exception if either the App ID or App Certificate is missing.
     *
     * @param string $appId Agora App ID from the Agora developer console.
     * @param string $appCertificate Agora App Certificate for token signing.
     * 
     * @throws AgoraException If either credential is not provided.
     */
    public function __construct(string $appId, string $appCertificate)
    {
        if ($appId === '' || $appCertificate === '') {
            throw new AgoraException("Need to set environment variable AGORA_APP_ID and AGORA_APP_CERTIFICATE");
        }

        $this->appId = $appId;
        $this->appCertificate = $appCertificate;
    }

    /**
     * Sets the target channel name for the token.
     * 
     * The string length must be less than 64 bytes. The channel name may contain the following characters:
     * - All lowercase English letters: a to z.
     * - All uppercase English letters: A to Z.
     * - All numeric characters: 0 to 9.
     * - The space character.
     * - "!", "#", "$", "%", "&", "(", ")", "+", "-", ":", ";", "<", "=", ".", ">", "?", "@", "[", "]", "^", "_", " {", "}", "|", "~", ",".
     *
     * @param string $channelName The unique channel name for the Agora RTC session.
     * 
     * @return self Return instance of Agora client.
     * @internal
     */
    public function setChannel(string $channelName): self 
    {
        $this->channelName = $channelName;
        return $this;
    }

    /**
     * Sets the account (UID or string ID) used in the token.
     * 
     * A 32-bit unsigned integer with a value range from 1 to (2^32 - 1). It must be unique. 
     * Set uid as 0, if you do not want to authenticate the user ID, that is, any uid from the app client can join the channel.
     *
     * @param string|int $account The user account or UID.
     * 
     * @return self Return instance of Agora client.
     * @throws AgoraException
     * @internal
     */
    public function setIdentifier(string|int $account): self 
    {
        if($account === 0 || $account === ''){
            $this->account = '';
            return $this;
        }

        Util::assertAccountId($account);
        $this->account = $account;
        return $this;
    }

    /**
     * Sets the user role for RTC token generation.
     * Common values:
     * - 1 = Publisher
     * - 2 = Subscriber
     *
     * @param int $role The user role.
     * 
     * @return self Return instance of Agora client.
     * @internal
     */
    public function setRole(int $role): self 
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Sets Agora token expiration time (in seconds).
     *
     * @param int $expires The app token expiration.
     * 
     * @return self Return instance of Agora client.
     */
    public function setExpiration(int $expires): self 
    {
        $this->expireTime = $expires;
        return $this;
    }

    /**
     * Gets the current user account or UID identifier.
     *
     * @return string|int Return the user account or UID.
     */
    public function getIdentifier(): string|int
    {
        return $this->account;
    }

    /**
     * Gets the Agora App ID.
     *
     * @return string Return application ID.
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * Gets the current user role.
     *
     * @return int Return role.
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * Gets the Agora App Certificate.
     *
     * @return string Return application certificate.
     */
    public function getAppCertificate(): string
    {
        return $this->appCertificate;
    }

    /**
     * Gets the currently set channel name.
     *
     * @return string Return channel.
     */
    public function getChannel(): string
    {
        return $this->channelName;
    }

    /**
     * Gets the expiration time (in seconds).
     *
     * @return int Return expiration.
     */
    public function getExpiration(): int
    {
        return $this->expireTime;
    }
}