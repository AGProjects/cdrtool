<?php

class WebService_SoapSIMPLEProxy_PresencePort extends SOAP_Client_Custom
{
    function &setPresenceInformation($sipId, $password, $information)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:SoapSIMPLEProxy}SipId', $sipId);
        // information is a ComplexType PresenceInformation,
        // refer to wsdl for more info
        $information = new SOAP_Value('information', '{urn:AGProjects:SoapSIMPLEProxy}PresenceInformation', $information);
        $result = $this->call('setPresenceInformation',
                              $v = array('sipId' => $sipId, 'password' => $password, 'information' => $information),
                              array('namespace' => 'urn:AGProjects:SoapSIMPLEProxy:Presence',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getPresenceInformation($sipId, $password)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:SoapSIMPLEProxy}SipId', $sipId);
        $result = $this->call('getPresenceInformation',
                              $v = array('sipId' => $sipId, 'password' => $password),
                              array('namespace' => 'urn:AGProjects:SoapSIMPLEProxy:Presence',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deletePresenceInformation($sipId, $password)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:SoapSIMPLEProxy}SipId', $sipId);
        $result = $this->call('deletePresenceInformation',
                              $v = array('sipId' => $sipId, 'password' => $password),
                              array('namespace' => 'urn:AGProjects:SoapSIMPLEProxy:Presence',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getWatchers($sipId, $password)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:SoapSIMPLEProxy}SipId', $sipId);
        $result = $this->call('getWatchers',
                              $v = array('sipId' => $sipId, 'password' => $password),
                              array('namespace' => 'urn:AGProjects:SoapSIMPLEProxy:Presence',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &setPolicy($sipId, $password, $policy)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:SoapSIMPLEProxy}SipId', $sipId);
        // policy is a ComplexType PresencePolicy,
        // refer to wsdl for more info
        $policy = new SOAP_Value('policy', '{urn:AGProjects:SoapSIMPLEProxy}PresencePolicy', $policy);
        $result = $this->call('setPolicy',
                              $v = array('sipId' => $sipId, 'password' => $password, 'policy' => $policy),
                              array('namespace' => 'urn:AGProjects:SoapSIMPLEProxy:Presence',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getPolicy($sipId, $password)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:SoapSIMPLEProxy}SipId', $sipId);
        $result = $this->call('getPolicy',
                              $v = array('sipId' => $sipId, 'password' => $password),
                              array('namespace' => 'urn:AGProjects:SoapSIMPLEProxy:Presence',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}
