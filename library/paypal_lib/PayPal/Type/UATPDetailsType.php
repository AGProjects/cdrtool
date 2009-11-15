<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * UATPDetailsType
 * 
 * UATP Card Details Type
 *
 * @package PayPal
 */
class UATPDetailsType extends XSDSimpleType
{
    /**
     * UATP Card Number
     */
    var $UATPNumber;

    /**
     * UATP Card expirty month
     */
    var $ExpMonth;

    /**
     * UATP Card expirty year
     */
    var $ExpYear;

    function UATPDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'UATPNumber' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExpMonth' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExpYear' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getUATPNumber()
    {
        return $this->UATPNumber;
    }
    function setUATPNumber($UATPNumber, $charset = 'iso-8859-1')
    {
        $this->UATPNumber = $UATPNumber;
        $this->_elements['UATPNumber']['charset'] = $charset;
    }
    function getExpMonth()
    {
        return $this->ExpMonth;
    }
    function setExpMonth($ExpMonth, $charset = 'iso-8859-1')
    {
        $this->ExpMonth = $ExpMonth;
        $this->_elements['ExpMonth']['charset'] = $charset;
    }
    function getExpYear()
    {
        return $this->ExpYear;
    }
    function setExpYear($ExpYear, $charset = 'iso-8859-1')
    {
        $this->ExpYear = $ExpYear;
        $this->_elements['ExpYear']['charset'] = $charset;
    }
}
