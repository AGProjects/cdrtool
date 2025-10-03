<?php

class WebService_NGNPro_EnumPort extends SOAP_Client_Custom
{
    function &addRange($range)
    {
        // range is a ComplexType EnumRange,
        // refer to wsdl for more info
        $range = new SOAP_Value('range', '{urn:AGProjects:NGNPro}EnumRange', $range);
        $result = $this->call('addRange',
                              $v = array('range' => $range),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateRange($range)
    {
        // range is a ComplexType EnumRange,
        // refer to wsdl for more info
        $range = new SOAP_Value('range', '{urn:AGProjects:NGNPro}EnumRange', $range);
        $result = $this->call('updateRange',
                              $v = array('range' => $range),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteRange($range)
    {
        // range is a ComplexType EnumRangeId,
        // refer to wsdl for more info
        $range = new SOAP_Value('range', '{urn:AGProjects:NGNPro}EnumRangeId', $range);
        $result = $this->call('deleteRange',
                              $v = array('range' => $range),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getRanges($query)
    {
        // query is a ComplexType EnumRangeQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}EnumRangeQuery', $query);
        $result = $this->call('getRanges',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &addNumber($number)
    {
        // number is a ComplexType EnumNumber,
        // refer to wsdl for more info
        $number = new SOAP_Value('number', '{urn:AGProjects:NGNPro}EnumNumber', $number);
        $result = $this->call('addNumber',
                              $v = array('number' => $number),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateNumber($number)
    {
        // number is a ComplexType EnumNumber,
        // refer to wsdl for more info
        $number = new SOAP_Value('number', '{urn:AGProjects:NGNPro}EnumNumber', $number);
        $result = $this->call('updateNumber',
                              $v = array('number' => $number),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteNumber($enumId)
    {
        // enumId is a ComplexType EnumId,
        // refer to wsdl for more info
        $enumId = new SOAP_Value('enumId', '{urn:AGProjects:NGNPro}EnumId', $enumId);
        $result = $this->call('deleteNumber',
                              $v = array('enumId' => $enumId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getNumber($enumId)
    {
        // enumId is a ComplexType EnumId,
        // refer to wsdl for more info
        $enumId = new SOAP_Value('enumId', '{urn:AGProjects:NGNPro}EnumId', $enumId);
        $result = $this->call('getNumber',
                              $v = array('enumId' => $enumId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getNumbers($query)
    {
        // query is a ComplexType EnumNumberQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}EnumNumberQuery', $query);
        $result = $this->call('getNumbers',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Enum',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}
