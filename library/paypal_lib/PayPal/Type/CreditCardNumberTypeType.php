<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CreditCardNumberTypeType
 *
 * @package PayPal
 */
class CreditCardNumberTypeType extends XSDSimpleType
{
    var $CreditCardType;

    var $CreditCardNumber;

    function CreditCardNumberTypeType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'CreditCardType' => 
              array (
                'required' => false,
                'type' => 'CreditCardTypeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreditCardNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCreditCardType()
    {
        return $this->CreditCardType;
    }
    function setCreditCardType($CreditCardType, $charset = 'iso-8859-1')
    {
        $this->CreditCardType = $CreditCardType;
        $this->_elements['CreditCardType']['charset'] = $charset;
    }
    function getCreditCardNumber()
    {
        return $this->CreditCardNumber;
    }
    function setCreditCardNumber($CreditCardNumber, $charset = 'iso-8859-1')
    {
        $this->CreditCardNumber = $CreditCardNumber;
        $this->_elements['CreditCardNumber']['charset'] = $charset;
    }
}
