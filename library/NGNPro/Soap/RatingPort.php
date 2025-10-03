<?php

class WebService_NGNPro_RatingPort extends SOAP_Client_Custom
{
    function &setEntityProfiles($profiles)
    {
        // profiles is a ComplexType RatingEntityProfiles,
        // refer to wsdl for more info
        $profiles = new SOAP_Value('profiles', '{urn:AGProjects:NGNPro}RatingEntityProfiles', $profiles);
        $result = $this->call('setEntityProfiles',
                              $v = array('profiles' => $profiles),
                              array('namespace' => 'urn:AGProjects:NGNPro:Rating',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &deleteEntityProfiles($entity)
    {
        $result = $this->call('deleteEntityProfiles',
                              $v = array('entity' => $entity),
                              array('namespace' => 'urn:AGProjects:NGNPro:Rating',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
    function &getEntityProfiles($entity)
    {
        $result = $this->call('getEntityProfiles',
                              $v = array('entity' => $entity),
                              array('namespace' => 'urn:AGProjects:NGNPro:Rating',
                                    'soapaction' => '',
                                    'style' => 'rpc',
                                    'use' => 'encoded'));
        return $result;
    }
}
