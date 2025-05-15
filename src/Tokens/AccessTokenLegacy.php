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
namespace Peterujah\Agora\Tokens;

use \Peterujah\Agora\Message;
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\User;
use \Peterujah\Agora\Util;
use \Peterujah\Agora\Exceptions\AgoraException;

/**
 * Class Access Token Legacy previously AccessToken
 *
 * Represents a secure token structure for Agora's version 006 authentication system.
 * This token supports multiple services (RTC, RTM, FPA, etc.) with packed privileges.
 */
class AccessTokenLegacy
{
    /**
     * The App ID issued by Agora Console.
     * 
     * @var string $appId
     */
    public string $appId = '';

    /**
     * The App Certificate used for signing the token.
     * 
     * @var string $appCertificate
     */
    public string $appCertificate = '';

    /**
     * The name of the Agora channel the user joins.
     * 
     * @var string $channelName
     */
    public string $channelName = '';

    /**
     * The UID or account string of the user.
     * 
     * @var string|int $uid
     */
    public string|int $uid = 0;

    /**
     * The Message object holding salt, timestamp, and privileges.
     * 
     * @var Message|null $message
     */
    public ?Message $message = null;


    /**
     * Constructor. Optionally sets values from an Agora client.
     *
     * @param Agora|null $client Optional Agora instance to initialize properties.
     */
    public function __construct(?Agora $client = null)
    {
        if($client instanceof Agora){
            $this->setClient($client);
        }
        
        $this->message = new Message();
    }

    /**
     * Sets the message object containing privileges and timestamps.
     *
     * @param Message $message The message object to assign.
     * 
     * @return self Return instance of AccessToken.
     */
    public function setMessage(Message $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Assigns App ID, Certificate, channel, and UID from an Agora client instance.
     *
     * @param Agora $client The Agora client instance.
     * 
     * @return self Return instance of AccessToken.
     */
    protected function setClient(Agora $client): self
    {
        $this->appId = $client->getAppId();
        $this->appCertificate = $client->getAppCertificate();
        $this->channelName = $client->getChannel();
        $this->uid = $client->getIdentifier();

        return $this;
    }

    /**
     * Sets the user account or UID for the token. Converts 0 to an empty string.
     *
     * @param string|int $uid The UID or account string.
     */
    public function setUid(string|int $uid): void 
    {
        if ($uid === 0) {
            $this->uid = "";
            return;
        }

        $this->uid = $uid . '';
    }

    /**
     * Initializes a new token using Agora and optional user information.
     *
     * @param Agora $client The Agora instance.
     * @param User|null $user Optional user instance for additional context.
     * 
     * @return AccessTokenLegacy Return a new instance of AccessToken.
     */
    public static function init(Agora $client, ?User $user = null): AccessTokenLegacy
    {
        if($user instanceof User){
            $client->setChannel($user->getChannel());
            $client->setRole($user->getRole());
            $client->setIdentifier($user->getUid());
        }

        self::isEmpty("appID", $client->getAppId());
        self::isEmpty("appCertificate", $client->getAppCertificate());
        self::isEmpty("channelName", $client->getChannel());

        return (new AccessTokenLegacy($client))->setMessage(new Message());
    }

    /**
     * Initializes an AccessToken instance by decoding an existing token string.
     *
     * @param Agora $client The Agora instance.
     * @param User $user The user providing token and identity info.
     * 
     * @return AccessTokenLegacy Return a new instance of AccessToken.
     */
    public static function initWithToken(
        Agora $client,
        User $user,
    ) : AccessTokenLegacy
    {
        $client->setChannel($user->getChannel());
        $accessToken = new AccessTokenLegacy($client);
        $accessToken->extract(
            $user->getToken(), 
            $client->getAppCertificate(), 
            $user->getChannel(), 
            $user->getUid()
        );

        return $accessToken;
    }

    /**
     * Adds a privilege and its expiry timestamp to the token message.
     *
     * @param int $key The privilege key (e.g, `Privileges::*`).
     * @param int $expireTimestamp The privilege expiry timestamp (UTC seconds).
     * 
     * @return self Return instance of AccessToken.
     */
    public function addPrivilege(int $key, int $expireTimestamp): self
    {
        $this->message->privileges[$key] = $expireTimestamp;
        return $this;
    }

    /**
     * Parses the token and extracts data such as App ID, signature, and privileges.
     *
     * @param string $token The Agora token string.
     * @param string $appCertificate The certificate for verification.
     * @param string $channelName The target channel name.
     * @param string|int $uid The UID associated with the token.
     * 
     * @return bool True if extraction is successful.
     * @throws AgoraException
     */
    public function extract(
        string $token, 
        string $appCertificate, 
        string $channelName, 
        string|int $uid
    ): bool
    {
        $ver_len = 3;
        $appid_len = 32;
        $version = substr($token, 0, $ver_len);

        if ($version !== "006") {
            throw new AgoraException("invalid version {$version}");
        }

        self::isEmpty("token", $token);
        self::isEmpty("appCertificate", $appCertificate);
        self::isEmpty("channelName", $channelName);

        $appid = substr($token, $ver_len, $appid_len);
        $content = (base64_decode(substr($token, $ver_len + $appid_len, strlen($token) - ($ver_len + $appid_len))));

        $pos = 0;
        $len = unpack("v", $content . substr($pos, 2))[1];
        $pos += 2;
        $sig = substr($content, $pos, $len);
        $pos += $len;
        $crc_channel = unpack("V", substr($content, $pos, 4))[1];
        $pos += 4;
        $crc_uid = unpack("V", substr($content, $pos, 4))[1];
        $pos += 4;
        $msgLen = unpack("v", substr($content, $pos, 2))[1];
        $pos += 2;
        $msg = substr($content, $pos, $msgLen);

        $this->appId = $appid;
        // $message = new Message();
        // Not implemented by agora
        // $message->unpackContent($msg);
        $this->message = new Message();

        //non reversible values
        $this->appCertificate = $appCertificate;
        $this->channelName = $channelName;
        $this->setUid($uid);
        return true;
    }

    /**
     * Builds a new token string based on the current message and identity info.
     *
     * @return string Return the generated Agora token string.
     */
    public function build(): string
    {
        $msg = $this->message->packContent();
        $val = array_merge(
            unpack("C*", $this->appId), 
            unpack("C*", $this->channelName), 
            unpack("C*", $this->uid), 
            $msg
        );

        $sig = hash_hmac('sha256', implode(array_map("chr", $val)), $this->appCertificate, true);
        $crc_channel_name = crc32($this->channelName) & 0xffffffff;
        $crc_uid = crc32($this->uid) & 0xffffffff;

        $content = array_merge(
            unpack("C*", Util::packString2Byte($sig)), 
            unpack("C*", pack("V", $crc_channel_name)), 
            unpack("C*", pack("V", $crc_uid)), 
            unpack("C*", pack("v", count($msg))), 
            $msg
        );

        $version = "006";
        $ret = $version . $this->appId . base64_encode(implode(array_map("chr", $content)));

        return $ret;
    }

    /**
     * Validates that a string is non-empty.
     *
     * @param string $name The name of the field.
     * @param string $str The string to validate.
     * 
     * @return bool True if valid, otherwise throws an exception.
     * @throws AgoraException
     */
    private static function isEmpty(string $name, string $str): bool
    {
        if (trim($str) !== '') {
            return true;
        }

        throw new AgoraException("{$name} check failed, should be a non-empty string");
    }
}