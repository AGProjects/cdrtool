Dear {$client->name},

The follwing values have been changed for the SIP account {$client->account} from {$client->ip}.

    {foreach name=fields from=$client->fields item=field}
    {$smarty.foreach.fields.iteration}: {$field}
    {/foreach}
