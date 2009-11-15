<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * BAUpdateRequestType
 *
 * @package PayPal
 */
class BAUpdateRequestType extends AbstractRequestType
{
    var $ReferenceID;

    var $BillingAgreementDescription;

    var $BillingAgreementStatus;

    var $BillingAgreementCustom;

    function BAUpdateRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'ReferenceID' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'BillingAgreementDescription' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'BillingAgreementStatus' => 
              array (
                'required' => false,
                'type' => 'MerchantPullStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'BillingAgreementCustom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getReferenceID()
    {
        return $this->ReferenceID;
    }
    function setReferenceID($ReferenceID, $charset = 'iso-8859-1')
    {
        $this->ReferenceID = $ReferenceID;
        $this->_elements['ReferenceID']['charset'] = $charset;
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
}
