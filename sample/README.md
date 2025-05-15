- **Peterujah\Agora\Builders\RtcToken**: Source code for generating a token for the following SDKs:
  - Agora Native SDK v2.1+

  - Agora Web SDK v2.4+

  - Agora Recording SDK v2.1+

  - Agora RTSA SDK

> The Agora RTSA SDK supports joining multiple channels. If you join multiple channels at the same time, then you MUST generate a specific token for each channel you join. 

- **Peterujah\Agora\Builders\RtmToken**: Source code for generating a token for the Agora RTM SDK. 
- **Peterujah\Agora\AccessToken**: Implements all the underlying algorithms for generating a token. Both **Peterujah\Agora\Builders\RtcToken** and **Peterujah\Agora\Builders\RtmToken** are a wrapper of **Peterujah\Agora\AccessToken** and have much easier-to-use APIs. We recommend using **Peterujah\Agora\Builders\RtcToken** for generating an RTC token or **Peterujah\Agora\Builders\RtmToken** for an RTM token.
