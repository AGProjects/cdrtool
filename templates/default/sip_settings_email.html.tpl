<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body style='background-image: url('https://mdns.sipthor.net/images/gradient_texture.png'); background-repeat:repeat-x;' background='https://mdns.sipthor.net/images/gradient_texture.png'>

{literal}
    <style type="text/css">
    <!--
    body {
        font-family: Helvetica,Arial,sans-serif; font-size: 10pt;
    }

    .wrapper {
        width: 650px; 
        background-color:#FFFFFF; 
        border-radius: 6px 6px 6px 6px; 
        padding: 5px;
        margin-top:4px;
        -moz-box-shadow: 0 0 5px #888;
        -webkit-box-shadow: 0 0 5px#888;
        box-shadow: 0 0 5px #888;
    }

    .logo {
        color:#5577A3;
        font-family: Helvetica, Arial,sans-serif ;
        font-size:48pt;
        padding-top: 10px;
        padding-bottom:10px;
        margin: 10px;
        text-shadow: 2px 2px 0px #6b6b6b;
        filter: dropshadow(color=#6b6b6b, offx=2, offy=2);
    }

    .logo_middle {
        color:#5577A3;
        font-family: Helvetica, Arial Narrow, Arial, sans-serif ;
        text-shadow: 0px 0px 0px #6b6b6b;
        font-size:24pt;
    }
    .grey {
        border-radius: 6px 6px 6px 6px;
        background-color: #EEEEEE;
    }
    -->
    </style>
{/literal}
    <table width="100%" height='100%' style='background: url('https://mdns.sipthor.net/images/gradient_texture.png');'>
        <tr>
            <td background="https://mdns.sipthor.net/images/gradient_texture.png">
                <center>
                    <table width='650px' style='margin-top: 5px; padding: 4px; width 650px; background-color:#FFFFFF; border: 1px solid #EEEEEE; border-radius: 6px 6px 6px 6px;    -moz-box-shadow: 0 0 5px #888; -webkit-box-shadow: 0 0 5px#888;box-shadow: 0 0 5px #888; padding-bottom:20px;'>
                        <tr>
                            <td bgcolor='#FFFFFF' style='font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                    <h3>
                                        Dear {$client->name},
                                    </h3>

                                <p>
                                    Make acquaintance with your new SIP account. SIP stands for Session 
                                    Initiation Protocol and is becoming the universal way to communicate using
                                    software phones, desktop telephones or dual-mode WiFi phones.
                                </p>
                                <p>
                                    Your SIP address is <b>{$client->account}</b>
                                </p>

                                <p>
                                    You may use this address in combination with a SIP device.
                                </p>

                                <p>
                                    You can use your SIP account for Voice and Video over IP, IM or Presence
                                    free of charge providing you have access to the Internet. Depending on
                                    your account settings you may also be able to place and receive calls
                                    to the public telephone network, redirect Internet calls to your mobile
                                    phone or receive voicemail by email.
                                </p>
                                <p>
                                    How to use your SIP account:
                                </p>
                                <ol>
                                <li>You may use a SIP hardware phone 
                                   or download Blink SIP software client for
                                   Windows, MacOSX or Linux from:
                                   <a href='http://icanblink.com'>Blink</a>
                                </li>

                                <li>Setup your SIP device as follows:
                                    <table width='642px' cellborder=0 cellspacing="0" cellpadding="4px" style='border: none;'>

                                        <tr style='background-color:#EEEEEE; font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                            <td style='text-align:right;font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                Username
                                            </td>
                                            <td width=80% style='font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                {$client->username}
                                            </td>
                                        </tr>
                                        <tr style='background-color:#EEEEEE; font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                            <td style='text-align:right;font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                Password
                                            </td>
                                            <td style='font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                {$client->password}
                                            </td>
                                        </tr>
                                        <tr style='background-color:#EEEEEE;font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                            <td style='text-align:right'>
                                                Domain/Realm
                                            </td>
                                            <td>
                                                {$client->domain}
                                            </td>
                                        </tr>
                                        <tr style='background-color:#EEEEEE;font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                            <td style='text-align:right'>
                                                Register with domain
                                            </td>
                                            <td>
                                                yes
                                            </td>
                                        </tr>
                                        <tr style='background-color:#EEEEEE;font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                            <td style='text-align:right'>
                                                Outbound Proxy
                                            </td>
                                            <td>
                                                {$client->sip_proxy}
                                            </td>
                                        </tr>
                                        
                                        {if $client->xcap_root}
                                        <tr style='background-color:#EEEEEE;font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                            <td style='text-align:right; font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                Presence Mode
                                            </td>
                                            <td style='font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                Presence agent
                                            </td>
                                        </tr>
                                        <tr style='background-color:#EEEEEE;font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                            <td style='text-align:right; font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                Storage Policy
                                            </td>
                                            <td style='font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                XCAP 
                                            </td>
                                        </tr>
                                        <tr style='background-color:#EEEEEE;font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                            <td style='text-align:right; font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                XCAP Root
                                            </td>
                                            <td style='font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                                {$client->xcap_root}
                                            </td>
                                        </tr>
                                        {/if}
                                    </table>
                                </li>
                                </ol>

                                {foreach name=enums from=$client->enums item=enum}
                                <p>ENUM number {$smarty.foreach.enums.iteration}: {$enum}</p>
                                {/foreach}

                                {foreach name=aliases from=$client->aliases item=alias}
                                <p>Alias {$smarty.foreach.aliases.iteration}: {$alias}</p>
                                {/foreach}
                                {if $client->voicemailMailbox}
                                <p>Your voicemail is delivered by e-mail to {$client->email}</p>
                                {/if}
                                {if $client->allowPSTN}
                                <p>You may call to PSTN</p>
                                {/if}
                                <p>
                                    To access your account settings go to <a href='{$client->sip_settings_page}'>{$client->sip_settings_page}</a><br />
                                    Your login details:
                                </p>
                                <p style='padding-left:10px; background-color:#EEEEEE'>
                                    <br />
                                    {if $client->web_password}
                                    Password: {$client->web_password}
                                    {else}
                                    Password: {$client->password}
                                    {/if}
                                </p>
				<p>
				If you wish to delete your account, go to identity section of the account settings page.
				</p>
                                <p>
                                How to use your SIP account:
                                </p>
                                <ul>
                                   <li>To test, call 3333, you should hear some music playing</li>
                                   <li>To call someone, type in the called SIP address (name@domain.com) and enter</li>
                                   <li>To call ENUM enabled numbers use + or * and then the full ENUM number</li>
                                   <li>To change your privacy settings dial {$client->changePrivacyAccessNumber}, to check privacy status dial {$client->checkPrivacyAccessNumber}</li>
                                {if $client->voicemailMailbox}
                                   <li>To access your voicemail messages or mailbox settings dial {$client->voicemailAccessNumber}</li>
                                {/if}
                                {if $client->cdrtool_address}
                                   <li>To access your Call Detail Records go to: {$client->cdrtool_address}</li>
                                {/if}
                                </ul>
                                {if $client->support_web}
                                <p>For more information visit {$client->support_web}</p>
                                {/if}
                            </td>
                        </tr>
                    </table>
                </center>
                <br/>
            </td>
        </tr>
    </table>
</body>
</html>