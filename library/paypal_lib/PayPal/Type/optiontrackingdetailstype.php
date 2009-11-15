<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * OptionTrackingDetailsType
 *
 * @package PayPal
 */
class OptionTrackingDetailsType extends XSDSimpleType
{
    /**
     * Option Number.
     */
    var $OptionNumber;

    /**
     * Option Quantity.
     */
    var $OptionQty;

    /**
     * Option Select Name.
     */
    var $OptionSelect;

    /**
     * Option Quantity Delta.
     */
    var $OptionQtyDelta;

    /**
     * Option Alert.
     */
    var $OptionAlert;

    /**
     * Option Cost.
     */
    var $OptionCost;

    function OptionTrackingDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'OptionNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OptionQty' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OptionSelect' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OptionQtyDelta' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OptionAlert' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OptionCost' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getOptionNumber()
    {
        return $this->OptionNumber;
    }
    function setOptionNumber($OptionNumber, $charset = 'iso-8859-1')
    {
        $this->OptionNumber = $OptionNumber;
        $this->_elements['OptionNumber']['charset'] = $charset;
    }
    function getOptionQty()
    {
        return $this->OptionQty;
    }
    function setOptionQty($OptionQty, $charset = 'iso-8859-1')
    {
        $this->OptionQty = $OptionQty;
        $this->_elements['OptionQty']['charset'] = $charset;
    }
    function getOptionSelect()
    {
        return $this->OptionSelect;
    }
    function setOptionSelect($OptionSelect, $charset = 'iso-8859-1')
    {
        $this->OptionSelect = $OptionSelect;
        $this->_elements['OptionSelect']['charset'] = $charset;
    }
    function getOptionQtyDelta()
    {
        return $this->OptionQtyDelta;
    }
    function setOptionQtyDelta($OptionQtyDelta, $charset = 'iso-8859-1')
    {
        $this->OptionQtyDelta = $OptionQtyDelta;
        $this->_elements['OptionQtyDelta']['charset'] = $charset;
    }
    function getOptionAlert()
    {
        return $this->OptionAlert;
    }
    function setOptionAlert($OptionAlert, $charset = 'iso-8859-1')
    {
        $this->OptionAlert = $OptionAlert;
        $this->_elements['OptionAlert']['charset'] = $charset;
    }
    function getOptionCost()
    {
        return $this->OptionCost;
    }
    function setOptionCost($OptionCost, $charset = 'iso-8859-1')
    {
        $this->OptionCost = $OptionCost;
        $this->_elements['OptionCost']['charset'] = $charset;
    }
}
