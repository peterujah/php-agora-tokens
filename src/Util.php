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

class Util
{
    /**
     * Asserts that two strings are equal and outputs the result with debug info.
     *
     * @param string $expected The expected string.
     * @param string $actual   The actual string to compare.
     */
    public static function assertEqual(string $expected, string $actual): void
    {
        $debug = debug_backtrace();
        $info = sprintf(
            "\n- File:%s, Func:%s, Line:%d", 
            basename($debug[1]["file"]), 
            $debug[1]["function"],
            $debug[1]["line"]
        );

        if ($expected != $actual) {
            echo $info . "\n  Assert failed" . "\n    Expected :" . $expected . "\n    Actual   :" . $actual;
            return;
        }

        echo $info . "\n  Assert ok";
    }

    /**
     * Validates a user ID as either a UID (integer) or a user account (string).
     *
     * - UID must be an integer between [1, 4294967295].
     * - User account must be ASCII and length between [1, 255].
     *
     * @param string|int $userId The user ID to validate.
     * @throws AgoraException If validation fails.
     */
    public static function assertAccountId(string|int $userId): void 
    {
        if (is_int($userId)) {
            if (!self::isValidUid($userId)) {
                throw new AgoraException(
                    'The UID value must be an integer in the range [1,4294967295].'
                );
            }
        } elseif (is_string($userId)) {
            $length = strlen($userId);

            if ($length < 1 || $length > 255) {
                throw new AgoraException(
                    ($length > 255)
                        ? 'The User account length exceeds the maximum allowed [255] characters.'
                        : 'The User account length must be at least [1] character.'
                );
            }

            if (!mb_check_encoding($userId, 'ASCII')) {
                throw new AgoraException(
                    'The User account must contain only ASCII characters.'
                );
            }
        } else {
            throw new AgoraException(
                'The userId must be either an integer or a string.'
            );
        }
    }

    /**
     * Validates if the given value is a valid Agora UID.
     *
     * A valid UID is a 32-bit unsigned integer in the range [1, 4294967295].
     *
     * @param int $value The value to validate.
     * 
     * @return bool True if valid, false otherwise.
     */
    public static function isValidUid(int $value): bool
    {
        return $value >= 1 && $value <= 4294967295;
    }

    /**
     * Generates a random valid Agora UID.
     *
     * @return int A random UID in the valid range.
     */
    public static function generateUid(): int 
    {
        return random_int(1, 4294967295);
    }   

    /**
     * Packs a 16-bit unsigned integer into binary string (little-endian).
     *
     * @param mixed $x The value to pack.
     * 
     * @return string The packed binary string.
     */
    public static function packUint16(mixed $x): string
    {
        return pack("v", $x);
    }

    /**
     * Unpacks a 16-bit unsigned integer from binary string.
     *
     * @param string $data The binary data (passed by reference).
     * 
     * @return mixed The unpacked integer value.
     */
    public static function unpackUint16(string &$data): mixed
    {
        $up = unpack("v", substr($data, 0, 2));
        $data = substr($data, 2);
        return $up[1];
    }

    /**
     * Packs a 32-bit unsigned integer into binary string (little-endian).
     *
     * @param mixed $x The value to pack.
     * 
     * @return string The packed binary string.
     */
    public static function packUint32(mixed $x): string
    {
        return pack("V", $x);
    }

    /**
     * Unpacks a 32-bit unsigned integer from binary string.
     *
     * @param string $data The binary data (passed by reference).
     * 
     * @return mixed The unpacked integer value.
     */
    public static function unpackUint32(string &$data): mixed
    {
        $up = unpack("V", substr($data, 0, 4));
        $data = substr($data, 4);
        return $up[1];
    }

    /**
     * Packs a 16-bit signed integer into binary string.
     *
     * @param mixed $x The value to pack.
     * 
     * @return string The packed binary string.
     */
    public static function packInt16(mixed $x): string
    {
        return pack("s", $x);
    }

    /**
     * Unpacks a 16-bit signed integer from binary string.
     *
     * @param string $data The binary data (passed by reference).
     * 
     * @return mixed The unpacked integer value.
     */
    public static function unpackInt16(string &$data): mixed
    {
        $up = unpack("s", substr($data, 0, 2));
        $data = substr($data, 2);

        return $up[1];
    }

    /**
     * Packs a string with a 2-byte length prefix (unsigned).
     *
     * @param string $value The string to pack.
     * 
     * @return string The packed string.
     */
    public static function packString2Byte(string $value): string
    {
        return pack("v", strlen($value)) . $value;
    }

    /**
     * Packs a string with its length as a 2-byte prefix.
     *
     * @param string $str The string to pack.
     * 
     * @return string The packed string.
     */
    public static function packString(string $str): string
    {
        return self::packUint16(strlen($str)) . $str;
    }

    /**
     * Unpacks a string with a 2-byte length prefix from binary data.
     *
     * @param string $data The binary data (passed by reference).
     * 
     * @return string The unpacked string.
     */
    public static function unpackString(string &$data): string
    {
        $len = self::unpackUint16($data);
        $up = unpack("C*", substr($data, 0, $len));
        $data = substr($data, $len);
        return implode(array_map("chr", $up));
    }

    /**
     * Packs a key-value map of uint16 => uint32 pairs.
     *
     * @param array $arr Associative array of values.
     * 
     * @return string The packed map.
     */
    public static function packMapUint32(array $arr): string
    {
        ksort($arr);
        $kv = "";
        foreach ($arr as $key => $val) {
            $kv .= self::packUint16($key) . self::packUint32($val);
        }

        return self::packUint16(count($arr)) . $kv;
    }

    /**
     * Unpacks a key-value map of uint16 => uint32 pairs from binary data.
     *
     * @param string $data The binary data (passed by reference).
     * 
     * @return array The unpacked associative array.
     */
    public static function unpackMapUint32(string &$data): array
    {
        $len = self::unpackUint16($data);
        $arr = [];
        for ($i = 0; $i < $len; $i++) {
            $arr[self::unpackUint16($data)] = self::unpackUint32($data);
        }
        return $arr;
    }
}