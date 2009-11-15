<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ReviseStatusType
 * 
 * Contains the revise status information details (e.g., item properties
 * information). ths node contains system set data only - always output and always
 * all data. no minccurs needed, except for motors specific data, since it wil lnot
 * be retruned for non motors items
 *
 * @package PayPal
 */
class ReviseStatusType extends XSDSimpleType
{
    /**
     * Indicates whether the item was revised since the auction started.
     */
    var $ItemRevised;

    /**
     * If true, indicates that a Buy It Now Price was added for the item. Only returned
     * for Motors items.
     */
    var $BuyItNowAdded;

    /**
     * Replaces BinLowered as of API version 305. If true, indicates that the Buy It
     * Now Price was lowered for the item. Only returned for Motors items.
     */
    var $BuyItNowLowered;

    /**
     * If true, indicates that the Reserve Price was lowered for the item. Only
     * returned for Motors items.
     */
    var $ReserveLowered;

    /**
     * If true, indicates that the Reserve Price was removed from the item. Only
     * returned for eBay Motors items.
     */
    var $ReserveRemoved;

    function ReviseStatusType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ItemRevised' => 
              array (
                'required' => true,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyItNowAdded' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyItNowLowered' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReserveLowered' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReserveRemoved' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getItemRevised()
    {
        return $this->ItemRevised;
    }
    function setItemRevised($ItemRevised, $charset = 'iso-8859-1')
    {
        $this->ItemRevised = $ItemRevised;
        $this->_elements['ItemRevised']['charset'] = $charset;
    }
    function getBuyItNowAdded()
    {
        return $this->BuyItNowAdded;
    }
    function setBuyItNowAdded($BuyItNowAdded, $charset = 'iso-8859-1')
    {
        $this->BuyItNowAdded = $BuyItNowAdded;
        $this->_elements['BuyItNowAdded']['charset'] = $charset;
    }
    function getBuyItNowLowered()
    {
        return $this->BuyItNowLowered;
    }
    function setBuyItNowLowered($BuyItNowLowered, $charset = 'iso-8859-1')
    {
        $this->BuyItNowLowered = $BuyItNowLowered;
        $this->_elements['BuyItNowLowered']['charset'] = $charset;
    }
    function getReserveLowered()
    {
        return $this->ReserveLowered;
    }
    function setReserveLowered($ReserveLowered, $charset = 'iso-8859-1')
    {
        $this->ReserveLowered = $ReserveLowered;
        $this->_elements['ReserveLowered']['charset'] = $charset;
    }
    function getReserveRemoved()
    {
        return $this->ReserveRemoved;
    }
    function setReserveRemoved($ReserveRemoved, $charset = 'iso-8859-1')
    {
        $this->ReserveRemoved = $ReserveRemoved;
        $this->_elements['ReserveRemoved']['charset'] = $charset;
    }
}
