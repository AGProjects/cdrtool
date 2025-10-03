<?php

class WebService_NGNPro_CustomerPort extends SOAP_Client_Custom
{
    function &addAccount($account)
    {
        // account is a ComplexType CustomerAccount,
        // refer to wsdl for more info
        $account = new SOAP_Value('account', '{urn:AGProjects:NGNPro}CustomerAccount', $account);
        $result = $this->call('addAccount',
                              $v = array('account' => $account),
                              array('namespace' => 'urn:AGProjects:NGNPro:Customer',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateAccount($account)
    {
        // account is a ComplexType CustomerAccount,
        // refer to wsdl for more info
        $account = new SOAP_Value('account', '{urn:AGProjects:NGNPro}CustomerAccount', $account);
        $result = $this->call('updateAccount',
                              $v = array('account' => $account),
                              array('namespace' => 'urn:AGProjects:NGNPro:Customer',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteAccount($id)
    {
        $result = $this->call('deleteAccount',
                              $v = array('id' => $id),
                              array('namespace' => 'urn:AGProjects:NGNPro:Customer',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getAccount($id)
    {
        $result = $this->call('getAccount',
                              $v = array('id' => $id),
                              array('namespace' => 'urn:AGProjects:NGNPro:Customer',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getCustomers($query)
    {
        // query is a ComplexType CustomerQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}CustomerQuery', $query);
        $result = $this->call('getCustomers',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Customer',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getResellers($query)
    {
        // query is a ComplexType CustomerQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}CustomerQuery', $query);
        $result = $this->call('getResellers',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Customer',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &setProperties($customer, $properties)
    {
        // properties is a ComplexType CustomerPropertyArray,
        // refer to wsdl for more info
        $properties = new SOAP_Value('properties', '{urn:AGProjects:NGNPro}CustomerPropertyArray', $properties);
        $result = $this->call('setProperties',
                              $v = array('customer' => $customer, 'properties' => $properties),
                              array('namespace' => 'urn:AGProjects:NGNPro:Customer',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getProperties($customer)
    {
        $result = $this->call('getProperties',
                              $v = array('customer' => $customer),
                              array('namespace' => 'urn:AGProjects:NGNPro:Customer',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}
