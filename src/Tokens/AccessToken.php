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

use \Peterujah\Agora\BaseService;
use \Peterujah\Agora\Agora;
use \Peterujah\Agora\Util;
use \Peterujah\Agora\Services\Rtc;
use \Peterujah\Agora\Services\Rtm;
use \Peterujah\Agora\Services\Fpa;
use \Peterujah\Agora\Services\Chat;
use \Peterujah\Agora\Services\Apaas;
use \Peterujah\Agora\Exceptions\AgoraException;

/**
 * Class AccessToken previously AccessToken2
 *
 * Represents a secure token structure for Agora's version 007 authentication system.
 * This token supports multiple services (RTC, RTM, FPA, etc.) with packed privileges.
 */
class AccessToken
{
    /**
     * The version identifier of the token format.
     * 
     * @var string VERSION
     */
    public const VERSION = "007";

    /**
     * Length of the version string used for token parsing and validation.
     * 
     * @var int VERSION_LENGTH
     */
    private const VERSION_LENGTH = 3;

    /**
     * The App Certificate used for signing the token. Must be a 32-character hexadecimal string.
     * 
     * @var string $appCert
     */
    public string $appCert = '';

    /**
     * The App ID used for identifying the Agora project. Must be a 32-character hexadecimal string.
     * 
     * @var string $appId
     */
    public string $appId = '';

    /**
     * Token expiration time in seconds, relative to issue timestamp.
     * 
     * @var int $expire
     */
    public int $expire = 900;

    /**
     * The UNIX timestamp (UTC) when the token is issued.
     * 
     * @var int $issueTs
     */
    public int $issueTs = 0;

    /**
     * A random salt value used for signature randomization.
     * 
     * @var int $salt
     */
    public int $salt = 0;

    /**
     * Array of services attached to the token, keyed by service type constant.
     * Each service defines its own privileges and structure.
     *
     * @var BaseService[]
     */
    public array $services = [];


    /**
     * Constructor.
     * Initializes token metadata and sets fields from an Agora client instance if provided.
     *
     * @param Agora|null $client Optional Agora client instance for initializing app ID, cert, and expiration.
     */
    public function __construct(?Agora $client = null)
    {
        if($client instanceof Agora){
            $this->setClient($client);
        }

        $this->issueTs = time();
        $this->salt = rand(1, 99999999);
    }

    /**
     * Sets core token fields using values from an Agora client instance.
     *
     * @param Agora $client The Agora client to initialize from.
     * 
     * @return self Return instance of AccessToken.
     */
    protected function setClient(Agora $client): self
    {
        $this->appId = $client->getAppId();
        $this->appCert = $client->getAppCertificate();
        $this->expire = $client->getExpiration();

        return $this;
    }

    /**
     * Adds a service (RTC, RTM, etc.) to the token.
     * Services define specific privileges and their expiration timestamps.
     *
     * @param BaseService $service The service instance to add.
     * 
     * @return self Return instance of AccessToken.
     */
    public function addService(BaseService $service): self
    {
        $this->services[$service->getServiceType()] = $service;
        return $this;
    }

    /**
     * Builds and returns the final Agora token string.
     * This includes:
     * - Packing all metadata and services
     * - Signing the data with a hashed signature
     * - Compressing and encoding the token for transmission
     *
     * @param bool $throw If true, throws exceptions for invalid UUIDs. If false, returns an empty string on failure.
     * 
     * @return string The encoded Agora token string.
     * @throws RuntimeException If appId or appCert are invalid UUIDs and $throw is true.
     */
    public function build(bool $throw = true): string
    {
        if (!$throw && (!self::isUUid($this->appId) || !self::isUUid($this->appCert))) {
            return "";
        }

        if ($throw && !self::isUUid($this->appId)){
            throw new AgoraException(sprintf('Application Id: %s is not a valid UUID', $this->appId));
        }

        if ($throw && !self::isUUid($this->appCert)){
            throw new AgoraException(sprintf('Application Cert: %s is not a valid UUID', $this->appCert));
        }

        $signing = $this->getSign();
        $data = Util::packString($this->appId) . Util::packUint32($this->issueTs) . Util::packUint32($this->expire)
            . Util::packUint32($this->salt) . Util::packUint16(count($this->services));

        ksort($this->services);
        foreach ($this->services as $key => $service) {
            $data .= $service->pack();
        }

        $signature = hash_hmac("sha256", $data, $signing, true);

        return self::getVersion() . base64_encode(
            zlib_encode(Util::packString($signature) . $data, ZLIB_ENCODING_DEFLATE)
        );
    }

    /**
     * Generate the final signing key used for token signature.
     * This is a nested HMAC hash based on certificate, timestamp, and salt.
     *
     * @return string The signing key.
     */
    public function getSign(): string
    {
        $hh = hash_hmac("sha256", $this->appCert, Util::packUint32($this->issueTs), true);
        return hash_hmac("sha256", $hh, Util::packUint32($this->salt), true);
    }

    /**
     * Returns the static version string used by this token format.
     *
     * @return string The token version (e.g., "007").
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Validates whether the given string is a valid 32-character hexadecimal UUID.
     *
     * @param string $str The string to validate.
     * 
     * @return bool True if the string is a valid UUID.
     */
    public static function isUUid(string $str): bool
    {
        if (strlen($str) != 32) {
            return false;
        }

        return ctype_xdigit($str);
    }

    /**
     * Parses an existing Agora token string and extracts metadata and service privileges.
     *
     * @param string $token The token string to parse.
     * 
     * @return bool True if parsing succeeds and services are loaded; false otherwise.
     * @throws AgoraException
     */
    public function parse(string $token): bool
    {
        $version = substr($token, 0, self::VERSION_LENGTH);

        if (substr($token, 0, self::VERSION_LENGTH) != self::getVersion()) {
            throw new AgoraException("Invalid token version {$version}");
        }

        $data = zlib_decode(base64_decode(substr($token, self::VERSION_LENGTH)));
        // Unused by agora
        // $signature = Util::unpackString($data);
        $this->appId = Util::unpackString($data);
        $this->issueTs = Util::unpackUint32($data);
        $this->expire = Util::unpackUint32($data);
        $this->salt = Util::unpackUint32($data);
        $serviceNum = Util::unpackUint16($data);

        $servicesObj = [
            BaseService::RTC_SERVICE => new Rtc(),
            BaseService::RTM_SERVICE => new Rtm(),
            BaseService::FPA_SERVICE => new Fpa(),
            BaseService::CHAT_SERVICE => new Chat(),
            BaseService::APAAS_SERVICE => new Apaas(),
        ];

        for ($i = 0; $i < $serviceNum; $i++) {
            $serviceTye = Util::unpackUint16($data);
            $service = $servicesObj[$serviceTye];
            if ($service == null) {
                return false;
            }
            $service->unpack($data);
            $this->services[$serviceTye] = $service;
        }
        return true;
    }
}