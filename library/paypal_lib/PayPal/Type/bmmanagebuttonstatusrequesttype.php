<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * BMManageButtonStatusRequestType
 *
 * @package PayPal
 */
class BMManageButtonStatusRequestType extends AbstractRequestType
{
    /**
     * Button ID of Hosted button.
     */
    var $HostedButtonID;

    /**
     * Requested Status change for hosted button.
     */
    var $ButtonStatus;

    function BMManageButtonStatusRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'HostedButtonID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonStatus' => 
              array (
                'required' => false,
                'type' => 'ButtonStatusType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getHostedButtonID()
    {
        return $this->HostedButtonID;
    }
    function setHostedButtonID($HostedButtonID, $charset = 'iso-8859-1')
    {
        $this->HostedButtonID = $HostedButtonID;
        $this->_elements['HostedButtonID']['charset'] = $charset;
    }
    function getButtonStatus()
    {
        return $this->ButtonStatus;
    }
    function setButtonStatus($ButtonStatus, $charset = 'iso-8859-1')
    {
        $this->ButtonStatus = $ButtonStatus;
        $this->_elements['ButtonStatus']['charset'] = $charset;
    }
}
