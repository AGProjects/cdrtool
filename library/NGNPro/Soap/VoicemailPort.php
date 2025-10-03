<?php

class WebService_NGNPro_VoicemailPort extends SOAP_Client_Custom
{
    function &addAccount($account)
    {
        // account is a ComplexType VoicemailAccount,
        // refer to wsdl for more info
        $account = new SOAP_Value('account', '{urn:AGProjects:NGNPro}VoicemailAccount', $account);
        $result = $this->call('addAccount',
                              $v = array('account' => $account),
                              array('namespace' => 'urn:AGProjects:NGNPro:Voicemail',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &updateAccount($account)
    {
        // account is a ComplexType VoicemailAccount,
        // refer to wsdl for more info
        $account = new SOAP_Value('account', '{urn:AGProjects:NGNPro}VoicemailAccount', $account);
        $result = $this->call('updateAccount',
                              $v = array('account' => $account),
                              array('namespace' => 'urn:AGProjects:NGNPro:Voicemail',
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
                              array('namespace' => 'urn:AGProjects:NGNPro:Voicemail',
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
                              array('namespace' => 'urn:AGProjects:NGNPro:Voicemail',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &setAnnouncement($sipId, $message)
    {
        // sipId is a ComplexType SipId,
        // refer to wsdl for more info
        $sipId = new SOAP_Value('sipId', '{urn:AGProjects:NGNPro}SipId', $sipId);
        $result = $this->call('setAnnouncement',
                              $v = array('sipId' => $sipId, 'message' => $message),
                              array('namespace' => 'urn:AGProjects:NGNPro:Voicemail',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}

