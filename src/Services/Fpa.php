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

/**
 * Class Fpa
 *
 * Represents the **FPA (Flexible Public Authorization)** service for Agora token generation.
 * This service grants login access for clients using the Agora FPA SDK.
 */
class Fpa extends BaseService
{
    /**
     * Fpa constructor.
     *
     * Initializes the FPA service with the predefined service type identifier
     * for FPA. No user-specific data is needed for this service.
     */
    public function __construct()
    {
        parent::__construct(parent::FPA_SERVICE);
    }

    /**
     * Serializes the FPA service data into a binary string.
     *
     * Since the FPA service doesn't require additional custom fields,
     * this method simply returns the packed data from the base service.
     *
     * @return string The binary-packed string representation of the FPA service.
     */
    public function pack(): string
    {
        return parent::pack();
    }

    /**
     * Deserializes FPA service data from a binary string.
     *
     * No additional fields are unpacked beyond what is handled in the base class.
     *
     * @param string $data The binary-packed string to unpack.
     */
    public function unpack(&$data): void
    {
        parent::unpack($data);
    }
}