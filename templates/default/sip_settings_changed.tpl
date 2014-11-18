Dear {$client->name},

The follwing values have been changed for the SIP account {$client->account} from the IP: {$client->ip}.

    {foreach name=fields from=$client->fields item=field}
    {$field}
    {/foreach}
