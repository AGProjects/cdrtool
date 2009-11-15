<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CreateMobilePaymentRequestDetailsType
 *
 * @package PayPal
 */
class CreateMobilePaymentRequestDetailsType extends XSDSimpleType
{
    /**
     * Type of the payment
     */
    var $PaymentType;

    /**
     * How you want to obtain payment. Defaults to Sale.
     */
    var $PaymentAction;

    /**
     * Phone number of the user making the payment.
     */
    var $SenderPhone;

    /**
     * Type of recipient specified, i.e., phone number or email address
     */
    var $RecipientType;

    /**
     * Email address of the recipient
     */
    var $RecipientEmail;

    /**
     * Phone number of the recipipent
     */
    var $RecipientPhone;

    /**
     * Amount of item before tax and shipping
     */
    var $ItemAmount;

    /**
     * The tax charged on the transaction Tax
     */
    var $Tax;

    /**
     * Per-transaction shipping charge
     */
    var $Shipping;

    /**
     * Name of the item being ordered
     */
    var $ItemName;

    /**
     * SKU of the item being ordered
     */
    var $ItemNumber;

    /**
     * Memo entered by sender in PayPal Website Payments note field.
     */
    var $Note;

    /**
     * Unique ID for the order. Required for non-P2P transactions
     */
    var $CustomID;

    /**
     * Indicates whether the sender's phone number will be shared with recipient
     */
    var $SharePhoneNumber;

    /**
     * Indicates whether the sender's home address will be shared with recipient
     */
    var $ShareHomeAddress;

    function CreateMobilePaymentRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PaymentType' => 
              array (
                'required' => true,
                'type' => 'MobilePaymentCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentAction' => 
              array (
                'required' => true,
                'type' => 'PaymentActionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SenderPhone' => 
              array (
                'required' => true,
                'type' => 'PhoneNumberType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RecipientType' => 
              array (
                'required' => true,
                'type' => 'MobileRecipientCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RecipientEmail' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RecipientPhone' => 
              array (
                'required' => false,
                'type' => 'PhoneNumberType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
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
              'Note' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CustomID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SharePhoneNumber' => 
              array (
                'required' => false,
                'type' => 'integer',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShareHomeAddress' => 
              array (
                'required' => false,
                'type' => 'integer',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
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
    function getPaymentAction()
    {
        return $this->PaymentAction;
    }
    function setPaymentAction($PaymentAction, $charset = 'iso-8859-1')
    {
        $this->PaymentAction = $PaymentAction;
        $this->_elements['PaymentAction']['charset'] = $charset;
    }
    function getSenderPhone()
    {
        return $this->SenderPhone;
    }
    function setSenderPhone($SenderPhone, $charset = 'iso-8859-1')
    {
        $this->SenderPhone = $SenderPhone;
        $this->_elements['SenderPhone']['charset'] = $charset;
    }
    function getRecipientType()
    {
        return $this->RecipientType;
    }
    function setRecipientType($RecipientType, $charset = 'iso-8859-1')
    {
        $this->RecipientType = $RecipientType;
        $this->_elements['RecipientType']['charset'] = $charset;
    }
    function getRecipientEmail()
    {
        return $this->RecipientEmail;
    }
    function setRecipientEmail($RecipientEmail, $charset = 'iso-8859-1')
    {
        $this->RecipientEmail = $RecipientEmail;
        $this->_elements['RecipientEmail']['charset'] = $charset;
    }
    function getRecipientPhone()
    {
        return $this->RecipientPhone;
    }
    function setRecipientPhone($RecipientPhone, $charset = 'iso-8859-1')
    {
        $this->RecipientPhone = $RecipientPhone;
        $this->_elements['RecipientPhone']['charset'] = $charset;
    }
    function getItemAmount()
    {
        return $this->ItemAmount;
    }
    function setItemAmount($ItemAmount, $charset = 'iso-8859-1')
    {
        $this->ItemAmount = $ItemAmount;
        $this->_elements['ItemAmount']['charset'] = $charset;
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
    function getNote()
    {
        return $this->Note;
    }
    function setNote($Note, $charset = 'iso-8859-1')
    {
        $this->Note = $Note;
        $this->_elements['Note']['charset'] = $charset;
    }
    function getCustomID()
    {
        return $this->CustomID;
    }
    function setCustomID($CustomID, $charset = 'iso-8859-1')
    {
        $this->CustomID = $CustomID;
        $this->_elements['CustomID']['charset'] = $charset;
    }
    function getSharePhoneNumber()
    {
        return $this->SharePhoneNumber;
    }
    function setSharePhoneNumber($SharePhoneNumber, $charset = 'iso-8859-1')
    {
        $this->SharePhoneNumber = $SharePhoneNumber;
        $this->_elements['SharePhoneNumber']['charset'] = $charset;
    }
    function getShareHomeAddress()
    {
        return $this->ShareHomeAddress;
    }
    function setShareHomeAddress($ShareHomeAddress, $charset = 'iso-8859-1')
    {
        $this->ShareHomeAddress = $ShareHomeAddress;
        $this->_elements['ShareHomeAddress']['charset'] = $charset;
    }
}
