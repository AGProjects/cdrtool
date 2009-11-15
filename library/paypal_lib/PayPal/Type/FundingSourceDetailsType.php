<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * FundingSourceDetailsType
 *
 * @package PayPal
 */
class FundingSourceDetailsType extends XSDSimpleType
{
    /**
     * Allowable values: 0,1
     */
    var $AllowPushFunding;

    /**
     * This is the payment Solution what merchant perfers, instant pay or default
     */
    var $AllowedPaymentMethod;

    function FundingSourceDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AllowPushFunding' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AllowedPaymentMethod' => 
              array (
                'required' => false,
                'type' => 'AllowedPaymentMethodType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAllowPushFunding()
    {
        return $this->AllowPushFunding;
    }
    function setAllowPushFunding($AllowPushFunding, $charset = 'iso-8859-1')
    {
        $this->AllowPushFunding = $AllowPushFunding;
        $this->_elements['AllowPushFunding']['charset'] = $charset;
    }
    function getAllowedPaymentMethod()
    {
        return $this->AllowedPaymentMethod;
    }
    function setAllowedPaymentMethod($AllowedPaymentMethod, $charset = 'iso-8859-1')
    {
        $this->AllowedPaymentMethod = $AllowedPaymentMethod;
        $this->_elements['AllowedPaymentMethod']['charset'] = $charset;
    }
}
