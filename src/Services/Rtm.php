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
 * Class Rtm
 *
 * Represents the **RTM (Real-Time Messaging)** service used for generating
 * Agora access tokens that grant login privileges to the RTM system.
 */
class Rtm extends BaseService
{
    /**
     * Rtm constructor.
     *
     * Initializes the RTM service with its corresponding service type identifier.
     * If a User instance is provided, it sets the user ID used for RTM login.
     *
     * @param User|null $user Optional user instance containing the UID.
     */
    public function __construct(?User $user = null)
    {
        parent::__construct(parent::RTM_SERVICE);

        if ($user instanceof User) {
            $this->userId = $user->getAccount();
        }
    }

    /**
     * Serializes the RTM service data into a binary string.
     *
     * Includes the base service data and RTM-specific field: user ID.
     *
     * @return string The binary-packed string representing the RTM service.
     */
    public function pack(): string
    {
        return parent::pack()
            . Util::packString($this->userId);
    }

    /**
     * Deserializes the RTM service data from a binary string.
     *
     * Extracts the RTM-specific user ID after unpacking the base service data.
     *
     * @param string $data The binary-packed string to unpack.
     */
    public function unpack(&$data): void
    {
        parent::unpack($data);
        $this->userId = Util::unpackString($data);
    }
}