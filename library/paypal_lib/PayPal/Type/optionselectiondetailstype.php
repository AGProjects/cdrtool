<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * OptionSelectionDetailsType
 *
 * @package PayPal
 */
class OptionSelectionDetailsType extends XSDSimpleType
{
    /**
     * Option Selection.
     */
    var $OptionSelection;

    /**
     * Option Price.
     */
    var $Price;

    function OptionSelectionDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'OptionSelection' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Price' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getOptionSelection()
    {
        return $this->OptionSelection;
    }
    function setOptionSelection($OptionSelection, $charset = 'iso-8859-1')
    {
        $this->OptionSelection = $OptionSelection;
        $this->_elements['OptionSelection']['charset'] = $charset;
    }
    function getPrice()
    {
        return $this->Price;
    }
    function setPrice($Price, $charset = 'iso-8859-1')
    {
        $this->Price = $Price;
        $this->_elements['Price']['charset'] = $charset;
    }
}
