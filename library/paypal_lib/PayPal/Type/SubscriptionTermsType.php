<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SubscriptionTermsType
 * 
 * SubscriptionTermsType Terms of a PayPal subscription.
 *
 * @package PayPal
 */
class SubscriptionTermsType extends XSDSimpleType
{
    var $Amount;

    function SubscriptionTermsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
        $this->_attributes = array_merge($this->_attributes,
            array (
              'period' => 
              array (
                'name' => 'period',
                'type' => 'xs:string',
                'use' => 'required',
              ),
            ));
    }

    function getAmount()
    {
        return $this->Amount;
    }
    function setAmount($Amount, $charset = 'iso-8859-1')
    {
        $this->Amount = $Amount;
        $this->_elements['Amount']['charset'] = $charset;
    }
}
