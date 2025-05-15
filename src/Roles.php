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

class Roles
{
    /** 
     * RTC Attendee role.
     * 
     * Typically used for basic users in communication mode.
     */
    public const RTC_ATTENDEE = 0;

    /**
     * RTC Admin role.
     * 
     * This role usually has full privileges.
     */
    public const RTC_ADMIN = 101;

    /** RtcToken and RtcToken2 Publisher role.
     * 
     * RECOMMENDED. Use this role for a voice/video call or a live broadcast, if
     * your scenario does not require authentication for
     * [Co-host](https://docs.agora.io/en/video-calling/get-started/authentication-workflow?#co-host-token-authentication).
     */
    public const RTC_PUBLISHER = 1;

    /**
     * RtcToken and RtcToken2 Subsciber role.
     * 
     * Only use this role if your scenario require authentication for
     * [Co-host](https://docs.agora.io/en/video-calling/get-started/authentication-workflow?#co-host-token-authentication).
     *
     * @note In order for this role to take effect, please contact our support team
     * to enable authentication for Hosting-in for you. Otherwise, Role_Subscriber
     * still has the same privileges as Role_Publisher.
     */
    public const RTC_SUBSCRIBER = 2;

    /**
     * RTM User role.
     * 
     * Standard role for real-time messaging token usage.
     */
    public const RTM_USER = 1;
}