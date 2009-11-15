<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * GetTransactionDetailsResponseType
 *
 * @package PayPal
 */
class GetTransactionDetailsResponseType extends AbstractResponseType
{
    var $PaymentTransactionDetails;

    var $ThreeDSecureDetails;

    function GetTransactionDetailsResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'PaymentTransactionDetails' => 
              array (
                'required' => true,
                'type' => 'PaymentTransactionType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ThreeDSecureDetails' => 
              array (
                'required' => true,
                'type' => 'ThreeDSecureInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPaymentTransactionDetails()
    {
        return $this->PaymentTransactionDetails;
    }
    function setPaymentTransactionDetails($PaymentTransactionDetails, $charset = 'iso-8859-1')
    {
        $this->PaymentTransactionDetails = $PaymentTransactionDetails;
        $this->_elements['PaymentTransactionDetails']['charset'] = $charset;
    }
    function getThreeDSecureDetails()
    {
        return $this->ThreeDSecureDetails;
    }
    function setThreeDSecureDetails($ThreeDSecureDetails, $charset = 'iso-8859-1')
    {
        $this->ThreeDSecureDetails = $ThreeDSecureDetails;
        $this->_elements['ThreeDSecureDetails']['charset'] = $charset;
    }
}
