<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SetCustomerBillingAgreementRequestDetailsType
 *
 * @package PayPal
 */
class SetCustomerBillingAgreementRequestDetailsType extends XSDSimpleType
{
    var $BillingAgreementDetails;

    var $ReturnURL;

    var $CancelURL;

    var $LocaleCode;

    var $PageStyle;

    var $cpp_header_image;

    var $cpp_header_border_color;

    var $cpp_header_back_color;

    var $cpp_payflow_color;

    var $BuyerEmail;

    /**
     * The value 1 indicates that you require that the customer's billing address on
     * file. Setting this element overrides the setting you have specified in Admin.
     */
    var $ReqBillingAddress;

    function SetCustomerBillingAgreementRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BillingAgreementDetails' => 
              array (
                'required' => true,
                'type' => 'BillingAgreementDetailsType',
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
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LocaleCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PageStyle' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'cpp_header_image' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'cpp_header_border_color' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'cpp_header_back_color' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'cpp_payflow_color' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerEmail' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReqBillingAddress' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBillingAgreementDetails()
    {
        return $this->BillingAgreementDetails;
    }
    function setBillingAgreementDetails($BillingAgreementDetails, $charset = 'iso-8859-1')
    {
        $this->BillingAgreementDetails = $BillingAgreementDetails;
        $this->_elements['BillingAgreementDetails']['charset'] = $charset;
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
    function getLocaleCode()
    {
        return $this->LocaleCode;
    }
    function setLocaleCode($LocaleCode, $charset = 'iso-8859-1')
    {
        $this->LocaleCode = $LocaleCode;
        $this->_elements['LocaleCode']['charset'] = $charset;
    }
    function getPageStyle()
    {
        return $this->PageStyle;
    }
    function setPageStyle($PageStyle, $charset = 'iso-8859-1')
    {
        $this->PageStyle = $PageStyle;
        $this->_elements['PageStyle']['charset'] = $charset;
    }
    function getcpp_header_image()
    {
        return $this->cpp_header_image;
    }
    function setcpp_header_image($cpp_header_image, $charset = 'iso-8859-1')
    {
        $this->cpp_header_image = $cpp_header_image;
        $this->_elements['cpp_header_image']['charset'] = $charset;
    }
    function getcpp_header_border_color()
    {
        return $this->cpp_header_border_color;
    }
    function setcpp_header_border_color($cpp_header_border_color, $charset = 'iso-8859-1')
    {
        $this->cpp_header_border_color = $cpp_header_border_color;
        $this->_elements['cpp_header_border_color']['charset'] = $charset;
    }
    function getcpp_header_back_color()
    {
        return $this->cpp_header_back_color;
    }
    function setcpp_header_back_color($cpp_header_back_color, $charset = 'iso-8859-1')
    {
        $this->cpp_header_back_color = $cpp_header_back_color;
        $this->_elements['cpp_header_back_color']['charset'] = $charset;
    }
    function getcpp_payflow_color()
    {
        return $this->cpp_payflow_color;
    }
    function setcpp_payflow_color($cpp_payflow_color, $charset = 'iso-8859-1')
    {
        $this->cpp_payflow_color = $cpp_payflow_color;
        $this->_elements['cpp_payflow_color']['charset'] = $charset;
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
    function getReqBillingAddress()
    {
        return $this->ReqBillingAddress;
    }
    function setReqBillingAddress($ReqBillingAddress, $charset = 'iso-8859-1')
    {
        $this->ReqBillingAddress = $ReqBillingAddress;
        $this->_elements['ReqBillingAddress']['charset'] = $charset;
    }
}
