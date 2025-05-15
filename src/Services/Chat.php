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
namespace Peterujah\Agora\Services;

use \Peterujah\Agora\BaseService;
use \Peterujah\Agora\Util;
use \Peterujah\Agora\User;

/**
 * Class Chat
 *
 * Represents the Chat service for Agora token generation.
 * This service grants permissions related to chat operations for a specific user.
 */
class Chat extends BaseService
{
    /**
     * Chat constructor.
     *
     * Initializes the Chat service by passing the appropriate service type
     * to the parent `BaseService`. If a `User` object is provided, it sets
     * the user ID to be included in the token.
     *
     * @param User|null $user Optional user object whose ID will be used for the chat token.
     */
    public function __construct(?User $user = null)
    {
        parent::__construct(parent::CHAT_SERVICE);

        if ($user instanceof User) {
            $this->userId = $user->getAccount();
        }
    }

    /**
     * Serializes the Chat-specific service data into a binary string.
     * Includes:
     * - Parent service data (privileges and expiration)
     * - User ID as a packed string
     *
     * @return string The binary-packed string representation of the Chat service.
     */
    public function pack(): string
    {
        return parent::pack() . Util::packString($this->userId);
    }

    /**
     * Deserializes Chat-specific service data from a binary string.
     * Unpacks:
     * - Parent service data
     * - User ID
     *
     * @param string $data The binary-packed string to unpack.
     */
    public function unpack(&$data): void
    {
        parent::unpack($data);
        $this->userId = Util::unpackString($data);
    }
}