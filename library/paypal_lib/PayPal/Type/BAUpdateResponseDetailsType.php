<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * BAUpdateResponseDetailsType
 *
 * @package PayPal
 */
class BAUpdateResponseDetailsType extends XSDSimpleType
{
    var $BillingAgreementID;

    var $BillingAgreementDescription;

    var $BillingAgreementStatus;

    var $BillingAgreementCustom;

    var $PayerInfo;

    var $BillingAgreementMax;

    function BAUpdateResponseDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BillingAgreementID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAgreementDescription' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAgreementStatus' => 
              array (
                'required' => true,
                'type' => 'MerchantPullStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAgreementCustom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerInfo' => 
              array (
                'required' => true,
                'type' => 'PayerInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAgreementMax' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBillingAgreementID()
    {
        return $this->BillingAgreementID;
    }
    function setBillingAgreementID($BillingAgreementID, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementID = $BillingAgreementID;
        $this->_elements['BillingAgreementID']['charset'] = $charset;
    }
    function getBillingAgreementDescription()
    {
        return $this->BillingAgreementDescription;
    }
    function setBillingAgreementDescription($BillingAgreementDescription, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementDescription = $BillingAgreementDescription;
        $this->_elements['BillingAgreementDescription']['charset'] = $charset;
    }
    function getBillingAgreementStatus()
    {
        return $this->BillingAgreementStatus;
    }
    function setBillingAgreementStatus($BillingAgreementStatus, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementStatus = $BillingAgreementStatus;
        $this->_elements['BillingAgreementStatus']['charset'] = $charset;
    }
    function getBillingAgreementCustom()
    {
        return $this->BillingAgreementCustom;
    }
    function setBillingAgreementCustom($BillingAgreementCustom, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementCustom = $BillingAgreementCustom;
        $this->_elements['BillingAgreementCustom']['charset'] = $charset;
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
    function getBillingAgreementMax()
    {
        return $this->BillingAgreementMax;
    }
    function setBillingAgreementMax($BillingAgreementMax, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementMax = $BillingAgreementMax;
        $this->_elements['BillingAgreementMax']['charset'] = $charset;
    }
}
