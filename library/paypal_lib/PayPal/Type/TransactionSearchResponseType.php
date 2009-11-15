<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * TransactionSearchResponseType
 *
 * @package PayPal
 */
class TransactionSearchResponseType extends AbstractResponseType
{
    var $PaymentTransactions;

    function TransactionSearchResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'PaymentTransactions' => 
              array (
                'required' => false,
                'type' => 'PaymentTransactionSearchResultType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPaymentTransactions()
    {
        return $this->PaymentTransactions;
    }
    function setPaymentTransactions($PaymentTransactions, $charset = 'iso-8859-1')
    {
        $this->PaymentTransactions = $PaymentTransactions;
        $this->_elements['PaymentTransactions']['charset'] = $charset;
    }
}
