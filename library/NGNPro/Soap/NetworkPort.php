<?php

class WebService_NGNPro_NetworkPort extends SOAP_Client_Custom
{
    function &getStatistics()
    {
        $result = $this->call('getStatistics',
                              $v = null,
                              array('namespace' => 'urn:AGProjects:NGNPro:Network',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getStatus()
    {
        $result = $this->call('getStatus',
                              $v = null,
                              array('namespace' => 'urn:AGProjects:NGNPro:Network',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}

