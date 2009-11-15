<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * GetExpressCheckoutDetailsResponseDetailsType
 *
 * @package PayPal
 */
class GetExpressCheckoutDetailsResponseDetailsType extends XSDSimpleType
{
    /**
     * The timestamped token value that was returned by SetExpressCheckoutResponse and
     * passed on GetExpressCheckoutDetailsRequest.
     */
    var $Token;

    /**
     * Information about the payer
     */
    var $PayerInfo;

    /**
     * A free-form field for your own use, as set by you in the Custom element of
     * SetExpressCheckoutRequest.
     */
    var $Custom;

    /**
     * Your own invoice or tracking number, as set by you in the InvoiceID element of
     * SetExpressCheckoutRequest.
     */
    var $InvoiceID;

    /**
     * Payer's contact telephone number. PayPal returns a contact telephone number only
     * if your Merchant account profile settings require that the buyer enter one.
     */
    var $ContactPhone;

    var $BillingAgreementAcceptedStatus;

    var $RedirectRequired;

    /**
     * Customer's billing address.
     */
    var $BillingAddress;

    /**
     * Text note entered by the buyer in PayPal flow.
     */
    var $Note;

    /**
     * Returns the status of the EC checkout session.
     */
    var $CheckoutStatus;

    /**
     * PayPal may offer a discount or gift certificate to the buyer, which will be
     * represented by a negativeamount. If the buyer has a negative balance, PayPal
     * will add that amount to the current charges, which will be represented as a
     * positive amount.
     */
    var $PayPalAdjustment;

    /**
     * Information about the individual purchased items.
     */
    var $PaymentDetails;

    /**
     * Information about the user selected options.
     */
    var $UserSelectedOptions;

    function GetExpressCheckoutDetailsResponseDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Token' => 
              array (
                'required' => true,
                'type' => 'ExpressCheckoutTokenType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerInfo' => 
              array (
                'required' => true,
                'type' => 'PayerInfoType',
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
              'ContactPhone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAgreementAcceptedStatus' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RedirectRequired' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAddress' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Note' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CheckoutStatus' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayPalAdjustment' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentDetails' => 
              array (
                'required' => false,
                'type' => 'PaymentDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'UserSelectedOptions' => 
              array (
                'required' => false,
                'type' => 'UserSelectedOptionType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getToken()
    {
        return $this->Token;
    }
    function setToken($Token, $charset = 'iso-8859-1')
    {
        $this->Token = $Token;
        $this->_elements['Token']['charset'] = $charset;
    }
    function getPayerInfo()
    {
        return $this->PayerInfo;
    }
    function setPayerInfo($PayerInfo, $charset = 'iso-8859-1')
    {
        $this->PayerInfo = $PayerInfo;
        $this->_elements['PayerInfo']['charset'] = $charset;
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
    function getContactPhone()
    {
        return $this->ContactPhone;
    }
    function setContactPhone($ContactPhone, $charset = 'iso-8859-1')
    {
        $this->ContactPhone = $ContactPhone;
        $this->_elements['ContactPhone']['charset'] = $charset;
    }
    function getBillingAgreementAcceptedStatus()
    {
        return $this->BillingAgreementAcceptedStatus;
    }
    function setBillingAgreementAcceptedStatus($BillingAgreementAcceptedStatus, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementAcceptedStatus = $BillingAgreementAcceptedStatus;
        $this->_elements['BillingAgreementAcceptedStatus']['charset'] = $charset;
    }
    function getRedirectRequired()
    {
        return $this->RedirectRequired;
    }
    function setRedirectRequired($RedirectRequired, $charset = 'iso-8859-1')
    {
        $this->RedirectRequired = $RedirectRequired;
        $this->_elements['RedirectRequired']['charset'] = $charset;
    }
    function getBillingAddress()
    {
        return $this->BillingAddress;
    }
    function setBillingAddress($BillingAddress, $charset = 'iso-8859-1')
    {
        $this->BillingAddress = $BillingAddress;
        $this->_elements['BillingAddress']['charset'] = $charset;
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
    function getCheckoutStatus()
    {
        return $this->CheckoutStatus;
    }
    function setCheckoutStatus($CheckoutStatus, $charset = 'iso-8859-1')
    {
        $this->CheckoutStatus = $CheckoutStatus;
        $this->_elements['CheckoutStatus']['charset'] = $charset;
    }
    function getPayPalAdjustment()
    {
        return $this->PayPalAdjustment;
    }
    function setPayPalAdjustment($PayPalAdjustment, $charset = 'iso-8859-1')
    {
        $this->PayPalAdjustment = $PayPalAdjustment;
        $this->_elements['PayPalAdjustment']['charset'] = $charset;
    }
    function getPaymentDetails()
    {
        return $this->PaymentDetails;
    }
    function setPaymentDetails($PaymentDetails, $charset = 'iso-8859-1')
    {
        $this->PaymentDetails = $PaymentDetails;
        $this->_elements['PaymentDetails']['charset'] = $charset;
    }
    function getUserSelectedOptions()
    {
        return $this->UserSelectedOptions;
    }
    function setUserSelectedOptions($UserSelectedOptions, $charset = 'iso-8859-1')
    {
        $this->UserSelectedOptions = $UserSelectedOptions;
        $this->_elements['UserSelectedOptions']['charset'] = $charset;
    }
}
