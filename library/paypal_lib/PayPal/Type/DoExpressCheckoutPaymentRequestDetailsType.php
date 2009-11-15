<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * DoExpressCheckoutPaymentRequestDetailsType
 *
 * @package PayPal
 */
class DoExpressCheckoutPaymentRequestDetailsType extends XSDSimpleType
{
    /**
     * How you want to obtain payment.
     */
    var $PaymentAction;

    /**
     * The timestamped token value that was returned by SetExpressCheckoutResponse and
     * passed on GetExpressCheckoutDetailsRequest.
     */
    var $Token;

    /**
     * Encrypted PayPal customer account identification number as returned by
     * GetExpressCheckoutDetailsResponse.
     */
    var $PayerID;

    /**
     * URL on Merchant site pertaining to this invoice.
     */
    var $OrderURL;

    /**
     * Information about the payment
     */
    var $PaymentDetails;

    /**
     * Flag to indicate if previously set promoCode shall be overriden. Value 1
     * indicates overriding.
     */
    var $PromoOverrideFlag;

    /**
     * Promotional financing code for item. Overrides any previous PromoCode setting.
     */
    var $PromoCode;

    /**
     * Contains data for enhanced data like Airline Itinerary Data.
     */
    var $EnhancedData;

    /**
     * Soft Descriptor supported for Sale and Auth in DEC only. For Order this will be
     * ignored.
     */
    var $SoftDescriptor;

    /**
     * Information about the user selected options.
     */
    var $UserSelectedOptions;

    function DoExpressCheckoutPaymentRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PaymentAction' => 
              array (
                'required' => true,
                'type' => 'PaymentActionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Token' => 
              array (
                'required' => true,
                'type' => 'ExpressCheckoutTokenType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerID' => 
              array (
                'required' => true,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OrderURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentDetails' => 
              array (
                'required' => true,
                'type' => 'PaymentDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PromoOverrideFlag' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PromoCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EnhancedData' => 
              array (
                'required' => false,
                'type' => 'EnhancedDataType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SoftDescriptor' => 
              array (
                'required' => false,
                'type' => 'string',
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

    function getPaymentAction()
    {
        return $this->PaymentAction;
    }
    function setPaymentAction($PaymentAction, $charset = 'iso-8859-1')
    {
        $this->PaymentAction = $PaymentAction;
        $this->_elements['PaymentAction']['charset'] = $charset;
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
    function getPayerID()
    {
        return $this->PayerID;
    }
    function setPayerID($PayerID, $charset = 'iso-8859-1')
    {
        $this->PayerID = $PayerID;
        $this->_elements['PayerID']['charset'] = $charset;
    }
    function getOrderURL()
    {
        return $this->OrderURL;
    }
    function setOrderURL($OrderURL, $charset = 'iso-8859-1')
    {
        $this->OrderURL = $OrderURL;
        $this->_elements['OrderURL']['charset'] = $charset;
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
    function getPromoOverrideFlag()
    {
        return $this->PromoOverrideFlag;
    }
    function setPromoOverrideFlag($PromoOverrideFlag, $charset = 'iso-8859-1')
    {
        $this->PromoOverrideFlag = $PromoOverrideFlag;
        $this->_elements['PromoOverrideFlag']['charset'] = $charset;
    }
    function getPromoCode()
    {
        return $this->PromoCode;
    }
    function setPromoCode($PromoCode, $charset = 'iso-8859-1')
    {
        $this->PromoCode = $PromoCode;
        $this->_elements['PromoCode']['charset'] = $charset;
    }
    function getEnhancedData()
    {
        return $this->EnhancedData;
    }
    function setEnhancedData($EnhancedData, $charset = 'iso-8859-1')
    {
        $this->EnhancedData = $EnhancedData;
        $this->_elements['EnhancedData']['charset'] = $charset;
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
