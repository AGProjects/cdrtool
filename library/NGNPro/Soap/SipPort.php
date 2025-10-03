<?php

class WebService_NGNPro_SipPort extends SOAP_Client_Custom
{
    function &addGateway($gateway)
    {
        // gateway is a ComplexType Gateway,
        // refer to wsdl for more info
        $gateway = new SOAP_Value('gateway', '{urn:AGProjects:NGNPro}Gateway', $gateway);
        $result = $this->call('addGateway',
                              $v = array('gateway' => $gateway),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateGateway($gateway)
    {
        // gateway is a ComplexType Gateway,
        // refer to wsdl for more info
        $gateway = new SOAP_Value('gateway', '{urn:AGProjects:NGNPro}Gateway', $gateway);
        $result = $this->call('updateGateway',
                              $v = array('gateway' => $gateway),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteGateway($id)
    {
        $result = $this->call('deleteGateway',
                              $v = array('id' => $id),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getGateways($query)
    {
        // query is a ComplexType GatewayQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}GatewayQuery', $query);
        $result = $this->call('getGateways',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addGatewayRule($rule)
    {
        // rule is a ComplexType GatewayRule,
        // refer to wsdl for more info
        $rule = new SOAP_Value('rule', '{urn:AGProjects:NGNPro}GatewayRule', $rule);
        $result = $this->call('addGatewayRule',
                              $v = array('rule' => $rule),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateGatewayRule($rule)
    {
        // rule is a ComplexType GatewayRule,
        // refer to wsdl for more info
        $rule = new SOAP_Value('rule', '{urn:AGProjects:NGNPro}GatewayRule', $rule);
        $result = $this->call('updateGatewayRule',
                              $v = array('rule' => $rule),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteGatewayRule($id)
    {
        $result = $this->call('deleteGatewayRule',
                              $v = array('id' => $id),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getGatewayRules($query)
    {
        // query is a ComplexType GatewayRuleQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}GatewayRuleQuery', $query);
        $result = $this->call('getGatewayRules',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addCarrier($carrier)
    {
        // carrier is a ComplexType Carrier,
        // refer to wsdl for more info
        $carrier = new SOAP_Value('carrier', '{urn:AGProjects:NGNPro}Carrier', $carrier);
        $result = $this->call('addCarrier',
                              $v = array('carrier' => $carrier),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateCarrier($carrier)
    {
        // carrier is a ComplexType Carrier,
        // refer to wsdl for more info
        $carrier = new SOAP_Value('carrier', '{urn:AGProjects:NGNPro}Carrier', $carrier);
        $result = $this->call('updateCarrier',
                              $v = array('carrier' => $carrier),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteCarrier($id)
    {
        $result = $this->call('deleteCarrier',
                              $v = array('id' => $id),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getCarriers($query)
    {
        // query is a ComplexType CarrierQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}CarrierQuery', $query);
        $result = $this->call('getCarriers',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addRoutes($routes)
    {
        // routes is a ComplexType RouteArray,
        // refer to wsdl for more info
        $routes = new SOAP_Value('routes', '{urn:AGProjects:NGNPro}RouteArray', $routes);
        $result = $this->call('addRoutes',
                              $v = array('routes' => $routes),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateRoutes($routes)
    {
        // routes is a ComplexType RouteArray,
        // refer to wsdl for more info
        $routes = new SOAP_Value('routes', '{urn:AGProjects:NGNPro}RouteArray', $routes);
        $result = $this->call('updateRoutes',
                              $v = array('routes' => $routes),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteRoutes($routes)
    {
        // routes is a ComplexType RouteArray,
        // refer to wsdl for more info
        $routes = new SOAP_Value('routes', '{urn:AGProjects:NGNPro}RouteArray', $routes);
        $result = $this->call('deleteRoutes',
                              $v = array('routes' => $routes),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getRoutes($query)
    {
        // query is a ComplexType RouteQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}RouteQuery', $query);
        $result = $this->call('getRoutes',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addDomain($domain)
    {
        // domain is a ComplexType SipDomain,
        // refer to wsdl for more info
        $domain = new SOAP_Value('domain', '{urn:AGProjects:NGNPro}SipDomain', $domain);
        $result = $this->call('addDomain',
                              $v = array('domain' => $domain),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateDomain($domain)
    {
        // domain is a ComplexType SipDomain,
        // refer to wsdl for more info
        $domain = new SOAP_Value('domain', '{urn:AGProjects:NGNPro}SipDomain', $domain);
        $result = $this->call('updateDomain',
                              $v = array('domain' => $domain),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteDomain($domain)
    {
        $result = $this->call('deleteDomain',
                              $v = array('domain' => $domain),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getDomains($query)
    {
        // query is a ComplexType SipDomainQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}SipDomainQuery', $query);
        $result = $this->call('getDomains',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addTrustedPeer($peer)
    {
        // peer is a ComplexType TrustedPeer,
        // refer to wsdl for more info
        $peer = new SOAP_Value('peer', '{urn:AGProjects:NGNPro}TrustedPeer', $peer);
        $result = $this->call('addTrustedPeer',
                              $v = array('peer' => $peer),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteTrustedPeer($ip)
    {
        $result = $this->call('deleteTrustedPeer',
                              $v = array('ip' => $ip),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateTrustedPeer($peer)
    {
        $peer = new SOAP_Value('peer', '{urn:AGProjects:NGNPro}TrustedPeer', $peer);
        $result = $this->call('updateTrustedPeer',
                              $v = array('peer' => $peer),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getTrustedPeers($query)
    {
        // query is a ComplexType TrustedPeerQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}TrustedPeerQuery', $query);
        $result = $this->call('getTrustedPeers',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addAccount($account)
    {
        // account is a ComplexType SipAccount,
        // refer to wsdl for more info
        $account = new SOAP_Value('account', '{urn:AGProjects:NGNPro}SipAccount', $account);
        $result = $this->call('addAccount',
                              $v = array('account' => $account),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateAccount($account)
    {
        // account is a ComplexType SipAccount,
        // refer to wsdl for more info
        $account = new SOAP_Value('account', '{urn:AGProjects:NGNPro}SipAccount', $account);
        $result = $this->call('updateAccount',
                              $v = array('account' => $account),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteAccount($sipId)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('deleteAccount',
                              $v = array('sipId' => $sipId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getAccount($sipId)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('getAccount',
                              $v = array('sipId' => $sipId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getAccounts($query)
    {
        // query is a ComplexType SipQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}SipQuery', $query);
        $result = $this->call('getAccounts',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addAlias($alias)
    {
        // alias is a ComplexType SipAlias,
        // refer to wsdl for more info
        $alias = new SOAP_Value('alias', '{urn:AGProjects:NGNPro}SipAlias', $alias);
        $result = $this->call('addAlias',
                              $v = array('alias' => $alias),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateAlias($alias)
    {
        // alias is a ComplexType SipAlias,
        // refer to wsdl for more info
        $alias = new SOAP_Value('alias', '{urn:AGProjects:NGNPro}SipAlias', $alias);
        $result = $this->call('updateAlias',
                              $v = array('alias' => $alias),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteAlias($id)
    {
        // id is a ComplexType SipId,
        // refer to wsdl for more info
        $id = new SOAP_Value('id', '{urn:AGProjects:NGNPro}SipId', $id);
        $result = $this->call('deleteAlias',
                              $v = array('id' => $id),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getAlias($id)
    {
        // id is a ComplexType SipId,
        // refer to wsdl for more info
        $id = new SOAP_Value('id', '{urn:AGProjects:NGNPro}SipId', $id);
        $result = $this->call('getAlias',
                              $v = array('id' => $id),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getAliases($query)
    {
        // query is a ComplexType AliasQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}AliasQuery', $query);
        $result = $this->call('getAliases',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addToGroup($sipId, $group)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('addToGroup',
                              $v = array('sipId' => $sipId, 'group' => $group),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &removeFromGroup($sipId, $group)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('removeFromGroup',
                              $v = array('sipId' => $sipId, 'group' => $group),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getGroups($sipId)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('getGroups',
                              $v = array('sipId' => $sipId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addBalance($sipId, $value, $description)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('addBalance',
                              $v = array('sipId' => $sipId, 'value' => $value, 'description' => $description),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addBalanceFromVoucher($sipId, $card)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // card is a ComplexType PrepaidCard,
        // refer to wsdl for more info
        $card = new SOAP_Value('card', '{urn:AGProjects:NGNPro}PrepaidCard', $card);
        $result = $this->call('addBalanceFromVoucher',
                              $v = array('sipId' => $sipId, 'card' => $card),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getPrepaidStatus($sipIds)
    {
        // sipIds is a ComplexType SipIdArray,
        // refer to wsdl for more info
        $sipIds = new SOAP_Value('sipIds', '{urn:AGProjects:NGNPro}SipIdArray', $sipIds);
        $result = $this->call('getPrepaidStatus',
                              $v = array('sipIds' => $sipIds),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getCreditHistory($sipId, $count)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('getCreditHistory',
                              $v = array('sipId' => $sipId, 'count' => $count),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addPhonebookEntry($sipId, $entry)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // entry is a ComplexType PhonebookEntry,
        // refer to wsdl for more info
        $entry = new SOAP_Value('entry', '{urn:AGProjects:NGNPro}PhonebookEntry', $entry);
        $result = $this->call('addPhonebookEntry',
                              $v = array('sipId' => $sipId, 'entry' => $entry),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updatePhonebookEntry($sipId, $entry)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // entry is a ComplexType PhonebookEntry,
        // refer to wsdl for more info
        $entry = new SOAP_Value('entry', '{urn:AGProjects:NGNPro}PhonebookEntry', $entry);
        $result = $this->call('updatePhonebookEntry',
                              $v = array('sipId' => $sipId, 'entry' => $entry),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deletePhonebookEntry($sipId, $uri)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('deletePhonebookEntry',
                              $v = array('sipId' => $sipId, 'uri' => $uri),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getPhonebookEntries($sipId, $match, $range)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // match is a ComplexType PhonebookEntry,
        // refer to wsdl for more info
        $match = new SOAP_Value('match', '{urn:AGProjects:NGNPro}PhonebookEntry', $match);
        // range is a ComplexType Range,
        // refer to wsdl for more info
        $range = new SOAP_Value('range', '{urn:AGProjects:NGNPro}Range', $range);
        $result = $this->call('getPhonebookEntries',
                              $v = array('sipId' => $sipId, 'match' => $match, 'range' => $range),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &setRejectMembers($sipId, $members)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // members is a ComplexType StringArray,
        // refer to wsdl for more info
        $members = new SOAP_Value('members', '{urn:AGProjects:NGNPro}StringArray', $members);
        $result = $this->call('setRejectMembers',
                              $v = array('sipId' => $sipId, 'members' => $members),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getRejectMembers($sipId)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('getRejectMembers',
                              $v = array('sipId' => $sipId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &setAcceptRules($sipId, $rules)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // rules is a ComplexType AcceptRules,
        // refer to wsdl for more info
        $rules = new SOAP_Value('rules', '{urn:AGProjects:NGNPro}AcceptRules', $rules);
        $result = $this->call('setAcceptRules',
                              $v = array('sipId' => $sipId, 'rules' => $rules),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getAcceptRules($sipId)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('getAcceptRules',
                              $v = array('sipId' => $sipId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &setBarringPrefixes($sipId, $prefixes)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // prefixes is a ComplexType StringArray,
        // refer to wsdl for more info
        $prefixes = new SOAP_Value('prefixes', '{urn:AGProjects:NGNPro}StringArray', $prefixes);
        $result = $this->call('setBarringPrefixes',
                              $v = array('sipId' => $sipId, 'prefixes' => $prefixes),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getBarringPrefixes($sipId)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('getBarringPrefixes',
                              $v = array('sipId' => $sipId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &setCallDiversions($sipId, $diversions)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // diversions is a ComplexType CallDiversions,
        // refer to wsdl for more info
        $diversions = new SOAP_Value('diversions', '{urn:AGProjects:NGNPro}CallDiversions', $diversions);
        $result = $this->call('setCallDiversions',
                              $v = array('sipId' => $sipId, 'diversions' => $diversions),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getCallDiversions($sipId)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('getCallDiversions',
                              $v = array('sipId' => $sipId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getCalls($sipId, $query)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // query is a ComplexType CallsQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}CallsQuery', $query);
        $result = $this->call('getCalls',
                              $v = array('sipId' => $sipId, 'query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getCallStatistics($sipId, $query)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        // query is a ComplexType CallsQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}CallsQuery', $query);
        $result = $this->call('getCallStatistics',
                              $v = array('sipId' => $sipId, 'query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getSipDeviceLocations($sipIds)
    {
        // sipIds is a ComplexType SipIdArray,
        // refer to wsdl for more info
        $sipIds = new SOAP_Value('sipIds', '{urn:AGProjects:NGNPro}SipIdArray', $sipIds);
        $result = $this->call('getSipDeviceLocations',
                              $v = array('sipIds' => $sipIds),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getSipTrace($filter)
    {
        // filter is a ComplexType SipTraceFilter,
        // refer to wsdl for more info
        $filter = new SOAP_Value('filter', '{urn:AGProjects:NGNPro}SipTraceFilter', $filter);
        $result = $this->call('getSipTrace',
                              $v = array('filter' => $filter),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getMediaTrace($filter)
    {
        // filter is a ComplexType MediaTraceFilter,
        // refer to wsdl for more info
        $filter = new SOAP_Value('filter', '{urn:AGProjects:NGNPro}MediaTraceFilter', $filter);
        $result = $this->call('getMediaTrace',
                              $v = array('filter' => $filter),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getMediaSummary()
    {
        $result = $this->call('getMediaSummary',
                              $v = null,
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getMediaSessions()
    {
        $result = $this->call('getMediaSessions',
                              $v = null,
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}
