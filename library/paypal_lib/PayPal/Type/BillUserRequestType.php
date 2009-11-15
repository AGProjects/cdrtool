<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * BillUserRequestType
 *
 * @package PayPal
 */
class BillUserRequestType extends AbstractRequestType
{
    var $MerchantPullPaymentDetails;

    /**
     * This flag indicates that the response should include FMFDetails
     */
    var $ReturnFMFDetails;

    function BillUserRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'MerchantPullPaymentDetails' => 
              array (
                'required' => true,
                'type' => 'MerchantPullPaymentType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReturnFMFDetails' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getMerchantPullPaymentDetails()
    {
        return $this->MerchantPullPaymentDetails;
    }
    function setMerchantPullPaymentDetails($MerchantPullPaymentDetails, $charset = 'iso-8859-1')
    {
        $this->MerchantPullPaymentDetails = $MerchantPullPaymentDetails;
        $this->_elements['MerchantPullPaymentDetails']['charset'] = $charset;
    }
    function getReturnFMFDetails()
    {
        return $this->ReturnFMFDetails;
    }
    function setReturnFMFDetails($ReturnFMFDetails, $charset = 'iso-8859-1')
    {
        $this->ReturnFMFDetails = $ReturnFMFDetails;
        $this->_elements['ReturnFMFDetails']['charset'] = $charset;
    }
}
