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
 * Class Rtc
 *
 * Represents the **RTC (Real-Time Communication)** service used for generating Agora access tokens
 * for joining and publishing to RTC channels.
 */
class Rtc extends BaseService
{
    /**
     * Rtc constructor.
     *
     * Initializes the RTC service with the predefined service type identifier
     * for RTC. If a User instance is provided, it sets the user ID and channel name
     * required for RTC authentication.
     *
     * @param User|null $user Optional user instance containing the UID and channel name.
     */
    public function __construct(?User $user = null)
    {
        parent::__construct(parent::RTC_SERVICE);

        if ($user instanceof User) {
            $this->userId = $user->getAccount();
            $this->channelName = $user->getChannel();
        }
    }

    /**
     * Serializes the RTC service data into a binary string.
     *
     * Packs the base service data along with RTC-specific fields: channel name and user ID.
     *
     * @return string The binary-packed string representing the RTC service.
     */
    public function pack(): string
    {
        return parent::pack()
            . Util::packString($this->channelName)
            . Util::packString($this->userId);
    }

    /**
     * Deserializes the RTC service data from a binary string.
     *
     * Extracts the RTC-specific fields (channel name and user ID)
     * after unpacking the base service data.
     *
     * @param string $data The binary-packed string to unpack.
     */
    public function unpack(&$data): void
    {
        parent::unpack($data);
        $this->channelName = Util::unpackString($data);
        $this->userId = Util::unpackString($data);
    }
}
