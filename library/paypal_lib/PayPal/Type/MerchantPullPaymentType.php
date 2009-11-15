<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * MerchantPullPaymentType
 * 
 * MerchantPullPayment Parameters to make initiate a pull payment
 *
 * @package PayPal
 */
class MerchantPullPaymentType extends XSDSimpleType
{
    /**
     * The amount to charge to the customer.
     */
    var $Amount;

    /**
     * Preapproved Payments billing agreement identification number between the PayPal
     * customer and you.
     */
    var $MpID;

    /**
     * Specifies type of PayPal payment you require
     */
    var $PaymentType;

    /**
     * Text entered by the customer in the Note field during enrollment
     */
    var $Memo;

    /**
     * Subject line of confirmation email sent to recipient
     */
    var $EmailSubject;

    /**
     * The tax charged on the transaction
     */
    var $Tax;

    /**
     * Per-transaction shipping charge
     */
    var $Shipping;

    /**
     * Per-transaction handling charge
     */
    var $Handling;

    /**
     * Name of purchased item
     */
    var $ItemName;

    /**
     * Reference number of purchased item
     */
    var $ItemNumber;

    /**
     * Your invoice number
     */
    var $Invoice;

    /**
     * Custom annotation field for tracking or other use
     */
    var $Custom;

    /**
     * An identification code for use by third-party applications to identify
     * transactions.
     */
    var $ButtonSource;

    /**
     * Passed in soft descriptor string to be appended.
     */
    var $SoftDescriptor;

    function MerchantPullPaymentType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MpID' => 
              array (
                'required' => true,
                'type' => 'MerchantPullIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentType' => 
              array (
                'required' => false,
                'type' => 'MerchantPullPaymentCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Memo' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EmailSubject' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Tax' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Shipping' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Handling' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Invoice' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Custom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ButtonSource' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SoftDescriptor' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAmount()
    {
        return $this->Amount;
    }
    function setAmount($Amount, $charset = 'iso-8859-1')
    {
        $this->Amount = $Amount;
        $this->_elements['Amount']['charset'] = $charset;
    }
    function getMpID()
    {
        return $this->MpID;
    }
    function setMpID($MpID, $charset = 'iso-8859-1')
    {
        $this->MpID = $MpID;
        $this->_elements['MpID']['charset'] = $charset;
    }
    function getPaymentType()
    {
        return $this->PaymentType;
    }
    function setPaymentType($PaymentType, $charset = 'iso-8859-1')
    {
        $this->PaymentType = $PaymentType;
        $this->_elements['PaymentType']['charset'] = $charset;
    }
    function getMemo()
    {
        return $this->Memo;
    }
    function setMemo($Memo, $charset = 'iso-8859-1')
    {
        $this->Memo = $Memo;
        $this->_elements['Memo']['charset'] = $charset;
    }
    function getEmailSubject()
    {
        return $this->EmailSubject;
    }
    function setEmailSubject($EmailSubject, $charset = 'iso-8859-1')
    {
        $this->EmailSubject = $EmailSubject;
        $this->_elements['EmailSubject']['charset'] = $charset;
    }
    function getTax()
    {
        return $this->Tax;
    }
    function setTax($Tax, $charset = 'iso-8859-1')
    {
        $this->Tax = $Tax;
        $this->_elements['Tax']['charset'] = $charset;
    }
    function getShipping()
    {
        return $this->Shipping;
    }
    function setShipping($Shipping, $charset = 'iso-8859-1')
    {
        $this->Shipping = $Shipping;
        $this->_elements['Shipping']['charset'] = $charset;
    }
    function getHandling()
    {
        return $this->Handling;
    }
    function setHandling($Handling, $charset = 'iso-8859-1')
    {
        $this->Handling = $Handling;
        $this->_elements['Handling']['charset'] = $charset;
    }
    function getItemName()
    {
        return $this->ItemName;
    }
    function setItemName($ItemName, $charset = 'iso-8859-1')
    {
        $this->ItemName = $ItemName;
        $this->_elements['ItemName']['charset'] = $charset;
    }
    function getItemNumber()
    {
        return $this->ItemNumber;
    }
    function setItemNumber($ItemNumber, $charset = 'iso-8859-1')
    {
        $this->ItemNumber = $ItemNumber;
        $this->_elements['ItemNumber']['charset'] = $charset;
    }
    function getInvoice()
    {
        return $this->Invoice;
    }
    function setInvoice($Invoice, $charset = 'iso-8859-1')
    {
        $this->Invoice = $Invoice;
        $this->_elements['Invoice']['charset'] = $charset;
    }
    function getCustom()
    {
        return $this->Custom;
    }
    function setCustom($Custom, $charset = 'iso-8859-1')
    {
        $this->Custom = $Custom;
        $this->_elements['Custom']['charset'] = $charset;
    }
    function getButtonSource()
    {
        return $this->ButtonSource;
    }
    function setButtonSource($ButtonSource, $charset = 'iso-8859-1')
    {
        $this->ButtonSource = $ButtonSource;
        $this->_elements['ButtonSource']['charset'] = $charset;
    }
    function getSoftDescriptor()
    {
        return $this->SoftDescriptor;
    }
    function setSoftDescriptor($SoftDescriptor, $charset = 'iso-8859-1')
    {
        $this->SoftDescriptor = $SoftDescriptor;
        $this->_elements['SoftDescriptor']['charset'] = $charset;
    }
}
