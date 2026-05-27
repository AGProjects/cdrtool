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
    .alert {
        padding: 8px 35px 8px 14px;
        margin-bottom: 14px;
        text-shadow: 0 1px 0
        rgba(255, 255, 255, 0.5);
        background-color:
        #fcf8e3;
        border: 1px solid
        #fbeed5;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        color: #c09853;
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
                            <td bgcolor="#EEEEEE" class=grey>
                                <center>
                                    <img src="https://mdns.sipthor.net/images/Sip2Sip-logo.png">
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor='#FFFFFF' style='font-family: Helvetica,Arial,sans-serif; font-size: 10pt;'>
                                    <h3>
                                        Dear {$client->name},
                                    </h3>

                                <p>
                                    A delete request for SIP account <strong>{$client->account}</strong> has been issued. If it was not you who made the request, or you don't want to remove your account, you can safely ignore this email.
                                </p>

                                {* Structured request details. Same template
                                   handles both web-page and mobile-device
                                   requests — deleteAccount() server-side
                                   always builds the full record now, so the
                                   shape is uniform. Device-only fields
                                   (device_brand, device_model, os_version,
                                   app_version) are wrapped in {if isset}
                                   guards so they collapse out for web
                                   requests rather than rendering empty rows. *}
                                <p style='margin-bottom: 6px;'><strong>Request details:</strong></p>
                                <table style='border-collapse: collapse; margin-bottom: 14px; font-size: 10pt; width: 100%;' cellpadding='4'>
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555; width: 35%;'>Server request ID</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.server_request_id|escape:'html'}</td>
                                    </tr>
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555;'>Client request ID</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.client_request_id|escape:'html'}</td>
                                    </tr>
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555;'>Timestamp</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.client_timestamp|escape:'html'}</td>
                                    </tr>
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555;'>IP address</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.ip|escape:'html'}</td>
                                    </tr>
                                </table>

                                {* Requester device sub-section — keys present
                                   on every request (platform, user_agent) +
                                   mobile-only details when available. The
                                   {if} guards keep the row out when the
                                   value is missing or empty, so a web
                                   request renders just Platform + User
                                   agent without empty device-fingerprint
                                   rows. *}
                                <p style='margin-bottom: 6px;'><strong>Requester device:</strong></p>
                                <table style='border-collapse: collapse; margin-bottom: 14px; font-size: 10pt; width: 100%;' cellpadding='4'>
                                    {if isset($requester_entity.platform) && $requester_entity.platform neq ''}
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555; width: 35%;'>Platform</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.platform|escape:'html'}</td>
                                    </tr>
                                    {/if}
                                    {if isset($requester_entity.user_agent) && $requester_entity.user_agent neq ''}
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555;'>User agent</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace; word-break: break-all;'>{$requester_entity.user_agent|escape:'html'}</td>
                                    </tr>
                                    {/if}
                                    {if isset($requester_entity.os_version) && $requester_entity.os_version neq ''}
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555;'>OS version</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.os_version|escape:'html'}</td>
                                    </tr>
                                    {/if}
                                    {if isset($requester_entity.device_brand) && $requester_entity.device_brand neq ''}
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555;'>Device brand</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.device_brand|escape:'html'}</td>
                                    </tr>
                                    {/if}
                                    {if isset($requester_entity.device_model) && $requester_entity.device_model neq ''}
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555;'>Device model</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.device_model|escape:'html'}</td>
                                    </tr>
                                    {/if}
                                    {if isset($requester_entity.app_version) && $requester_entity.app_version neq ''}
                                    <tr>
                                        <td style='border: 1px solid #DDDDDD; background-color: #F5F5F5; padding: 4px 10px; text-align: right; color: #555555;'>App version</td>
                                        <td style='border: 1px solid #DDDDDD; padding: 4px 10px; font-family: Menlo, Consolas, monospace;'>{$requester_entity.app_version|escape:'html'}</td>
                                    </tr>
                                    {/if}
                                </table>

                                <p class='alert' style='padding: 8px 35px 8px 14px; margin-bottom: 14px; text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5); background-color: #fcf8e3; border: 1px solid #fbeed5; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; color: #c09853;'>
                                <strong>The possibility to delete the account will be active for 2 days until {$client->expire_date}</strong>
                                </p>
                                <p>
                                    Click <a href="{$client->sip_settings_page}/?action=delete_account&username={$client->account}&delete_id={$client->delete_id}">this</a> link to remove the account.                                    
                                </p>
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
