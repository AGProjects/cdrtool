<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SetExpressCheckoutRequestDetailsType
 *
 * @package PayPal
 */
class SetExpressCheckoutRequestDetailsType extends XSDSimpleType
{
    /**
     * The total cost of the order to the customer. If shipping cost and tax charges
     * are known, include them in OrderTotal; if not, OrderTotal should be the current
     * sub-total of the order.
     */
    var $OrderTotal;

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
     * URL to which the customer's browser is returned after paying with giropay
     * online.
     */
    var $giropaySuccessURL;

    /**
     * URL to which the customer's browser is returned after fail to pay with giropay
     * online.
     */
    var $giropayCancelURL;

    /**
     * URL to which the customer's browser can be returned in the mEFT done page.
     */
    var $BanktxnPendingURL;

    /**
     * On your first invocation of SetExpressCheckoutRequest, the value of this token
     * is returned by SetExpressCheckoutResponse.
     */
    var $Token;

    /**
     * The expected maximum total amount of the complete order, including shipping cost
     * and tax charges.
     */
    var $MaxAmount;

    /**
     * Description of items the customer is purchasing.
     */
    var $OrderDescription;

    /**
     * A free-form field for your own use, such as a tracking number or other value you
     * want PayPal to return on GetExpressCheckoutDetailsResponse and
     * DoExpressCheckoutPaymentResponse.
     */
    var $Custom;

    /**
     * Your own unique invoice or tracking number. PayPal returns this value to you on
     * DoExpressCheckoutPaymentResponse.
     */
    var $InvoiceID;

    /**
     * The value 1 indicates that you require that the customer's shipping address on
     * file with PayPal be a confirmed address. Any value other than 1 indicates that
     * the customer's shipping address on file with PayPal need NOT be a confirmed
     * address. Setting this element overrides the setting you have specified in the
     * recipient's Merchant Account Profile.
     */
    var $ReqConfirmShipping;

    /**
     * The value 1 indicates that you require that the customer's billing address on
     * file. Setting this element overrides the setting you have specified in Admin.
     */
    var $ReqBillingAddress;

    /**
     * The billing address for the buyer.
     */
    var $BillingAddress;

    /**
     * The value 1 indicates that on the PayPal pages, no shipping address fields
     * should be displayed whatsoever.
     */
    var $NoShipping;

    /**
     * The value 1 indicates that the PayPal pages should display the shipping address
     * set by you in the Address element on this SetExpressCheckoutRequest, not the
     * shipping address on file with PayPal for this customer. Displaying the PayPal
     * street address on file does not allow the customer to edit that address.
     */
    var $AddressOverride;

    /**
     * Locale of pages displayed by PayPal during Express Checkout.
     */
    var $LocaleCode;

    /**
     * Sets the Custom Payment Page Style for payment pages associated with this
     * button/link. PageStyle corresponds to the HTML variable page_style for
     * customizing payment pages. The value is the same as the Page Style Name you
     * chose when adding or editing the page style from the Profile subtab of the My
     * Account tab of your PayPal account.
     */
    var $PageStyle;

    /**
     * A URL for the image you want to appear at the top left of the payment page. The
     * image has a maximum size of 750 pixels wide by 90 pixels high. PayPal recommends
     * that you provide an image that is stored on a secure (https) server.
     */
    var $cpp_header_image;

    /**
     * Sets the border color around the header of the payment page. The border is a
     * 2-pixel perimeter around the header space, which is 750 pixels wide by 90 pixels
     * high.
     */
    var $cpp_header_border_color;

    /**
     * Sets the background color for the header of the payment page.
     */
    var $cpp_header_back_color;

    /**
     * Sets the background color for the payment page.
     */
    var $cpp_payflow_color;

    /**
     * Customer's shipping address.
     */
    var $Address;

    /**
     * How you want to obtain payment.
     */
    var $PaymentAction;

    /**
     * This will indicate which flow you are choosing (expresschecheckout or
     * expresscheckout optional)
     */
    var $SolutionType;

    /**
     * This indicates Which page to display for ExpressO (Billing or Login)
     */
    var $LandingPage;

    /**
     * Email address of the buyer as entered during checkout. PayPal uses this value to
     * pre-fill the PayPal membership sign-up portion of the PayPal login page.
     */
    var $BuyerEmail;

    var $ChannelType;

    var $BillingAgreementDetails;

    /**
     * Promo Code
     */
    var $PromoCodes;

    /**
     * Default Funding option for PayLater Checkout button.
     */
    var $PayPalCheckOutBtnType;

    var $ProductCategory;

    var $ShippingMethod;

    /**
     * Date and time (in GMT in the format yyyy-MM-ddTHH:mm:ssZ) at which address was
     * changed by the user.
     */
    var $ProfileAddressChangeDate;

    /**
     * The value 1 indicates that the customer may enter a note to the merchant on the
     * PayPal page during checkout. The note is returned in the
     * GetExpressCheckoutDetails response and the DoExpressCheckoutPayment response.
     */
    var $AllowNote;

    /**
     * Funding source preferences.
     */
    var $FundingSourceDetails;

    /**
     * The label that needs to be displayed on the cancel links in the PayPal hosted
     * checkout pages.
     */
    var $BrandName;

    /**
     * URL for PayPal to use to retrieve shipping, handling, insurance, and tax details
     * from your website.
     */
    var $CallbackURL;

    /**
     * Enhanced data for different industry segments.
     */
    var $EnhancedCheckoutData;

    /**
     * List of other payment methods the user can pay with. Optional Refer to the
     * OtherPaymentMethodDetailsType for more details.
     */
    var $OtherPaymentMethods;

    /**
     * Details about the buyer's account.
     */
    var $BuyerDetails;

    /**
     * Information about the payment.
     */
    var $PaymentDetails;

    /**
     * List of Fall Back Shipping options provided by merchant.
     */
    var $FlatRateShippingOptions;

    /**
     * Information about the call back timeout override.
     */
    var $CallbackTimeout;

    function SetExpressCheckoutRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'OrderTotal' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
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
              'giropaySuccessURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'giropayCancelURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BanktxnPendingURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Token' => 
              array (
                'required' => false,
                'type' => 'ExpressCheckoutTokenType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MaxAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OrderDescription' => 
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
              'ReqConfirmShipping' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReqBillingAddress' => 
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
              'NoShipping' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AddressOverride' => 
              array (
                'required' => false,
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
              'Address' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentAction' => 
              array (
                'required' => false,
                'type' => 'PaymentActionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SolutionType' => 
              array (
                'required' => false,
                'type' => 'SolutionTypeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LandingPage' => 
              array (
                'required' => false,
                'type' => 'LandingPageType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerEmail' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ChannelType' => 
              array (
                'required' => false,
                'type' => 'ChannelType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAgreementDetails' => 
              array (
                'required' => false,
                'type' => 'BillingAgreementDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PromoCodes' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayPalCheckOutBtnType' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProductCategory' => 
              array (
                'required' => false,
                'type' => 'ProductCategoryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingMethod' => 
              array (
                'required' => false,
                'type' => 'ShippingServiceCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProfileAddressChangeDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AllowNote' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FundingSourceDetails' => 
              array (
                'required' => false,
                'type' => 'FundingSourceDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BrandName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CallbackURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EnhancedCheckoutData' => 
              array (
                'required' => false,
                'type' => 'EnhancedCheckoutDataType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OtherPaymentMethods' => 
              array (
                'required' => false,
                'type' => 'OtherPaymentMethodDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerDetails' => 
              array (
                'required' => false,
                'type' => 'BuyerDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentDetails' => 
              array (
                'required' => false,
                'type' => 'PaymentDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FlatRateShippingOptions' => 
              array (
                'required' => false,
                'type' => 'ShippingOptionType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CallbackTimeout' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getOrderTotal()
    {
        return $this->OrderTotal;
    }
    function setOrderTotal($OrderTotal, $charset = 'iso-8859-1')
    {
        $this->OrderTotal = $OrderTotal;
        $this->_elements['OrderTotal']['charset'] = $charset;
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
    function getgiropaySuccessURL()
    {
        return $this->giropaySuccessURL;
    }
    function setgiropaySuccessURL($giropaySuccessURL, $charset = 'iso-8859-1')
    {
        $this->giropaySuccessURL = $giropaySuccessURL;
        $this->_elements['giropaySuccessURL']['charset'] = $charset;
    }
    function getgiropayCancelURL()
    {
        return $this->giropayCancelURL;
    }
    function setgiropayCancelURL($giropayCancelURL, $charset = 'iso-8859-1')
    {
        $this->giropayCancelURL = $giropayCancelURL;
        $this->_elements['giropayCancelURL']['charset'] = $charset;
    }
    function getBanktxnPendingURL()
    {
        return $this->BanktxnPendingURL;
    }
    function setBanktxnPendingURL($BanktxnPendingURL, $charset = 'iso-8859-1')
    {
        $this->BanktxnPendingURL = $BanktxnPendingURL;
        $this->_elements['BanktxnPendingURL']['charset'] = $charset;
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
    function getMaxAmount()
    {
        return $this->MaxAmount;
    }
    function setMaxAmount($MaxAmount, $charset = 'iso-8859-1')
    {
        $this->MaxAmount = $MaxAmount;
        $this->_elements['MaxAmount']['charset'] = $charset;
    }
    function getOrderDescription()
    {
        return $this->OrderDescription;
    }
    function setOrderDescription($OrderDescription, $charset = 'iso-8859-1')
    {
        $this->OrderDescription = $OrderDescription;
        $this->_elements['OrderDescription']['charset'] = $charset;
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
    function getReqConfirmShipping()
    {
        return $this->ReqConfirmShipping;
    }
    function setReqConfirmShipping($ReqConfirmShipping, $charset = 'iso-8859-1')
    {
        $this->ReqConfirmShipping = $ReqConfirmShipping;
        $this->_elements['ReqConfirmShipping']['charset'] = $charset;
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
    function getBillingAddress()
    {
        return $this->BillingAddress;
    }
    function setBillingAddress($BillingAddress, $charset = 'iso-8859-1')
    {
        $this->BillingAddress = $BillingAddress;
        $this->_elements['BillingAddress']['charset'] = $charset;
    }
    function getNoShipping()
    {
        return $this->NoShipping;
    }
    function setNoShipping($NoShipping, $charset = 'iso-8859-1')
    {
        $this->NoShipping = $NoShipping;
        $this->_elements['NoShipping']['charset'] = $charset;
    }
    function getAddressOverride()
    {
        return $this->AddressOverride;
    }
    function setAddressOverride($AddressOverride, $charset = 'iso-8859-1')
    {
        $this->AddressOverride = $AddressOverride;
        $this->_elements['AddressOverride']['charset'] = $charset;
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
    function getAddress()
    {
        return $this->Address;
    }
    function setAddress($Address, $charset = 'iso-8859-1')
    {
        $this->Address = $Address;
        $this->_elements['Address']['charset'] = $charset;
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
    function getSolutionType()
    {
        return $this->SolutionType;
    }
    function setSolutionType($SolutionType, $charset = 'iso-8859-1')
    {
        $this->SolutionType = $SolutionType;
        $this->_elements['SolutionType']['charset'] = $charset;
    }
    function getLandingPage()
    {
        return $this->LandingPage;
    }
    function setLandingPage($LandingPage, $charset = 'iso-8859-1')
    {
        $this->LandingPage = $LandingPage;
        $this->_elements['LandingPage']['charset'] = $charset;
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
    function getChannelType()
    {
        return $this->ChannelType;
    }
    function setChannelType($ChannelType, $charset = 'iso-8859-1')
    {
        $this->ChannelType = $ChannelType;
        $this->_elements['ChannelType']['charset'] = $charset;
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
    function getPromoCodes()
    {
        return $this->PromoCodes;
    }
    function setPromoCodes($PromoCodes, $charset = 'iso-8859-1')
    {
        $this->PromoCodes = $PromoCodes;
        $this->_elements['PromoCodes']['charset'] = $charset;
    }
    function getPayPalCheckOutBtnType()
    {
        return $this->PayPalCheckOutBtnType;
    }
    function setPayPalCheckOutBtnType($PayPalCheckOutBtnType, $charset = 'iso-8859-1')
    {
        $this->PayPalCheckOutBtnType = $PayPalCheckOutBtnType;
        $this->_elements['PayPalCheckOutBtnType']['charset'] = $charset;
    }
    function getProductCategory()
    {
        return $this->ProductCategory;
    }
    function setProductCategory($ProductCategory, $charset = 'iso-8859-1')
    {
        $this->ProductCategory = $ProductCategory;
        $this->_elements['ProductCategory']['charset'] = $charset;
    }
    function getShippingMethod()
    {
        return $this->ShippingMethod;
    }
    function setShippingMethod($ShippingMethod, $charset = 'iso-8859-1')
    {
        $this->ShippingMethod = $ShippingMethod;
        $this->_elements['ShippingMethod']['charset'] = $charset;
    }
    function getProfileAddressChangeDate()
    {
        return $this->ProfileAddressChangeDate;
    }
    function setProfileAddressChangeDate($ProfileAddressChangeDate, $charset = 'iso-8859-1')
    {
        $this->ProfileAddressChangeDate = $ProfileAddressChangeDate;
        $this->_elements['ProfileAddressChangeDate']['charset'] = $charset;
    }
    function getAllowNote()
    {
        return $this->AllowNote;
    }
    function setAllowNote($AllowNote, $charset = 'iso-8859-1')
    {
        $this->AllowNote = $AllowNote;
        $this->_elements['AllowNote']['charset'] = $charset;
    }
    function getFundingSourceDetails()
    {
        return $this->FundingSourceDetails;
    }
    function setFundingSourceDetails($FundingSourceDetails, $charset = 'iso-8859-1')
    {
        $this->FundingSourceDetails = $FundingSourceDetails;
        $this->_elements['FundingSourceDetails']['charset'] = $charset;
    }
    function getBrandName()
    {
        return $this->BrandName;
    }
    function setBrandName($BrandName, $charset = 'iso-8859-1')
    {
        $this->BrandName = $BrandName;
        $this->_elements['BrandName']['charset'] = $charset;
    }
    function getCallbackURL()
    {
        return $this->CallbackURL;
    }
    function setCallbackURL($CallbackURL, $charset = 'iso-8859-1')
    {
        $this->CallbackURL = $CallbackURL;
        $this->_elements['CallbackURL']['charset'] = $charset;
    }
    function getEnhancedCheckoutData()
    {
        return $this->EnhancedCheckoutData;
    }
    function setEnhancedCheckoutData($EnhancedCheckoutData, $charset = 'iso-8859-1')
    {
        $this->EnhancedCheckoutData = $EnhancedCheckoutData;
        $this->_elements['EnhancedCheckoutData']['charset'] = $charset;
    }
    function getOtherPaymentMethods()
    {
        return $this->OtherPaymentMethods;
    }
    function setOtherPaymentMethods($OtherPaymentMethods, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethods = $OtherPaymentMethods;
        $this->_elements['OtherPaymentMethods']['charset'] = $charset;
    }
    function getBuyerDetails()
    {
        return $this->BuyerDetails;
    }
    function setBuyerDetails($BuyerDetails, $charset = 'iso-8859-1')
    {
        $this->BuyerDetails = $BuyerDetails;
        $this->_elements['BuyerDetails']['charset'] = $charset;
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
    function getFlatRateShippingOptions()
    {
        return $this->FlatRateShippingOptions;
    }
    function setFlatRateShippingOptions($FlatRateShippingOptions, $charset = 'iso-8859-1')
    {
        $this->FlatRateShippingOptions = $FlatRateShippingOptions;
        $this->_elements['FlatRateShippingOptions']['charset'] = $charset;
    }
    function getCallbackTimeout()
    {
        return $this->CallbackTimeout;
    }
    function setCallbackTimeout($CallbackTimeout, $charset = 'iso-8859-1')
    {
        $this->CallbackTimeout = $CallbackTimeout;
        $this->_elements['CallbackTimeout']['charset'] = $charset;
    }
}
