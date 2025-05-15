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
namespace Peterujah\Agora\Exceptions;

use \Exception;
use \Throwable;

class AgoraException extends Exception
{
    /**
     * Constructs a new AgoraException.
     *
     * @param string $message Error message.
     * @param int $code Error code (optional).
     * @param \Throwable|null $previous Previous exception for chaining (optional).
     */
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}