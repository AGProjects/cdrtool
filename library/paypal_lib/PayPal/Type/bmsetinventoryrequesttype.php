<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * BMSetInventoryRequestType
 *
 * @package PayPal
 */
class BMSetInventoryRequestType extends AbstractRequestType
{
    /**
     * Hosted Button ID of button you wish to change.
     */
    var $HostedButtonID;

    /**
     * Is Inventory tracked.
     */
    var $TrackInv;

    /**
     * Is PNL Tracked.
     */
    var $TrackPnl;

    var $ItemTrackingDetails;

    /**
     * Option Index.
     */
    var $OptionIndex;

    var $OptionTrackingDetails;

    /**
     * URL of page to display when an item is soldout.
     */
    var $SoldoutURL;

    function BMSetInventoryRequestType()
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
