<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * BMGetInventoryRequestType
 *
 * @package PayPal
 */
class BMGetInventoryRequestType extends AbstractRequestType
{
    /**
     * Hosted Button ID of the button to return inventory for.
     */
    var $HostedButtonID;

    function BMGetInventoryRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'HostedButtonID' => 
              array (
                'required' => true,
                'type' => 'string',
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
}
