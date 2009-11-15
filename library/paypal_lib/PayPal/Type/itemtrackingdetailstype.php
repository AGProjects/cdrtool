<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ItemTrackingDetailsType
 *
 * @package PayPal
 */
class ItemTrackingDetailsType extends XSDSimpleType
{
    /**
     * Item Number.
     */
    var $ItemNumber;

    /**
     * Option Quantity.
     */
    var $ItemQty;

    /**
     * Item Quantity Delta.
     */
    var $ItemQtyDelta;

    /**
     * Item Alert.
     */
    var $ItemAlert;

    /**
     * Item Cost.
     */
    var $ItemCost;

    function ItemTrackingDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ItemNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemQty' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemQtyDelta' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemAlert' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemCost' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getItemNumber()
    {
        return $this->ItemNumber;
    }
    function setItemNumber($ItemNumber, $charset = 'iso-8859-1')
    {
        $this->ItemNumber = $ItemNumber;
        $this->_elements['ItemNumber']['charset'] = $charset;
    }
    function getItemQty()
    {
        return $this->ItemQty;
    }
    function setItemQty($ItemQty, $charset = 'iso-8859-1')
    {
        $this->ItemQty = $ItemQty;
        $this->_elements['ItemQty']['charset'] = $charset;
    }
    function getItemQtyDelta()
    {
        return $this->ItemQtyDelta;
    }
    function setItemQtyDelta($ItemQtyDelta, $charset = 'iso-8859-1')
    {
        $this->ItemQtyDelta = $ItemQtyDelta;
        $this->_elements['ItemQtyDelta']['charset'] = $charset;
    }
    function getItemAlert()
    {
        return $this->ItemAlert;
    }
    function setItemAlert($ItemAlert, $charset = 'iso-8859-1')
    {
        $this->ItemAlert = $ItemAlert;
        $this->_elements['ItemAlert']['charset'] = $charset;
    }
    function getItemCost()
    {
        return $this->ItemCost;
    }
    function setItemCost($ItemCost, $charset = 'iso-8859-1')
    {
        $this->ItemCost = $ItemCost;
        $this->_elements['ItemCost']['charset'] = $charset;
    }
}
