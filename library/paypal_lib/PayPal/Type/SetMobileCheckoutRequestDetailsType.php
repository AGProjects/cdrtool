<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SetMobileCheckoutRequestDetailsType
 *
 * @package PayPal
 */
class SetMobileCheckoutRequestDetailsType extends XSDSimpleType
{
    /**
     * The phone number of the buyer's mobile device, if available.
     */
    var $BuyerPhone;

    /**
     * Cost of this item before tax and shipping. You must set the currencyID attribute
     * to one of the three-character currency codes for any of the supported PayPal
     * currencies.
     */
    var $ItemAmount;

    /**
     * Tax amount for this item. You must set the currencyID attribute to one of the
     * three-character currency codes for any of the supported PayPal currencies.
     */
    var $Tax;

    /**
     * Shipping amount for this item. You must set the currencyID attribute to one of
     * the three-character currency codes for any of the supported PayPal currencies.
     */
    var $Shipping;

    /**
     * Description of the item that the customer is purchasing.
     */
    var $ItemName;

    /**
     * Reference number of the item that the customer is purchasing.
     */
    var $ItemNumber;

    /**
     * A free-form field for your own use, such as a tracking number or other value you
     * want returned to you in IPN.
     */
    var $Custom;

    /**
     * Your own unique invoice or tracking number.
     */
    var $InvoiceID;

    /**
     * URL to which the customer's browser is returned after choosing to pay with
     * PayPal. PayPal recommends that the value of ReturnURL be the final review page
     * on which the customer confirms the order and payment.
     */
    var $ReturnURL;

    /**
     * URL to which the customer is returned if he does not approve the use of PayPal
     * to pay you. PayPal recommends that the value of CancelURL be the original page
     * on which the customer chose to pay with PayPal.
     */
    var $CancelURL;

    /**
     * The value 1 indicates that you require that the customer's shipping address on
     * file with PayPal be a confirmed address. Setting this element overrides the
     * setting you have specified in your Merchant Account Profile.
     */
    var $AddressDisplayOptions;

    /**
     * The value 1 indicates that you require that the customer specifies a contact
     * phone for the transactxion. Default is 0 / none required.
     */
    var $SharePhone;

    /**
     * Customer's shipping address.
     */
    var $ShipToAddress;

    /**
     * Email address of the buyer as entered during checkout. PayPal uses this value to
     * pre-fill the login portion of the PayPal login page.
     */
    var $BuyerEmail;

    function SetMobileCheckoutRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BuyerPhone' => 
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
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemNumber' => 
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
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReturnURL' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CancelURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AddressDisplayOptions' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SharePhone' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShipToAddress' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerEmail' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBuyerPhone()
    {
        return $this->BuyerPhone;
    }
    function setBuyerPhone($BuyerPhone, $charset = 'iso-8859-1')
    {
        $this->BuyerPhone = $BuyerPhone;
        $this->_elements['BuyerPhone']['charset'] = $charset;
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
    function getCustom()
    {
        return $this->Custom;
    }
    function setCustom($Custom, $charset = 'iso-8859-1')
    {
        $this->Custom = $Custom;
        $this->_elements['Custom']['charset'] = $charset;
    }
    function getInvoiceID()
    {
        return $this->InvoiceID;
    }
    function setInvoiceID($InvoiceID, $charset = 'iso-8859-1')
    {
        $this->InvoiceID = $InvoiceID;
        $this->_elements['InvoiceID']['charset'] = $charset;
    }
    function getReturnURL()
    {
        return $this->ReturnURL;
    }
    function setReturnURL($ReturnURL, $charset = 'iso-8859-1')
    {
        $this->ReturnURL = $ReturnURL;
        $this->_elements['ReturnURL']['charset'] = $charset;
    }
    function getCancelURL()
    {
        return $this->CancelURL;
    }
    function setCancelURL($CancelURL, $charset = 'iso-8859-1')
    {
        $this->CancelURL = $CancelURL;
        $this->_elements['CancelURL']['charset'] = $charset;
    }
    function getAddressDisplayOptions()
    {
        return $this->AddressDisplayOptions;
    }
    function setAddressDisplayOptions($AddressDisplayOptions, $charset = 'iso-8859-1')
    {
        $this->AddressDisplayOptions = $AddressDisplayOptions;
        $this->_elements['AddressDisplayOptions']['charset'] = $charset;
    }
    function getSharePhone()
    {
        return $this->SharePhone;
    }
    function setSharePhone($SharePhone, $charset = 'iso-8859-1')
    {
        $this->SharePhone = $SharePhone;
        $this->_elements['SharePhone']['charset'] = $charset;
    }
    function getShipToAddress()
    {
        return $this->ShipToAddress;
    }
    function setShipToAddress($ShipToAddress, $charset = 'iso-8859-1')
    {
        $this->ShipToAddress = $ShipToAddress;
        $this->_elements['ShipToAddress']['charset'] = $charset;
    }
    function getBuyerEmail()
    {
        return $this->BuyerEmail;
    }
    function setBuyerEmail($BuyerEmail, $charset = 'iso-8859-1')
    {
        $this->BuyerEmail = $BuyerEmail;
        $this->_elements['BuyerEmail']['charset'] = $charset;
    }
}
