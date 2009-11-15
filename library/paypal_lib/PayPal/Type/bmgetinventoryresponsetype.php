<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * BMGetInventoryResponseType
 *
 * @package PayPal
 */
class BMGetInventoryResponseType extends AbstractResponseType
{
    var $HostedButtonID;

    var $TrackInv;

    var $TrackPnl;

    var $ItemTrackingDetails;

    var $OptionIndex;

    var $OptionName;

    var $OptionTrackingDetails;

    var $SoldoutURL;

    function BMGetInventoryResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'HostedButtonID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TrackInv' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TrackPnl' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ItemTrackingDetails' => 
              array (
                'required' => false,
                'type' => 'ItemTrackingDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OptionIndex' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'OptionName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'OptionTrackingDetails' => 
              array (
                'required' => false,
                'type' => 'OptionTrackingDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SoldoutURL' => 
              array (
                'required' => false,
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
    function getTrackInv()
    {
        return $this->TrackInv;
    }
    function setTrackInv($TrackInv, $charset = 'iso-8859-1')
    {
        $this->TrackInv = $TrackInv;
        $this->_elements['TrackInv']['charset'] = $charset;
    }
    function getTrackPnl()
    {
        return $this->TrackPnl;
    }
    function setTrackPnl($TrackPnl, $charset = 'iso-8859-1')
    {
        $this->TrackPnl = $TrackPnl;
        $this->_elements['TrackPnl']['charset'] = $charset;
    }
    function getItemTrackingDetails()
    {
        return $this->ItemTrackingDetails;
    }
    function setItemTrackingDetails($ItemTrackingDetails, $charset = 'iso-8859-1')
    {
        $this->ItemTrackingDetails = $ItemTrackingDetails;
        $this->_elements['ItemTrackingDetails']['charset'] = $charset;
    }
    function getOptionIndex()
    {
        return $this->OptionIndex;
    }
    function setOptionIndex($OptionIndex, $charset = 'iso-8859-1')
    {
        $this->OptionIndex = $OptionIndex;
        $this->_elements['OptionIndex']['charset'] = $charset;
    }
    function getOptionName()
    {
        return $this->OptionName;
    }
    function setOptionName($OptionName, $charset = 'iso-8859-1')
    {
        $this->OptionName = $OptionName;
        $this->_elements['OptionName']['charset'] = $charset;
    }
    function getOptionTrackingDetails()
    {
        return $this->OptionTrackingDetails;
    }
    function setOptionTrackingDetails($OptionTrackingDetails, $charset = 'iso-8859-1')
    {
        $this->OptionTrackingDetails = $OptionTrackingDetails;
        $this->_elements['OptionTrackingDetails']['charset'] = $charset;
    }
    function getSoldoutURL()
    {
        return $this->SoldoutURL;
    }
    function setSoldoutURL($SoldoutURL, $charset = 'iso-8859-1')
    {
        $this->SoldoutURL = $SoldoutURL;
        $this->_elements['SoldoutURL']['charset'] = $charset;
    }
}
