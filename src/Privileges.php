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

/**
 * Class Privileges
 *
 * Defines privilege constants used by various Agora services in token generation.
 * These constants represent specific permissions granted to users or applications.
 */
class Privileges
{
    /**
     * Allows a user to join an APAAS room.
     * 
     * @var int APAAS_ROOM_USER
     */
    public const APAAS_ROOM_USER = 1;

    /**
     * Grants general user-level access in APAAS.
     * 
     * @var int APAAS_USER
     */
    public const APAAS_USER = 2;

    /**
     * Grants application-wide privileges in APAAS.
     * 
     * @var int APAAS_APP
     */
    public const APAAS_APP = 3;

    /**
     * Grants a user permission to log in and use chat services.
     * 
     * @var int CHAT_USER
     */
    public const CHAT_USER = 1;

    /**
     * Grants application-level chat permissions, used for server-side operations.
     * 
     * @var int CHAT_APP
     */
    public const CHAT_APP = 2;

    /**
     * Grants permission to initiate an FPA session.
     * 
     * @var int FPA_LOGIN
     */
    public const FPA_LOGIN = 1;

    /**
     * Grants permission to join an RTC channel.
     * 
     * @var int RTC_JOIN_CHANNEL
     */
    public const RTC_JOIN_CHANNEL = 1;

    /**
     * Grants permission to publish audio streams to the RTC channel.
     * 
     * @var int RTC_PUBLISH_AUDIO_STREAM
     */
    public const RTC_PUBLISH_AUDIO_STREAM = 2;

    /**
     * Grants permission to publish video streams to the RTC channel.
     * 
     * @var int RTC_PUBLISH_VIDEO_STREAM
     */
    public const RTC_PUBLISH_VIDEO_STREAM = 3;

    /**
     * Grants permission to publish data streams (e.g., SEI messages, whiteboards) in RTC.
     * 
     * @var int RTC_PUBLISH_DATA_STREAM
     */
    public const RTC_PUBLISH_DATA_STREAM = 4;

    /**
     * Grants permission to log in to the RTM (messaging) service.
     * 
     * @var int RTM_LOGIN
     */
    public const RTM_LOGIN = 1;

    /**
     * List of available AccessToken privilege constants.
     * 
     * Maps privilege names to their corresponding integer identifiers.
     * 
     * @var array<string,int> PERMISSIONS
     */
    public const PERMISSIONS = [
        "kJoinChannel"          => 1,
        "kPublishAudioStream"   => 2,
        "kPublishVideoStream"   => 3,
        "kPublishDataStream"    => 4,
        "kRtmLogin"             => 1000,
    ];
}