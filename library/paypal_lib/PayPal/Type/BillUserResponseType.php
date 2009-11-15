<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * BillUserResponseType
 *
 * @package PayPal
 */
class BillUserResponseType extends AbstractResponseType
{
    var $BillUserResponseDetails;

    var $FMFDetails;

    function BillUserResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'BillUserResponseDetails' => 
              array (
                'required' => true,
                'type' => 'MerchantPullPaymentResponseType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FMFDetails' => 
              array (
                'required' => false,
                'type' => 'FMFDetailsType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getBillUserResponseDetails()
    {
        return $this->BillUserResponseDetails;
    }
    function setBillUserResponseDetails($BillUserResponseDetails, $charset = 'iso-8859-1')
    {
        $this->BillUserResponseDetails = $BillUserResponseDetails;
        $this->_elements['BillUserResponseDetails']['charset'] = $charset;
    }
    function getFMFDetails()
    {
        return $this->FMFDetails;
    }
    function setFMFDetails($FMFDetails, $charset = 'iso-8859-1')
    {
        $this->FMFDetails = $FMFDetails;
        $this->_elements['FMFDetails']['charset'] = $charset;
    }
}
