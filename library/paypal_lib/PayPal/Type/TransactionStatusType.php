<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * TransactionStatusType
 *
 * @package PayPal
 */
class TransactionStatusType extends XSDSimpleType
{
    /**
     * Indicates the success or failure of an eBay Online Payment for the transaction.
     * If the payment failed, the value returned indicates the reason for the failure.
     * Possible values: 0 = No payment failure. 3 = Buyer's eCheck bounced. 4 = Buyer's
     * credit card failed. 5 = Buyer failed payment as reported by seller. 7 = Payment
     * from buyer to seller is in PayPal process, but has not yet been completed.
     */
    var $eBayPaymentStatus;

    /**
     * Indicates the current state of the checkout process for the transaction.
     * Possible values: 0 = Checkout complete. 1 = Checkout incomplete. No details
     * specified. 2 = Buyer requests total. 3 = Seller responded to buyer's request.
     */
    var $IncompleteState;

    /**
     * Indicates last date and time checkout status or incomplete state was updated (in
     * GMT).
     */
    var $LastTimeModified;

    /**
     * Payment method used by the buyer. (See BuyerPaymentCodeList/Type).
     */
    var $PaymentMethodUsed;

    /**
     * Indicates whether the transaction process complete or incomplete. Possible
     * values: 1 = Incomplete 2 = Complete
     */
    var $StatusIs;

    function TransactionStatusType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'eBayPaymentStatus' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'IncompleteState' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastTimeModified' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentMethodUsed' => 
              array (
                'required' => false,
                'type' => 'BuyerPaymentMethodCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StatusIs' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function geteBayPaymentStatus()
    {
        return $this->eBayPaymentStatus;
    }
    function seteBayPaymentStatus($eBayPaymentStatus, $charset = 'iso-8859-1')
    {
        $this->eBayPaymentStatus = $eBayPaymentStatus;
        $this->_elements['eBayPaymentStatus']['charset'] = $charset;
    }
    function getIncompleteState()
    {
        return $this->IncompleteState;
    }
    function setIncompleteState($IncompleteState, $charset = 'iso-8859-1')
    {
        $this->IncompleteState = $IncompleteState;
        $this->_elements['IncompleteState']['charset'] = $charset;
    }
    function getLastTimeModified()
    {
        return $this->LastTimeModified;
    }
    function setLastTimeModified($LastTimeModified, $charset = 'iso-8859-1')
    {
        $this->LastTimeModified = $LastTimeModified;
        $this->_elements['LastTimeModified']['charset'] = $charset;
    }
    function getPaymentMethodUsed()
    {
        return $this->PaymentMethodUsed;
    }
    function setPaymentMethodUsed($PaymentMethodUsed, $charset = 'iso-8859-1')
    {
        $this->PaymentMethodUsed = $PaymentMethodUsed;
        $this->_elements['PaymentMethodUsed']['charset'] = $charset;
    }
    function getStatusIs()
    {
        return $this->StatusIs;
    }
    function setStatusIs($StatusIs, $charset = 'iso-8859-1')
    {
        $this->StatusIs = $StatusIs;
        $this->_elements['StatusIs']['charset'] = $charset;
    }
}
