<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * GetBillingAgreementCustomerDetailsResponseDetailsType
 *
 * @package PayPal
 */
class GetBillingAgreementCustomerDetailsResponseDetailsType extends XSDSimpleType
{
    var $PayerInfo;

    /**
     * Customer's billing address.
     */
    var $BillingAddress;

    function GetBillingAgreementCustomerDetailsResponseDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PayerInfo' => 
              array (
                'required' => true,
                'type' => 'PayerInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAddress' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPayerInfo()
    {
        return $this->PayerInfo;
    }
    function setPayerInfo($PayerInfo, $charset = 'iso-8859-1')
    {
        $this->PayerInfo = $PayerInfo;
        $this->_elements['PayerInfo']['charset'] = $charset;
    }
    function getBillingAddress()
    {
        return $this->BillingAddress;
    }
    function setBillingAddress($BillingAddress, $charset = 'iso-8859-1')
    {
        $this->BillingAddress = $BillingAddress;
        $this->_elements['BillingAddress']['charset'] = $charset;
    }
}
