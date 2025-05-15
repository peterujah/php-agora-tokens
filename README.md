# PHP Agora Token Builder

This package is a Composer-friendly rewrite of [Agora's PHP token generator](https://github.com/AgoraIO/Tools/tree/master/DynamicKey/AgoraDynamicKey/php). It provides a simple and modern way to generate **Agora Access Tokens (v2.1.0)** and **Dynamic Keys (v2.0.2 or earlier)** for authenticating users in real-time communication applications.

> This rewrite **preserves the integrity** of Agora's token-signing logic while introducing improvements for better structure, developer experience, and Composer compatibility.

---

## Features and Enhancements

* **Type Hints**: Enforced strict typing for all method parameters and return types, making it IDE and static-analysis friendly.
* **Agora Client Class**: Centralized class for initializing your `App ID` and `App Certificate`.
* **User Model**: Object-oriented structure for setting user-specific values like UID, role, privileges, and token expiration.
* **Role Constants**: Easy-to-read and self-documented roles for RTC and RTM users.
* **Privilege Constants**: Easily manage supported privileges using named constants.
* **Namespace Organization**: Organized into PHP namespaces for clarity and autoloading support.
* **Exception Handling**: Errors now throw meaningful exceptions (e.g., missing credentials), improving debugging and robustness.
* **Composer Compatible**: Easily install and autoload with Composer.


---

## Class Refactor & Naming Convention

To improve clarity and follow modern naming conventions, we have renamed certain classes. The **legacy version** now uses a `Legacy` suffix, while the **latest version** drops the previous `2` suffix:

| Old Class Name  | New Class Name                       | Description                              |
| --------------- | ------------------------------------ | ---------------------------------------- |
| `AccessToken`   | `AccessTokenLegacy`                  | Agora AccessToken Version `006` (legacy) |
| `AccessToken2`  | `AccessToken`                        | Agora AccessToken Version `007` (latest) |
| `RtcTokenBuilder`      | `RtcTokenLegacy`                     | Builder for AccessToken `006`            |
| `RtcTokenBuilder2`     | `RtcToken`                           | Builder for AccessToken `007`            |
| `RtmTokenBuilder` | `RtmTokenLegacy` |  Builder for RTM token `AccessTokenLegacy`        |
| `RtmTokenBuilder2`     | `RtmToken`                           | Builder for RTM token `AccessToken`     |

> This ensures the default token builder always targets the most **up-to-date** version, while legacy usage remains supported but explicitly labeled.


---

## Target Versions

This package supports:

* **AccessToken Version `006`**
* **AccessToken2 Version `007`**

---

## Installation

Install the package via Composer:

```bash
composer require peterujah/php-agora-tokens
```

---

## Getting Started

1. **Sign Up for Agora**

   * Create an Agora account and obtain your **App ID** and **App Certificate**.
   * [https://www.agora.io/en/](https://www.agora.io/en/)

---

## Class Namespaces

Below is a breakdown of the namespaces and classes provided by the package:

### Core Components

* **`Peterujah\Agora\Agora`**
  Manages the Agora `App ID`, `App Certificate`, default channel name, user role, and token expiration setup.

* **`Peterujah\Agora\User`**
  Represents the user identity. Handles UID, channel name, user role, and privilege assignment.

* **`Peterujah\Agora\Roles`**
  Defines constants for user roles used in RTC and RTM tokens:

  * `RTC_PUBLISHER`, `RTC_SUBSCRIBER`, `RTC_ATTENDEE`, `RTC_ADMIN`, `RTM_USER`

* **`Peterujah\Agora\Privileges`**
  Contains all supported privilege constants for APAAS, CHAT, RTC and RTM services.

  * `APAAS_ROOM_USER`, `RTC_JOIN_CHANNEL`, `PERMISSIONS`

* **`Peterujah\Agora\Message`**
  Handles basic agora message payload packing.

* **`Peterujah\Agora\Util`**
  Utility helpers used internally for token formatting or time-based calculations.

* **`Peterujah\Agora\BaseService`**
  Shared logic base for service-related classes (e.g., default token generators).

* **`Peterujah\Agora\func`**
  Procedural utility functions for:

  * `DynamicKey4`, `DynamicKey5`, and `SignalingToken` generation.

---

### Agora Access Token Handlers

* **`Peterujah\Agora\Tokens\AccessTokenLegacy`**
  Builder for legacy AccessToken (`006`).

* **`Peterujah\Agora\Tokens\AccessToken`**
  Builder for modern AccessToken2 (`007`) format.

---

### Token Builders (Service Specific)

Each token builder generates a scoped access token for a specific Agora service:

* **`Peterujah\Agora\Builders\ApaasToken`** – For Agora Platform-as-a-Service (APaaS).
* **`Peterujah\Agora\Builders\ChatToken`** – For Chat/Messaging token generation.
* **`Peterujah\Agora\Builders\EducationToken`** – For Agora’s Education SDK token support.
* **`Peterujah\Agora\Builders\FpaToken`** – For First-Packet Acceleration (FPA).
* **`Peterujah\Agora\Builders\RtcToken`** – For RTC AccessToken (`007`) generation.
* **`Peterujah\Agora\Builders\RtcTokenLegacy`** – For RTC AccessToken2 (`006`) generation.
* **`Peterujah\Agora\Builders\RtmToken`** – For RTM AccessToken (`007`).
* **`Peterujah\Agora\Builders\RtmTokenLegacy`** – For RTM AccessToken2 (`006`).

---

### Agora Service Interfaces

High-level abstractions that encapsulate Agora service-specific token generation:

* **`Peterujah\Agora\Services\Apaas`** – Handles APaaS token creation.
* **`Peterujah\Agora\Services\Chat`** – Token management for Chat services.
* **`Peterujah\Agora\Services\Fpa`** – Handles First-Packet Acceleration service tokens.
* **`Peterujah\Agora\Services\Rtc`** – Manages RTC token generation via builder classes.
* **`Peterujah\Agora\Services\Rtm`** – Manages RTM token generation via builder classes.

---

## Usage Examples

You can generate tokens locally using your Agora App ID and App Certificate.

### 🔗 Sample References

* Original Agora PHP Sample: [Agora Dynamic Key PHP Samples](https://github.com/AgoraIO/Tools/tree/master/DynamicKey/AgoraDynamicKey/php/sample)
* Current Composer-Friendly Implementation: [PHP Agora Tokens Samples](https://github.com/peterujah/php-agora-tokens/sample)

---

### Basic Token Generation Example

```php
use Peterujah\Agora\Agora;
use Peterujah\Agora\User;
use Peterujah\Agora\Roles;
use Peterujah\Agora\Builders\RtcTokenLegacy;

// Example inputs
$channelName = "7d72365eb983485397e3e3f9d460bdda";
$uid = 2882341273;
$uidStr = "2882341273";
$expireTimeInSeconds = 3600;

// Generate privilege expiration timestamp (UTC)
$currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
$privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

// Initialize Agora client using environment variables
$client = new Agora(
    getenv("AGORA_APP_ID"),           // Set this in your environment
    getenv("AGORA_APP_CERTIFICATE")   // Set this in your environment
);

// User using UID (int)
$user1 = (new User($uid))
    ->setPrivilegeExpire($privilegeExpiredTs)
    ->setChannel($channelName)
    ->setRole(Roles::RTC_PUBLISHER);

// Generate RTC token using legacy builder (AccessToken v006)
$token1 = RtcTokenLegacy::buildTokenWithUid($client, $user1);
echo 'Token with int UID: ' . $token1 . PHP_EOL;

// User using User Account (string)
$user2 = (new User($uidStr))
    ->setPrivilegeExpire($privilegeExpiredTs)
    ->setChannel($channelName)
    ->setRole(Roles::RTC_PUBLISHER);

// Generate RTC token using legacy builder with user account
$token2 = RtcTokenLegacy::buildTokenWithUserAccount($client, $user2);
echo 'Token with user account: ' . $token2 . PHP_EOL;
```

---

### Recommendations

* Use `RtcToken` instead of `RtcTokenLegacy` if you're working with **AccessToken2 (v007)**.
* Use `putenv()` or `.env` files to set `AGORA_APP_ID` and `AGORA_APP_CERTIFICATE` during local development.
* Ensure your system clock is synchronized (especially in UTC) when generating tokens.

---

## License

This project is licensed under the [MIT License](LICENSE).

---

## Credits

Original implementation by [Agora](https://github.com/AgoraIO/Tools).
Composer-friendly rewrite and enhancements by [@peterujah](https://github.com/peterujah).
