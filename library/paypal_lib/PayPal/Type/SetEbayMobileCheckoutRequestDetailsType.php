<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SetEbayMobileCheckoutRequestDetailsType
 *
 * @package PayPal
 */
class SetEbayMobileCheckoutRequestDetailsType extends XSDSimpleType
{
    /**
     * The value 'Auction' indicates that user is coming to checkout after an auction
     * ended. A value of 'BuyItNow' indicates if the user is coming to checkout by
     * clicking on the 'buy it now' button in a chinese auction. A value of
     * 'FixedPriceItem' indicates that user clicked on 'Buy it now' on a fixed price
     * item. A value of Autopay indicates autopay (or immediate pay) which is not
     * supported at the moment.
     */
    var $CheckoutType;

    /**
     * An item number assigned to the item in eBay database.
     */
    var $ItemId;

    /**
     * An Transaction id assigned to the item in eBay database. In case of Chinese
     * auction Item Id itself indicates Transaction Id. Transaction Id in this case is
     * Zero.
     */
    var $TransactionId;

    /**
     * An id indicating the site on which the item was listed.
     */
    var $SiteId;

    /**
     * Buyers ebay Id.
     */
    var $BuyerId;

    /**
     * Indicating the client type. Weather it is WAP or J2ME. A value of 'WAP'
     * indicates WAP. A value of 'J2MEClient' indicates J2ME client.
     */
    var $ClientType;

    /**
     * The phone number of the buyer's mobile device, if available.
     */
    var $BuyerPhone;

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
     * Specify quantity in case it is an immediate pay (or autopay) item.
     */
    var $Quantity;

    /**
     * Cost of this item before tax and shipping.You must set the currencyID attribute
     * to one of the three-character currency codes for any of the supported PayPal
     * currencies.Used only for autopay items.
     */
    var $ItemAmount;

    function SetEbayMobileCheckoutRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'CheckoutType' => 
              array (
                'required' => true,
                'type' => 'EbayCheckoutType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemId' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TransactionId' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SiteId' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerId' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ClientType' => 
              array (
                'required' => true,
                'type' => 'DyneticClientType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BuyerPhone' => 
              array (
                'required' => false,
                'type' => 'PhoneNumberType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReturnURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CancelURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Quantity' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCheckoutType()
    {
        return $this->CheckoutType;
    }
    function setCheckoutType($CheckoutType, $charset = 'iso-8859-1')
    {
        $this->CheckoutType = $CheckoutType;
        $this->_elements['CheckoutType']['charset'] = $charset;
    }
    function getItemId()
    {
        return $this->ItemId;
    }
    function setItemId($ItemId, $charset = 'iso-8859-1')
    {
        $this->ItemId = $ItemId;
        $this->_elements['ItemId']['charset'] = $charset;
    }
    function getTransactionId()
    {
        return $this->TransactionId;
    }
    function setTransactionId($TransactionId, $charset = 'iso-8859-1')
    {
        $this->TransactionId = $TransactionId;
        $this->_elements['TransactionId']['charset'] = $charset;
    }
    function getSiteId()
    {
        return $this->SiteId;
    }
    function setSiteId($SiteId, $charset = 'iso-8859-1')
    {
        $this->SiteId = $SiteId;
        $this->_elements['SiteId']['charset'] = $charset;
    }
    function getBuyerId()
    {
        return $this->BuyerId;
    }
    function setBuyerId($BuyerId, $charset = 'iso-8859-1')
    {
        $this->BuyerId = $BuyerId;
        $this->_elements['BuyerId']['charset'] = $charset;
    }
    function getClientType()
    {
        return $this->ClientType;
    }
    function setClientType($ClientType, $charset = 'iso-8859-1')
    {
        $this->ClientType = $ClientType;
        $this->_elements['ClientType']['charset'] = $charset;
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
    function getQuantity()
    {
        return $this->Quantity;
    }
    function setQuantity($Quantity, $charset = 'iso-8859-1')
    {
        $this->Quantity = $Quantity;
        $this->_elements['Quantity']['charset'] = $charset;
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
}
