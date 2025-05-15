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

use Peterujah\Agora\Exceptions\AgoraException;

/**
 * Class User
 *
 * Represents a user in the Agora ecosystem with assigned privileges,
 * token, channel, and optional UID hashing support.
 */
class User
{
    /** @var string|int Raw user ID or UID */
    private string|int $userId = '';

    /** @var string Room ID the user belongs to */
    private string $roomId = '';

    /** @var string Agora channel name */
    private string $channelName = '';

    /** @var string Token generated for this user */
    private string $token = '';

    /** @var int RTC role (e.g., 1 for publisher, 2 for subscriber) */
    private int $role = -1;

    /** @var int General privilege expiration timestamp (seconds) */
    private int $privilegeExpireTime = 0;

    /** @var int Channel join privilege expiration timestamp */
    private int $joinChannelPrivilegeExpire = 0;

    /** @var int Audio publishing privilege expiration timestamp */
    private int $pubAudioPrivilegeExpire = 0;

    /** @var int Video publishing privilege expiration timestamp */
    private int $pubVideoPrivilegeExpire = 0;

    /** @var int Data stream publishing privilege expiration timestamp */
    private int $pubDataStreamPrivilegeExpire = 0;

    /** @var bool Whether to hash the user ID using MD5 */
    private bool $hashUserId = false;

    /**
     * Initializes a User instance with a given UID or user account.
     *
     * A UID must be a 32-bit unsigned integer in the range [0, 10000].
     * A user account (string) must be 1–255 ASCII characters.
     * 
     * @param string|int $accountOrUid The user account ID or UID.
     * @throws AgoraException if the UID or account is invalid.
     */
    public function __construct(string|int $accountOrUid)
    {
        if ($accountOrUid === 0 || $accountOrUid === '') {
            $this->userId = '';
            return;
        }

        Util::assertAccountId($accountOrUid);
        $this->userId = $accountOrUid;
    }

    /**
     * Enables or disables UID hashing (MD5).
     *
     * @param bool $hashUserId
     * 
     * @return self Return instance of class.
     * @internal
     */
    public function hash(bool $hashUserId = true): self 
    {
        $this->hashUserId = $hashUserId;
        return $this;
    }

    /**
     * Sets the token for the user.
     *
     * @param string $token
     * 
     * @return self Return instance of class.
     */
    public function setToken(string $token): self 
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Sets the general privilege expiration time.
     *
     * @param int $expires Unix timestamp or duration in seconds.
     * 
     * @return self Return instance of class.
     */
    public function setPrivilegeExpire(int $expires): self 
    {
        $this->privilegeExpireTime = $expires;
        return $this;
    }

    /**
     * Sets the expiration time for channel join privilege.
     *
     * @param int $expire
     * 
     * @return self Return instance of class.
     */
    public function setChannelPrivilegeExpire(int $expire): self
    {
        $this->joinChannelPrivilegeExpire = $expire;
        return $this;
    }

    /**
     * Sets the expiration time for audio publishing privilege.
     *
     * @param int $expire
     * 
     * @return self Return instance of class.
     */ 
    public function setAudioPrivilegeExpire(int $expire): self
    {
        $this->pubAudioPrivilegeExpire = $expire;
        return $this;
    }

    /**
     * Sets the expiration time for video publishing privilege.
     *
     * @param int $expire
     * 
     * @return self Return instance of class.
     */
    public function setVideoPrivilegeExpire(int $expire): self
    {
        $this->pubVideoPrivilegeExpire = $expire;
        return $this;
    }

    /**
     * Sets the expiration time for data stream publishing privilege.
     *
     * @param int $expire
     * 
     * @return self Return instance of class.
     */
    public function setStreamPrivilegeExpire(int $expire): self
    {
        $this->pubDataStreamPrivilegeExpire = $expire;
        return $this;
    }

    /**
     * Sets the Agora channel name for the user.
     * 
     * The string length must be less than 64 bytes. The channel name may contain the following characters:
     * - All lowercase English letters: a to z.
     * - All uppercase English letters: A to Z.
     * - All numeric characters: 0 to 9.
     * - The space character.
     * - "!", "#", "$", "%", "&", "(", ")", "+", "-", ":", ";", "<", "=", ".", ">", "?", "@", "[", "]", "^", "_", " {", "}", "|", "~", ",".
     *
     * @param string $channelName The unique channel name for the Agora RTC session in string format. 
     * 
     * @return self Return instance of class.
     */
    public function setChannel(string $channelName): self 
    {
        $this->channelName = $channelName;
        return $this;
    }

    /**
     * Sets the logical room ID for the user.
     *
     * @param string $roomId
     * 
     * @return self Return instance of class.
     */
    public function setRoom(string $roomId): self 
    {
        $this->roomId = $roomId;
        return $this;
    }

    /**
     * Sets the RTC role for the user.
     * Example roles:
     * - 1 = Publisher
     * - 2 = Subscriber
     *
     * @param int $role The role asigning to use (e.g, `Role::RTC_PUBLISHER`).
     * 
     * @return self Return instance of class.
     */
    public function setRole(int $role): self 
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Gets the generated token for this user.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Gets the general privilege expiration timestamp.
     *
     * @return int
     */
    public function getPrivilegeExpiration(): int
    {
        return $this->privilegeExpireTime;
    }

    /**
     * Gets the channel join privilege expiration timestamp.
     *
     * @return int
     */
    public function getChannelPrivilegeExpiration(): int
    {
        return $this->joinChannelPrivilegeExpire;
    }

    /**
     * Gets the audio publish privilege expiration timestamp.
     *
     * @return int
     */
    public function getAudioPrivilegeExpiration(): int
    {
        return $this->pubAudioPrivilegeExpire;
    }

    /**
     * Gets the video publish privilege expiration timestamp.
     *
     * @return int
     */
    public function getVideoPrivilegeExpiration(): int
    {
        return $this->pubVideoPrivilegeExpire;
    }

    /**
     * Gets the data stream publish privilege expiration timestamp.
     *
     * @return int
     */
    public function getStreamPrivilegeExpiration(): int
    {
        return $this->pubDataStreamPrivilegeExpire;
    }

    /**
     * Returns the user's UID.
     *
     * @return string|int
     */
    public function getUid(): int
    {
        return is_int($this->userId) ? $this->userId : 0;
    }

    /**
     * Returns the user's account. If hashing is enabled, returns the MD5 hash of the user account ID.
     *
     * @return string
     */
    public function getAccount(): string
    {
        return $this->hashUserId ? md5((string) $this->userId) : (string) $this->userId;
    }

    /**
     * Gets the room ID the user is associated with.
     *
     * @return string
     */
    public function getRoomId(): string
    {
        return $this->roomId;
    }

    /**
     * Gets the RTC role for this user.
     *
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * Gets the Agora channel name.
     *
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channelName;
    }
}