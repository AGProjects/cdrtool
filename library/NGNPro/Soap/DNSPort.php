<?php

class WebService_NGNPro_DnsPort extends SOAP_Client_Custom
{
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
    function &getZone($zone)
    {
        $result = $this->call('getZone',
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
    function &addFancyRecord($record)
    {
        // record is a ComplexType DnsFancyRecord,
        // refer to wsdl for more info
        $record = new SOAP_Value('record', '{urn:AGProjects:NGNPro}DnsFancyRecord', $record);
        $result = $this->call('addFancyRecord',
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
    function &updateFancyRecord($record)
    {
        // record is a ComplexType DnsFancyRecord,
        // refer to wsdl for more info
        $record = new SOAP_Value('record', '{urn:AGProjects:NGNPro}DnsFancyRecord', $record);
        $result = $this->call('updateFancyRecord',
                              $v = array('record' => $record),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteRecord($recordId)
    {
        $result = $this->call('deleteRecord',
                              $v = array('recordId' => $recordId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteFancyRecord($recordId)
    {
        $result = $this->call('deleteFancyRecord',
                              $v = array('recordId' => $recordId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getRecord($recordId)
    {
        $result = $this->call('getRecord',
                              $v = array('recordId' => $recordId),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getFancyRecord($recordId)
    {
        $result = $this->call('getFancyRecord',
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
    function &getFancyRecords($query)
    {
        // query is a ComplexType DnsFancyRecordQuery,
        // refer to wsdl for more info
        $query = new SOAP_Value('query', '{urn:AGProjects:NGNPro}DnsFancyRecordQuery', $query);
        $result = $this->call('getFancyRecords',
                              $v = array('query' => $query),
                              array('namespace' => 'urn:AGProjects:NGNPro:Dns',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}

