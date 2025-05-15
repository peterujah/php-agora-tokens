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
 * Class Apaas
 *
 * Represents the APAAS (App as a Service) service in the Agora token system.
 * This service allows granting privileges to users for joining APAAS rooms with specified roles.
 */
class Apaas extends BaseService
{
    /**
     * Apaas constructor.
     *
     * Initializes the service as APAAS by passing the appropriate service type
     * to the BaseService constructor. If a user object is provided, it sets
     * the user ID, room ID, and role accordingly.
     *
     * @param User|null $user Optional user object containing room ID, user ID, and role.
     */
    public function __construct(?User $user = null)
    {
        parent::__construct(parent::APAAS_SERVICE);

        if ($user instanceof User) {
            $this->setUser($user);
        }
    }

    /**
     * Serializes the APAAS-specific service data into a binary string.
     * The resulting string includes:
     * - Packed base service data (privileges and expiration)
     * - Room ID
     * - User ID
     * - Role (as 16-bit integer)
     *
     * @return string The binary-packed string representation of the service data.
     */
    public function pack(): string
    {
        return parent::pack()
            . Util::packString($this->roomId)
            . Util::packString($this->userId)
            . Util::packInt16($this->role);
    }

    /**
     * Deserializes APAAS-specific service data from a binary string.
     * It extracts:
     * - Room ID
     * - User ID
     * - Role
     *
     * The unpacking process assumes the data follows the same structure
     * used in the `pack()` method.
     *
     * @param string $data The binary string containing the serialized data.
     */
    public function unpack(&$data): void
    {
        parent::unpack($data);
        $this->roomId = Util::unpackString($data);
        $this->userId = Util::unpackString($data);
        $this->role   = Util::unpackInt16($data);
    }
}