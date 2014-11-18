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
                                    The follwing values have been changed for the SIP account {$client->account} from {$client->ip}.
                                </p>
                                <ul>
                                {foreach name=fields from=$client->fields item=field}
                                <li>{$smarty.foreach.fields.iteration}: {$field}</li>
                                {/foreach}
                                </ul>
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
