Dear {$client->name},

The following properties have been changed for your SIP account {$client->account} from the IP address {$client->ip} {if $client->location}located in {$client->location}.{else}.{/if}

    {foreach name=fields from=$client->fields item=field}
    {$field}
    {/foreach}
