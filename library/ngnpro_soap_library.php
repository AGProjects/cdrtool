<?

/*
	Copyright (c) 2007 AG Projects
  	http://ag-projects.com
  	Author Adrian Georgescu

  	This library provide NGN-Pro soap client functions
  	It requires the SOAP library from http://pear.php.net

*/

require_once('SOAP/Client.php');

class SOAP_Client_Custom extends SOAP_Client {

    function _serializeValue(&$value, $name = '', $type = false, $elNamespace = NULL, $typeNamespace=NULL, $options=array(), $attributes = array(), $artype='')
    {
        $namespaces = array();
        $arrayType = $array_depth = $xmlout_value = null;
        $typePrefix = $elPrefix = $xmlout_offset = $xmlout_arrayType = $xmlout_type = $xmlns = '';
        $ptype = $array_type_ns = '';

        if (!$name || is_numeric($name)) {
            $name = 'item';
        }

        if ($this->_wsdl)
            list($ptype, $arrayType, $array_type_ns, $array_depth)
                    = $this->_wsdl->getSchemaType($type, $name, $typeNamespace);

        if (!$arrayType) $arrayType = $artype;
        if (!$ptype) $ptype = $this->_getType($value);
        if (!$type) $type = $ptype;

        if (strcasecmp($ptype,'Struct') == 0 || strcasecmp($type,'Struct') == 0) {
            // struct
            $vars = NULL;
            if (is_object($value)) {
                $vars = get_object_vars($value);
            } else {
                $vars = &$value;
            }
            if (is_array($vars)) {
                foreach (array_keys($vars) as $k) {
                    if ($k[0]=='_') continue; // hide private vars
                    if (is_object($vars[$k])) {
                        if (is_a($vars[$k],'soap_value')) {
                            $xmlout_value .= $vars[$k]->serialize($this);
                        } else {
                            // XXX get the members and serialize them instead
                            // converting to an array is more overhead than we
                            // should realy do, but php-soap is on it's way.
                            $xmlout_value .= $this->_serializeValue(get_object_vars($vars[$k]), $k, false, $this->_section5?NULL:$elNamespace);
                        }
                    } else {
                        $xmlout_value .= $this->_serializeValue($vars[$k],$k, false, $this->_section5?NULL:$elNamespace);
                    }
                }
            }
        } else if (strcasecmp($ptype,'Array')==0 || strcasecmp($type,'Array')==0) {
            // array
            $typeNamespace = SOAP_SCHEMA_ENCODING;
            $orig_type = $type;
            $type = 'Array';
            $numtypes = 0;
            // XXX this will be slow on larger array's.  Basicly, it flattens array's to allow us
            // to serialize multi-dimensional array's.  We only do this if arrayType is set,
            // which will typicaly only happen if we are using WSDL
            if (isset($options['flatten']) || ($arrayType && (strchr($arrayType,',') || strstr($arrayType,'][')))) {
                $numtypes = $this->_multiArrayType($value, $arrayType, $ar_size, $xmlout_value);
            }

            $array_type = $array_type_prefix = '';
            if ($numtypes != 1) {
                $arrayTypeQName =& new QName($arrayType);
                $arrayType = $arrayTypeQName->name;
                $array_types = array();
                $array_val = NULL;

                // serialize each array element
                $ar_size = count($value);
		foreach ($value as $array_val) {
                    if ($this->_isSoapValue($array_val)) {
                        $array_type = $array_val->type;
                        $array_types[$array_type] = 1;
                        $array_type_ns = $array_val->type_namespace;
                        $xmlout_value .= $array_val->serialize($this);
                    } else {
                        $array_type = $this->_getType($array_val);
                        $array_types[$array_type] = 1;
                        $xmlout_value .= $this->_serializeValue($array_val,'item', $array_type, $this->_section5?NULL:$elNamespace);
                    }
                }

                $xmlout_offset = " SOAP-ENC:offset=\"[0]\"";
                if (!$arrayType) {
                    $numtypes = count($array_types);
                    if ($numtypes == 1) $arrayType = $array_type;
                    // using anyType is more interoperable
                    if ($array_type == 'Struct') {
                        $array_type = '';
                    } else if ($array_type == 'Array') {
                        $arrayType = 'anyType';
                        $array_type_prefix = 'xsd';
                    } else
                    if (!$arrayType) $arrayType = $array_type;
                }
            }
            if (!$arrayType || $numtypes > 1) {
                $arrayType = 'xsd:anyType'; // should reference what schema we're using
            } else {
                if ($array_type_ns) {
                    $array_type_prefix = $this->_getNamespacePrefix($array_type_ns);
                } else if (array_key_exists($arrayType, $this->_typemap[$this->_XMLSchemaVersion])) {
                    $array_type_prefix = $this->_namespaces[$this->_XMLSchemaVersion];
                }
                if ($array_type_prefix)
                    $arrayType = $array_type_prefix.':'.$arrayType;
            }

            $xmlout_arrayType = " SOAP-ENC:arrayType=\"" . $arrayType;
            if ($array_depth != null) {
                for ($i = 0; $i < $array_depth; $i++) {
                    $xmlout_arrayType .= '[]';
                }
            }
            $xmlout_arrayType .= "[$ar_size]\"";
        } else if ($this->_isSoapValue($value)) {
            $xmlout_value =& $value->serialize($this);
        } else if ($type == 'string') {
            $xmlout_value = htmlspecialchars($value);
        } else if ($type == 'rawstring') {
            $xmlout_value =& $value;
        } else if ($type == 'boolean') {
            $xmlout_value = $value?'true':'false';
        } else {
            $xmlout_value =& $value;
        }

        // add namespaces
        if ($elNamespace) {
            $elPrefix = $this->_getNamespacePrefix($elNamespace);
            $xmlout_name = "$elPrefix:$name";
        } else {
            $xmlout_name = $name;
        }

        if ($typeNamespace) {
            $typePrefix = $this->_getNamespacePrefix($typeNamespace);
            $xmlout_type = "$typePrefix:$type";
        } else if ($type && array_key_exists($type, $this->_typemap[$this->_XMLSchemaVersion])) {
            $typePrefix = $this->_namespaces[$this->_XMLSchemaVersion];
            $xmlout_type = "$typePrefix:$type";
        }

        // handle additional attributes
        $xml_attr = '';
        if (count($attributes) > 0) {
            foreach ($attributes as $k => $v) {
                $kqn =& new QName($k);
                $vqn =& new QName($v);
                $xml_attr .= ' '.$kqn->fqn().'="'.$vqn->fqn().'"';
            }
        }

        // store the attachement for mime encoding
        if (isset($options['attachment']))
            $this->__attachments[] = $options['attachment'];

        if ($this->_section5) {
            if ($xmlout_type) $xmlout_type = " xsi:type=\"$xmlout_type\"";
            if (is_null($xmlout_value)) {
                $xml = "\r\n<$xmlout_name$xmlout_type$xmlns$xmlout_arrayType$xml_attr/>";
            } else {
                $xml = "\r\n<$xmlout_name$xmlout_type$xmlns$xmlout_arrayType$xmlout_offset$xml_attr>".
                    $xmlout_value."</$xmlout_name>";
            }
        } else {
            if (is_null($xmlout_value)) {
                $xml = "\r\n<$xmlout_name$xmlns$xml_attr/>";
            } else {
                $xml = "\r\n<$xmlout_name$xmlns$xml_attr>".
                    $xmlout_value."</$xmlout_name>";
            }
        }
        return $xml;
    }




	function addHeader(&$soap_value) {
        // add a new header to the SOAP message if not already exists

		if (is_array($soap_value) && is_array($this->headersOut)) {
            foreach ($this->headersOut as $_header) {
                if ($_header->name == $soap_value[0]) {
                    return true;
                }
            }
        }

        if (is_a($soap_value,'soap_header')) {
            $this->headersOut[] =& $soap_value;
        } else if (gettype($soap_value) == 'array') {
            // name, value, namespace, mustunderstand, actor
            $this->headersOut[] =& new SOAP_Header($soap_value[0], NULL, $soap_value[1], $soap_value[2], $soap_value[3]);;
        } else {
            $this->_raiseSoapFault("Don't understand the header info you provided.  Must be array or SOAP_Header.");
        }
    }
}

class WebService_NGNPro_SipPort extends SOAP_Client_Custom
{
    function WebService_NGNPro_SipPort($url)
{
        $this->SOAP_Client_Custom($url, 0);
    }
    function &addDomain($domain) {
        return $this->call("addDomain", 
                        $v = array("domain"=>$domain), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &updateDomain($domain) {
        // domain is a ComplexType SipDomain,
        //refer to wsdl for more info
        $domain =& new SOAP_Value('domain','{urn:AGProjects:NGNPro}SipDomain',$domain);
        return $this->call("updateDomain", 
                        $v = array("domain"=>$domain), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteDomain($domain) {
        return $this->call("deleteDomain", 
                        $v = array("domain"=>$domain), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getDomains($query) {
        // query is a ComplexType SipDomainQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}SipDomainQuery',$query);
        return $this->call("getDomains", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getDomainStatistics($filter) {
        return $this->call("getDomainStatistics", 
                        $v = array("filter"=>$filter), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addTrustedPeer($peer) {
        // peer is a ComplexType TrustedPeer,
        //refer to wsdl for more info
        $peer =& new SOAP_Value('peer','{urn:AGProjects:NGNPro}TrustedPeer',$peer);
        return $this->call("addTrustedPeer", 
                        $v = array("peer"=>$peer), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteTrustedPeer($ip) {
        return $this->call("deleteTrustedPeer", 
                        $v = array("ip"=>$ip), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getTrustedPeers($query) {
        // query is a ComplexType TrustedPeerQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}TrustedPeerQuery',$query);
        return $this->call("getTrustedPeers", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addAccount($account) {
        // account is a ComplexType SipAccount,
        //refer to wsdl for more info
        $account =& new SOAP_Value('account','{urn:AGProjects:NGNPro}SipAccount',$account);
        return $this->call("addAccount", 
                        $v = array("account"=>$account), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &updateAccount($account) {
        // account is a ComplexType SipAccount,
        //refer to wsdl for more info
        $account =& new SOAP_Value('account','{urn:AGProjects:NGNPro}SipAccount',$account);
        return $this->call("updateAccount", 
                        $v = array("account"=>$account), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteAccount($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("deleteAccount", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getAccount($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getAccount", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getAccounts($query) {
        // query is a ComplexType SipQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}SipQuery',$query);
        return $this->call("getAccounts", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addAlias($alias) {
        // alias is a ComplexType SipAlias,
        //refer to wsdl for more info
        $alias =& new SOAP_Value('alias','{urn:AGProjects:NGNPro}SipAlias',$alias);
        return $this->call("addAlias", 
                        $v = array("alias"=>$alias), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &updateAlias($alias) {
        // alias is a ComplexType SipAlias,
        //refer to wsdl for more info
        $alias =& new SOAP_Value('alias','{urn:AGProjects:NGNPro}SipAlias',$alias);
        return $this->call("updateAlias", 
                        $v = array("alias"=>$alias), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteAlias($id) {
        // id is a ComplexType SipId,
        //refer to wsdl for more info
        $id =& new SOAP_Value('id','{urn:AGProjects:NGNPro}SipId',$id);
        return $this->call("deleteAlias", 
                        $v = array("id"=>$id), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getAlias($id) {
        // id is a ComplexType SipId,
        //refer to wsdl for more info
        $id =& new SOAP_Value('id','{urn:AGProjects:NGNPro}SipId',$id);
        return $this->call("getAlias", 
                        $v = array("id"=>$id), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getAliasesForAccount($target) {
        // target is a ComplexType SipId,
        //refer to wsdl for more info
        $target =& new SOAP_Value('target','{urn:AGProjects:NGNPro}SipId',$target);
        return $this->call("getAliasesForAccount", 
                        $v = array("target"=>$target), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getAliases($query) {
        // query is a ComplexType AliasQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}AliasQuery',$query);
        return $this->call("getAliases", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addToGroup($sipId, $group) {
        return $this->call("addToGroup", 
                        $v = array("sipId"=>$sipId, "group"=>$group), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &removeFromGroup($sipId, $group) {
        return $this->call("removeFromGroup", 
                        $v = array("sipId"=>$sipId, "group"=>$group), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getGroups($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getGroups", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addBalance($sipId, $value) {
        return $this->call("addBalance", 
                        $v = array("sipId"=>$sipId, "value"=>$value), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addBalanceFromVoucher($sipId, $card) {
        // card is a ComplexType PrepaidCard,
        //refer to wsdl for more info
        $card =& new SOAP_Value('card','{urn:AGProjects:NGNPro}PrepaidCard',$card);
        return $this->call("addBalanceFromVoucher", 
                        $v = array("sipId"=>$sipId, "card"=>$card), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getPrepaidStatusNew($sipIds) {
        // sipIds is a ComplexType SipIdArray,
        //refer to wsdl for more info
        $sipIds =& new SOAP_Value('sipIds','{urn:AGProjects:NGNPro}SipIdArray',$sipIds);
        return $this->call("getPrepaidStatus", 
                        $v = array("sipIds"=>$sipIds), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getPrepaidStatus($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getPrepaidStatus", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getCreditHistory($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getCreditHistory", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addPhonebookEntry($sipId, $entry) {
        // entry is a ComplexType PhonebookEntry,
        //refer to wsdl for more info
        $entry =& new SOAP_Value('entry','{urn:AGProjects:NGNPro}PhonebookEntry',$entry);
        return $this->call("addPhonebookEntry", 
                        $v = array("sipId"=>$sipId, "entry"=>$entry), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &updatePhonebookEntry($sipId, $entry) {
        // entry is a ComplexType PhonebookEntry,
        //refer to wsdl for more info
        $entry =& new SOAP_Value('entry','{urn:AGProjects:NGNPro}PhonebookEntry',$entry);
        return $this->call("updatePhonebookEntry", 
                        $v = array("sipId"=>$sipId, "entry"=>$entry), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deletePhonebookEntry($sipId, $uri) {
        return $this->call("deletePhonebookEntry", 
                        $v = array("sipId"=>$sipId, "uri"=>$uri), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getPhonebookEntries($sipId, $match, $range) {
        // range is a ComplexType Range,
        //refer to wsdl for more info
        $range =& new SOAP_Value('range','{urn:AGProjects:NGNPro}Range',$range);
        return $this->call("getPhonebookEntries", 
                        $v = array("sipId"=>$sipId, "match"=>$match, "range"=>$range), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &setRejectMembers($sipId, $members) {
        // members is a ComplexType StringArray,
        //refer to wsdl for more info
        $members =& new SOAP_Value('members','{urn:AGProjects:NGNPro}StringArray',$members);
        return $this->call("setRejectMembers", 
                        $v = array("sipId"=>$sipId, "members"=>$members), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getRejectMembers($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getRejectMembers", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &setAcceptRules($sipId, $rules) {
        // rules is a ComplexType AcceptRules,
        //refer to wsdl for more info
        $rules =& new SOAP_Value('rules','{urn:AGProjects:NGNPro}AcceptRules',$rules);
        return $this->call("setAcceptRules", 
                        $v = array("sipId"=>$sipId, "rules"=>$rules), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getAcceptRules($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getAcceptRules", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &setBarringPrefixes($sipId, $prefixes) {
        // prefixes is a ComplexType StringArray,
        //refer to wsdl for more info
        $prefixes =& new SOAP_Value('prefixes','{urn:AGProjects:NGNPro}StringArray',$prefixes);
        return $this->call("setBarringPrefixes", 
                        $v = array("sipId"=>$sipId, "prefixes"=>$prefixes), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getBarringPrefixes($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getBarringPrefixes", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &setCallDiversions($sipId, $diversions) {
        // diversions is a ComplexType CallDiversions,
        //refer to wsdl for more info
        $diversions =& new SOAP_Value('diversions','{urn:AGProjects:NGNPro}CallDiversions',$diversions);
        return $this->call("setCallDiversions", 
                        $v = array("sipId"=>$sipId, "diversions"=>$diversions), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getCallDiversions($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getCallDiversions", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getCalls($sipId, $query) {
        // query is a ComplexType CallsQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}CallsQuery',$query);
        return $this->call("getCalls", 
                        $v = array("sipId"=>$sipId, "query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getCallStatistics($sipId, $query) {
        // query is a ComplexType CallsQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}CallsQuery',$query);
        return $this->call("getCallStatistics", 
                        $v = array("sipId"=>$sipId, "query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getSipDeviceLocations($sipIds) {
        // sipIds is a ComplexType SipIdArray,
        //refer to wsdl for more info
        $sipIds =& new SOAP_Value('sipIds','{urn:AGProjects:NGNPro}SipIdArray',$sipIds);
        return $this->call("getSipDeviceLocations", 
                        $v = array("sipIds"=>$sipIds), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Sip',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
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
        $filter = new SOAP_Value('filter', '{urn:AGProjects:NGNPro}SipTraceFilter', $filter);
        $result = $this->call('getMediaTrace',
                              $v = array('filter' => $filter),
                              array('namespace' => 'urn:AGProjects:NGNPro:Sip',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}
class WebService_NGNPro_VoicemailPort extends SOAP_Client_Custom
{
    function WebService_NGNPro_VoicemailPort($url)
{
        $this->SOAP_Client_Custom($url, 0);
    }
    function &addAccount($account) {
        // account is a ComplexType VoicemailAccount,
        //refer to wsdl for more info
        $account =& new SOAP_Value('account','{urn:AGProjects:NGNPro}VoicemailAccount',$account);
        return $this->call("addAccount", 
                        $v = array("account"=>$account), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Voicemail',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &updateAccount($account) {
        // account is a ComplexType VoicemailAccount,
        //refer to wsdl for more info
        $account =& new SOAP_Value('account','{urn:AGProjects:NGNPro}VoicemailAccount',$account);
        return $this->call("updateAccount", 
                        $v = array("account"=>$account), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Voicemail',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteAccount($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("deleteAccount", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Voicemail',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getAccount($sipId) {
        // sipId is a ComplexType SipId,
        //refer to wsdl for more info
        $sipId =& new SOAP_Value('sipId','{urn:AGProjects:NGNPro}SipId',$sipId);
        return $this->call("getAccount", 
                        $v = array("sipId"=>$sipId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Voicemail',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &setAnnouncement($sipId, $message) {
        return $this->call("setAnnouncement", 
                        $v = array("sipId"=>$sipId, "message"=>$message), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Voicemail',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
}
class WebService_NGNPro_EnumPort extends SOAP_Client_Custom
{
    function WebService_NGNPro_EnumPort($url)
{
        $this->SOAP_Client_Custom($url, 0);
    }
    function &addRange($range) {
        // range is a ComplexType EnumRange,
        //refer to wsdl for more info
        $range =& new SOAP_Value('range','{urn:AGProjects:NGNPro}EnumRange',$range);
        return $this->call("addRange", 
                        $v = array("range"=>$range), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &updateRange($range) {
        // range is a ComplexType EnumRange,
        //refer to wsdl for more info
        $range =& new SOAP_Value('range','{urn:AGProjects:NGNPro}EnumRange',$range);
        return $this->call("updateRange", 
                        $v = array("range"=>$range), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteRange($range) {
        // range is a ComplexType EnumRangeId,
        //refer to wsdl for more info
        $range =& new SOAP_Value('range','{urn:AGProjects:NGNPro}EnumRangeId',$range);
        return $this->call("deleteRange", 
                        $v = array("range"=>$range), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getRanges($query) {
        // query is a ComplexType EnumRangeQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}EnumRangeQuery',$query);
        return $this->call("getRanges", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addNumber($number) {
        // number is a ComplexType EnumNumber,
        //refer to wsdl for more info
        $number =& new SOAP_Value('number','{urn:AGProjects:NGNPro}EnumNumber',$number);
        return $this->call("addNumber", 
                        $v = array("number"=>$number), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &updateNumber($number) {
        // number is a ComplexType EnumNumber,
        //refer to wsdl for more info
        $number =& new SOAP_Value('number','{urn:AGProjects:NGNPro}EnumNumber',$number);
        return $this->call("updateNumber", 
                        $v = array("number"=>$number), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteNumber($enumId) {
        // enumId is a ComplexType EnumId,
        //refer to wsdl for more info
        $enumId =& new SOAP_Value('enumId','{urn:AGProjects:NGNPro}EnumId',$enumId);
        return $this->call("deleteNumber", 
                        $v = array("enumId"=>$enumId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getNumber($enumId) {
        // enumId is a ComplexType EnumId,
        //refer to wsdl for more info
        $enumId =& new SOAP_Value('enumId','{urn:AGProjects:NGNPro}EnumId',$enumId);
        return $this->call("getNumber", 
                        $v = array("enumId"=>$enumId), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getNumbers($query) {
        // query is a ComplexType EnumQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}EnumQuery',$query);
        return $this->call("getNumbers", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Enum',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
}
class WebService_NGNPro_DnsPort extends SOAP_Client_Custom
{
    function WebService_NGNPro_DnsPort($url)
    {
        $this->SOAP_Client_Custom($url, 0);
    }
    function &addZone($zone)
    {
        // zone is a ComplexType DnsZone,
        // refer to wsdl for more info
        $zone = new SOAP_Value('zone', '{urn:AGProjects:NGNPro}DnsZone', $zone);
        $result = $this->call('addZone',
                              $v = array('zone' => $zone),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateZone($zone)
    {
        // zone is a ComplexType DnsZone,
        // refer to wsdl for more info
        $zone = new SOAP_Value('zone', '{urn:AGProjects:NGNPro}DnsZone', $zone);
        $result = $this->call('updateZone',
                              $v = array('zone' => $zone),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteZone($zone)
    {
        $result = $this->call('deleteZone',
                              $v = array('zone' => $zone),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getZones($query)
    {
        // query is a ComplexType DnsZoneQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}DnsZoneQuery', $query);
        $result = $this->call('getZones',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addRecord($record)
    {
        // record is a ComplexType DnsRecord,
        // refer to wsdl for more info
        $record = new SOAP_Value('record', '{urn:AGProjects:NGNPro}DnsRecord', $record);
        $result = $this->call('addRecord',
                              $v = array('record' => $record),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateRecord($record)
    {
        // record is a ComplexType DnsRecord,
        // refer to wsdl for more info
        $record = new SOAP_Value('record', '{urn:AGProjects:NGNPro}DnsRecord', $record);
        $result = $this->call('updateRecord',
                              $v = array('record' => $record),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteRecord($recordId)
    {
        // recordId is a ComplexType DnsRecordId,
        // refer to wsdl for more info
        $recordId = new SOAP_Value('recordId', '{urn:AGProjects:NGNPro}DnsRecordId', $recordId);
        $result = $this->call('deleteRecord',
                              $v = array('recordId' => $recordId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getRecord($recordId)
    {
        // recordId is a ComplexType DnsRecordId,
        // refer to wsdl for more info
        $recordId = new SOAP_Value('recordId', '{urn:AGProjects:NGNPro}DnsRecordId', $recordId);
        $result = $this->call('getRecord',
                              $v = array('recordId' => $recordId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getRecords($query)
    {
        // query is a ComplexType DnsRecordQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}DnsRecordQuery', $query);
        $result = $this->call('getRecords',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}
class WebService_NGNPro_RatingPort extends SOAP_Client_Custom
{
    function WebService_NGNPro_RatingPort($url)
{
        $this->SOAP_Client_Custom($url, 0);
    }
    function &setEntityProfiles($profiles) {
        // profiles is a ComplexType RatingEntityProfiles,
        //refer to wsdl for more info
        $profiles =& new SOAP_Value('profiles','{urn:AGProjects:NGNPro}RatingEntityProfiles',$profiles);
        return $this->call("setEntityProfiles", 
                        $v = array("profiles"=>$profiles), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Rating',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteEntityProfiles($entity) {
        return $this->call("deleteEntityProfiles", 
                        $v = array("entity"=>$entity), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Rating',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getEntityProfiles($entity) {
        return $this->call("getEntityProfiles", 
                        $v = array("entity"=>$entity), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Rating',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
}
class WebService_NGNPro_CustomerPort extends SOAP_Client_Custom
{
    function WebService_NGNPro_CustomerPort($url)
{
        $this->SOAP_Client_Custom($url, 0);
    }
    function &addAccount($account) {
        // account is a ComplexType CustomerAccount,
        //refer to wsdl for more info
        $account =& new SOAP_Value('account','{urn:AGProjects:NGNPro}CustomerAccount',$account);
        return $this->call("addAccount", 
                        $v = array("account"=>$account), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Customer',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &updateAccount($account) {
        // account is a ComplexType CustomerAccount,
        //refer to wsdl for more info
        $account =& new SOAP_Value('account','{urn:AGProjects:NGNPro}CustomerAccount',$account);
        return $this->call("updateAccount", 
                        $v = array("account"=>$account), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Customer',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteAccount($id) {
        return $this->call("deleteAccount", 
                        $v = array("id"=>$id), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Customer',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getAccount($id) {
        return $this->call("getAccount", 
                        $v = array("id"=>$id), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Customer',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getCustomers($query) {
        // query is a ComplexType CustomerQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}CustomerQuery',$query);
        return $this->call("getCustomers", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Customer',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getResellers($query) {
        // query is a ComplexType CustomerQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}CustomerQuery',$query);
        return $this->call("getResellers", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Customer',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'' ));
    }
    function &setProperties($customer, $properties) {
        // properties is a ComplexType CustomerPropertyArray,
        //refer to wsdl for more info
        $properties =& new SOAP_Value('properties','{urn:AGProjects:NGNPro}CustomerPropertyArray',$properties);
        return $this->call("setProperties", 
                        $v = array("customer"=>$customer, "properties"=>$properties), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Customer',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getProperties($customer) {
        return $this->call("getProperties", 
                        $v = array("customer"=>$customer), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Customer',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
}
class WebService_NGNPro_NetworkPort extends SOAP_Client_Custom
{
    function WebService_NGNPro_NetworkPort($url)
{
        $this->SOAP_Client_Custom($url, 0);
    }
    function &addGateway($gateway) {
        // gateway is a ComplexType Gateway,
        //refer to wsdl for more info
        $gateway =& new SOAP_Value('gateway','{urn:AGProjects:NGNPro}Gateway',$gateway);
        return $this->call("addGateway", 
                        $v = array("gateway"=>$gateway), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteGateway($name) {
        return $this->call("deleteGateway", 
                        $v = array("name"=>$name), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getGateways($query) {
        // query is a ComplexType GatewayQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}GatewayQuery',$query);
        return $this->call("getGateways", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addGatewayGroup($group) {
        return $this->call("addGatewayGroup", 
                        $v = array("group"=>$group), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteGatewayGroup($group) {
        return $this->call("deleteGatewayGroup", 
                        $v = array("group"=>$group), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getGatewayGroups($query) {
        // query is a ComplexType GatewayGroupQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}GatewayGroupQuery',$query);
        return $this->call("getGatewayGroups", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &addRoutes($routes) {
        // routes is a ComplexType RouteArray,
        //refer to wsdl for more info
        $routes =& new SOAP_Value('routes','{urn:AGProjects:NGNPro}RouteArray',$routes);
        return $this->call("addRoutes", 
                        $v = array("routes"=>$routes), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deleteRoutes($routes) {
        // routes is a ComplexType RouteArray,
        //refer to wsdl for more info
        $routes =& new SOAP_Value('routes','{urn:AGProjects:NGNPro}RouteArray',$routes);
        return $this->call("deleteRoutes", 
                        $v = array("routes"=>$routes), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getRoutes($query) {
        // query is a ComplexType RouteQuery,
        //refer to wsdl for more info
        $query =& new SOAP_Value('query','{urn:AGProjects:NGNPro}RouteQuery',$query);
        return $this->call("getRoutes", 
                        $v = array("query"=>$query), 
                        array('namespace'=>'urn:AGProjects:NGNPro:Network',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
}
class WebService_SoapSIMPLEProxy_PresencePort extends SOAP_Client_Custom
{
    function WebService_SoapSIMPLEProxy_PresencePort($url)
{
        $this->SOAP_Client_Custom($url, 0);
    }
    function &setPresenceInformation($sipId, $password, $information) {
        // information is a ComplexType PresenceInformation,
        //refer to wsdl for more info
        $information =& new SOAP_Value('information','{urn:AGProjects:SoapSIMPLEProxy}PresenceInformation',$information);
        return $this->call("setPresenceInformation", 
                        $v = array("sipId"=>$sipId, "password"=>$password, "information"=>$information), 
                        array('namespace'=>'urn:AGProjects:SoapSIMPLEProxy:Presence',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getPresenceInformation($sipId, $password) {
        return $this->call("getPresenceInformation", 
                        $v = array("sipId"=>$sipId, "password"=>$password), 
                        array('namespace'=>'urn:AGProjects:SoapSIMPLEProxy:Presence',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &deletePresenceInformation($sipId, $password) {
        return $this->call("deletePresenceInformation", 
                        $v = array("sipId"=>$sipId, "password"=>$password), 
                        array('namespace'=>'urn:AGProjects:SoapSIMPLEProxy:Presence',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getWatchers($sipId, $password) {
        return $this->call("getWatchers", 
                        $v = array("sipId"=>$sipId, "password"=>$password), 
                        array('namespace'=>'urn:AGProjects:SoapSIMPLEProxy:Presence',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &setPolicy($sipId, $password, $policy) {
        // policy is a ComplexType PresencePolicy,
        //refer to wsdl for more info
        $policy =& new SOAP_Value('policy','{urn:AGProjects:SoapSIMPLEProxy}PresencePolicy',$policy);
        return $this->call("setPolicy", 
                        $v = array("sipId"=>$sipId, "password"=>$password, "policy"=>$policy), 
                        array('namespace'=>'urn:AGProjects:SoapSIMPLEProxy:Presence',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
    function &getPolicy($sipId, $password) {
        return $this->call("getPolicy", 
                        $v = array("sipId"=>$sipId, "password"=>$password), 
                        array('namespace'=>'urn:AGProjects:SoapSIMPLEProxy:Presence',
                            'soapaction'=>'',
                            'style'=>'rpc',
                            'use'=>'encoded' ));
    }
}
?>
