<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SellerType
 * 
 * Information about user used by selling applications there are number of required
 * elements - they will always show up for seller node there is not such a call to
 * do revise seller info. only added minoccur=0 to elements that will not show up
 * in every type of request/responce
 *
 * @package PayPal
 */
class SellerType extends XSDSimpleType
{
    var $AllowPaymentEdit;

    var $BillingCurrency;

    var $CheckoutEnabled;

    var $CIPBankAccountStored;

    var $GoodStanding;

    var $LiveAuctionAuthorized;

    /**
     * Indicates whether the user has elected to participate as a seller in the
     * Merchandising Manager feature.
     */
    var $MerchandizingPref;

    var $QualifiesForB2BVAT;

    var $SellerLevel;

    var $SellerPaymentAddress;

    var $SchedulingInfo;

    var $StoreOwner;

    var $StoreURL;

    function SellerType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AllowPaymentEdit' => 
              array (
                'required' => true,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingCurrency' => 
              array (
                'required' => false,
                'type' => 'CurrencyCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CheckoutEnabled' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CIPBankAccountStored' => 
              array (
                'required' => true,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'GoodStanding' => 
              array (
                'required' => true,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LiveAuctionAuthorized' => 
              array (
                'required' => true,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MerchandizingPref' => 
              array (
                'required' => true,
                'type' => 'MerchandizingPrefCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'QualifiesForB2BVAT' => 
              array (
                'required' => true,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellerLevel' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SellerPaymentAddress' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SchedulingInfo' => 
              array (
                'required' => false,
                'type' => 'SchedulingInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StoreOwner' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StoreURL' => 
              array (
                'required' => false,
                'type' => 'anyURI',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAllowPaymentEdit()
    {
        return $this->AllowPaymentEdit;
    }
    function setAllowPaymentEdit($AllowPaymentEdit, $charset = 'iso-8859-1')
    {
        $this->AllowPaymentEdit = $AllowPaymentEdit;
        $this->_elements['AllowPaymentEdit']['charset'] = $charset;
    }
    function getBillingCurrency()
    {
        return $this->BillingCurrency;
    }
    function setBillingCurrency($BillingCurrency, $charset = 'iso-8859-1')
    {
        $this->BillingCurrency = $BillingCurrency;
        $this->_elements['BillingCurrency']['charset'] = $charset;
    }
    function getCheckoutEnabled()
    {
        return $this->CheckoutEnabled;
    }
    function setCheckoutEnabled($CheckoutEnabled, $charset = 'iso-8859-1')
    {
        $this->CheckoutEnabled = $CheckoutEnabled;
        $this->_elements['CheckoutEnabled']['charset'] = $charset;
    }
    function getCIPBankAccountStored()
    {
        return $this->CIPBankAccountStored;
    }
    function setCIPBankAccountStored($CIPBankAccountStored, $charset = 'iso-8859-1')
    {
        $this->CIPBankAccountStored = $CIPBankAccountStored;
        $this->_elements['CIPBankAccountStored']['charset'] = $charset;
    }
    function getGoodStanding()
    {
        return $this->GoodStanding;
    }
    function setGoodStanding($GoodStanding, $charset = 'iso-8859-1')
    {
        $this->GoodStanding = $GoodStanding;
        $this->_elements['GoodStanding']['charset'] = $charset;
    }
    function getLiveAuctionAuthorized()
    {
        return $this->LiveAuctionAuthorized;
    }
    function setLiveAuctionAuthorized($LiveAuctionAuthorized, $charset = 'iso-8859-1')
    {
        $this->LiveAuctionAuthorized = $LiveAuctionAuthorized;
        $this->_elements['LiveAuctionAuthorized']['charset'] = $charset;
    }
    function getMerchandizingPref()
    {
        return $this->MerchandizingPref;
    }
    function setMerchandizingPref($MerchandizingPref, $charset = 'iso-8859-1')
    {
        $this->MerchandizingPref = $MerchandizingPref;
        $this->_elements['MerchandizingPref']['charset'] = $charset;
    }
    function getQualifiesForB2BVAT()
    {
        return $this->QualifiesForB2BVAT;
    }
    function setQualifiesForB2BVAT($QualifiesForB2BVAT, $charset = 'iso-8859-1')
    {
        $this->QualifiesForB2BVAT = $QualifiesForB2BVAT;
        $this->_elements['QualifiesForB2BVAT']['charset'] = $charset;
    }
    function getSellerLevel()
    {
        return $this->SellerLevel;
    }
    function setSellerLevel($SellerLevel, $charset = 'iso-8859-1')
    {
        $this->SellerLevel = $SellerLevel;
        $this->_elements['SellerLevel']['charset'] = $charset;
    }
    function getSellerPaymentAddress()
    {
        return $this->SellerPaymentAddress;
    }
    function setSellerPaymentAddress($SellerPaymentAddress, $charset = 'iso-8859-1')
    {
        $this->SellerPaymentAddress = $SellerPaymentAddress;
        $this->_elements['SellerPaymentAddress']['charset'] = $charset;
    }
    function getSchedulingInfo()
    {
        return $this->SchedulingInfo;
    }
    function setSchedulingInfo($SchedulingInfo, $charset = 'iso-8859-1')
    {
        $this->SchedulingInfo = $SchedulingInfo;
        $this->_elements['SchedulingInfo']['charset'] = $charset;
    }
    function getStoreOwner()
    {
        return $this->StoreOwner;
    }
    function setStoreOwner($StoreOwner, $charset = 'iso-8859-1')
    {
        $this->StoreOwner = $StoreOwner;
        $this->_elements['StoreOwner']['charset'] = $charset;
    }
    function getStoreURL()
    {
        return $this->StoreURL;
    }
    function setStoreURL($StoreURL, $charset = 'iso-8859-1')
    {
        $this->StoreURL = $StoreURL;
        $this->_elements['StoreURL']['charset'] = $charset;
    }
}
