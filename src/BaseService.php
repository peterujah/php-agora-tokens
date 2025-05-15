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

use \Peterujah\Agora\Util;
use \Peterujah\Agora\User;

class BaseService
{
    /**
     * Service type constant for Real-Time Communication (RTC).
     * 
     * @var int RTC_SERVICE
     */
    public const RTC_SERVICE = 1;

    /**
     * Service type constant for Real-Time Messaging (RTM).
     * 
     * @var int RTM_SERVICE
     */
    public const RTM_SERVICE = 2;

    /**
     * Service type constant for First-Packet Acceleration (FPA).
     * 
     * @var int FPA_SERVICE
     */
    public const FPA_SERVICE = 4;

    /**
     * Service type constant for Chat services.
     * 
     * @var int CHAT_SERVICE
     */
    public const CHAT_SERVICE = 5;

    /**
     * Service type constant for aPaaS (Agora Platform as a Service).
     * 
     * @var int APAAS_SERVICE
     */
    public const APAAS_SERVICE = 7;

    /**
     * Name of the Agora channel associated with the user.
     * 
     * @var string $channelName
     */
    public string $channelName = '';

    /**
     * Identifier of the room associated with the user.
     * 
     * @var string $roomId
     */
    public string $roomId = '';

    /**
     * User ID of the participant in the Agora service.
     * 
     * @var string|int $userId
     */
    public string|int $userId = '';

    /**
     * Role of the user within the service (e.g., publisher, subscriber).
     * 
     * @var int $role
     */
    public int $role = -1;

    /**
     * Type of the service being used (e.g., RTC, RTM).
     * 
     * @var int $type
     */
    public int $type = 0;

    /**
     * List of privileges/permissions assigned to the user.
     * 
     * @var array<int,int> $privileges
     */
    public array $privileges = [];

    /**
     * Initializes the service with a specific type.
     * 
     * @param int $type The service type (use one of the defined constants).
     */
    public function __construct(int $type)
    {
        $this->type = $type;
    }

    /**
     * Sets user-related information (room ID, user ID, role, and channel) from a User object.
     * 
     * @param User $user The user object containing necessary details.
     * 
     * @return self Returns the current instance for method chaining.
     * 
     * @introduced 
     */
    public function setUser(User $user): self
    {
        $uid = $user->getUid();
        $this->roomId = $user->getRoomId();
        $this->userId = ($uid > 0) ? $uid : $user->getAccount();
        $this->role = $user->getRole();
        $this->channelName = $user->getChannel();

        return $this;
    }

    /**
     * Adds a privilege and its expiration time to the service.
     * 
     * @param int $privilege The privilege type (e.g, `Privileges::*`).
     * @param int $expire The expiration timestamp for the privilege.
     * 
     * @return self Returns the current instance for method chaining.
     */
    public function addPrivilege(int $privilege, int $expire): self 
    {
        $this->privileges[$privilege] = $expire;
        return $this;
    }

    /**
     * Gets the type of the service.
     * 
     * @return int The service type.
     */
    public function getServiceType(): int
    {
        return $this->type;
    }

    /**
     * Gets a human-readable name of the service based on its type.
     * 
     * @return string|null The service name or null if the type is unknown.
     * @introduced 
     */
    public function getServiceName(): ?string
    {
        switch($this->type){
            case self::RTC_SERVICE:
                return 'RTC SERVICE TYPE';
            case self::RTM_SERVICE:
                return 'RTC SERVICE TYPE';
            case self::FPA_SERVICE:
                return 'FPA SERVICE TYPE';
            case self::CHAT_SERVICE:
                return 'CHAT SERVICE TYPE';
            case self::APAAS_SERVICE:
                return 'APAAS SERVICE TYPE';
            default: 
                return null;
        }
    }

    /**
     * Packs the service type and associated privileges into a binary string.
     * 
     * @return string The packed binary string.
     */
    public function pack(): string
    {
        return Util::packUint16($this->type) . Util::packMapUint32($this->privileges);
    }

    /**
     * Unpacks privilege data from a binary input and assigns it to the service.
     * 
     * @param string $data Binary string containing the packed privileges.
     */
    public function unpack(string &$data): void
    {
        $this->privileges = Util::unpackMapUint32($data);
    }
}