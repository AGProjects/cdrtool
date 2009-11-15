<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AuthorizationInfoType
 * 
 * Authorization details
 *
 * @package PayPal
 */
class AuthorizationInfoType extends XSDSimpleType
{
    /**
     * The status of the payment:
     */
    var $PaymentStatus;

    /**
     * The reason the payment is pending: none: No pending reason
     */
    var $PendingReason;

    /**
     * Protection Eligibility for this Transaction - None, SPP or ESPP
     */
    var $ProtectionEligibility;

    function AuthorizationInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PaymentStatus' => 
              array (
                'required' => true,
                'type' => 'PaymentStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PendingReason' => 
              array (
                'required' => false,
                'type' => 'PendingStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProtectionEligibility' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPaymentStatus()
    {
        return $this->PaymentStatus;
    }
    function setPaymentStatus($PaymentStatus, $charset = 'iso-8859-1')
    {
        $this->PaymentStatus = $PaymentStatus;
        $this->_elements['PaymentStatus']['charset'] = $charset;
    }
    function getPendingReason()
    {
        return $this->PendingReason;
    }
    function setPendingReason($PendingReason, $charset = 'iso-8859-1')
    {
        $this->PendingReason = $PendingReason;
        $this->_elements['PendingReason']['charset'] = $charset;
    }
    function getProtectionEligibility()
    {
        return $this->ProtectionEligibility;
    }
    function setProtectionEligibility($ProtectionEligibility, $charset = 'iso-8859-1')
    {
        $this->ProtectionEligibility = $ProtectionEligibility;
        $this->_elements['ProtectionEligibility']['charset'] = $charset;
    }
}
