Dear {$client->name},

Make acquaintance with your new SIP account. SIP stands for Session 
Initiation Protocol and is becoming the universal way to communicate using
software phones, desktop telephones or dual-mode WiFi phones.

Your SIP address is {$client->account}

You may use this address in combination with a SIP device.

You can use your SIP account for Voice and Video over IP, IM or Presence
free of charge providing you have access to the Internet. Depending on
your account settings you may also be able to place and receive calls
to the public telephone network, redirect Internet calls to your mobile
phone or receive voicemail by email.

How to use your SIP account:

1. You may use a SIP hardware phone 
   or download Blink SIP software client for
   Windows, MacOSX or Linux from:
   
   http://icanblink.com

2. Setup your SIP device as follows:

Username: {$client->username}
{if $client->password}
Password: {$client->password}
{/if}
Domain: {$client->domain}
Register with domain: Yes
Outboud Proxy: {$client->sip_proxy}

{if $client->xcap_root}
Presence mode: Presence agent
Storage policy: XCAP
XCAP Root URL: {$client->xcap_root}
{/if}

{foreach name=enums from=$client->enums item=enum}
ENUM number {$smarty.foreach.enums.iteration}: {$enum}
{/foreach}
{foreach name=aliases from=$client->aliases item=alias}
Alias {$smarty.foreach.aliases.iteration}: {$alias}
{/foreach}

{if $client->voicemailMailbox}
Your voicemail is delivered by e-mail to {$client->email}
{/if}
{if $client->allowPSTN}
You may call to PSTN
{/if}

To access your account settings go to {$client->sip_settings_page}
{if $client->web_password or $client->password}
Your login details:

   Username: {$client->account}
{if $client->web_password}
   Password: {$client->web_password}
{else}
{if $client->password}
   Password: {$client->password}
{/if}
{/if}
{/if}
If you wish to delete your account, go to identity section of the account settings page.

How to use your SIP account:

   - To test, call 3333, you should hear some music playing
   - To call someone, type in the called SIP address (name@domain.com) and enter
   - To call ENUM enabled numbers use + or * and then the full ENUM number
   - To change your privacy settings dial {$client->changePrivacyAccessNumber}, to check privacy status dial {$client->checkPrivacyAccessNumber}
{if $client->voicemailMailbox}
   - To access your voicemail messages or mailbox settings dial {$client->voicemailAccessNumber}
{/if}
{if $client->cdrtool_address}
   - To access your Call Detail Records go to: {$client->cdrtool_address}
{/if}

{if $client->support_web}
For more information visit {$client->support_web}
{/if}

