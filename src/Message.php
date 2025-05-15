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

use \DateTime;
use \DateTimeZone;

class Message
{
    /**
     * A random salt used to for message content.
     * 
     * @var int $salt
     */
    public int $salt = 0;

    /**
     * The UTC timestamp representing message expiry time.
     * 
     * @var int $ts
     */
    public int $ts = 0;

    /**
     * An associative array of privileges where the key is the privilege type and the value is its expiry time.
     * 
     * @var array $privileges
     */
    public array $privileges = [];

    /**
     * Constructs a new Message instance.
     * 
     * Create a random salt and a timestamp 24 hours from now (UTC).
     */
    public function __construct()
    {
        $this->salt = rand(0, 100000);
        $this->ts = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp() + 24 * 3600;
        $this->privileges = [];
    }

    /**
     * Packs the message content (salt, timestamp, and privileges) into a binary-compatible array of bytes.
     * 
     * @return array The packed content as an array of unsigned bytes.
     */
    public function packContent(): array
    {
        $buffer = unpack("C*", pack("V", $this->salt));
        $buffer = array_merge($buffer, unpack("C*", pack("V", $this->ts)));
        $buffer = array_merge($buffer, unpack("C*", pack("v", sizeof($this->privileges))));
        
        foreach ($this->privileges as $key => $value) {
            $buffer = array_merge($buffer, unpack("C*", pack("v", $key)));
            $buffer = array_merge($buffer, unpack("C*", pack("V", $value)));
        }

        return $buffer;
    }

    /**
     * Placeholder for unpacking message content from a binary string.
     * Currently unimplemented by Agora.
     * 
     * @param string $message The packed binary message string to be unpacked.
     */
    public function unpackContent(string $message) {}
}