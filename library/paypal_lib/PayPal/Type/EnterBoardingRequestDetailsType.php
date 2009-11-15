<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * EnterBoardingRequestDetailsType
 *
 * @package PayPal
 */
class EnterBoardingRequestDetailsType extends XSDSimpleType
{
    /**
     * Onboarding program code given to you by PayPal.
     */
    var $ProgramCode;

    /**
     * A list of comma-separated values that indicate the PayPal products you are
     * implementing for this merchant:
     */
    var $ProductList;

    /**
     * Any custom information you want to store for this partner
     */
    var $PartnerCustom;

    /**
     * The URL for the logo displayed on the PayPal Partner Welcome Page.
     */
    var $ImageUrl;

    /**
     * Marketing category tha configures the graphic displayed n the PayPal Partner
     * Welcome page.
     */
    var $MarketingCategory;

    /**
     * Information about the merchant ’s business
     */
    var $BusinessInfo;

    /**
     * Information about the merchant (the business owner)
     */
    var $OwnerInfo;

    /**
     * Information about the merchant's bank account
     */
    var $BankAccount;

    function EnterBoardingRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ProgramCode' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProductList' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PartnerCustom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ImageUrl' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MarketingCategory' => 
              array (
                'required' => false,
                'type' => 'MarketingCategoryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BusinessInfo' => 
              array (
                'required' => false,
                'type' => 'BusinessInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OwnerInfo' => 
              array (
                'required' => false,
                'type' => 'BusinessOwnerInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BankAccount' => 
              array (
                'required' => false,
                'type' => 'BankAccountDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getProgramCode()
    {
        return $this->ProgramCode;
    }
    function setProgramCode($ProgramCode, $charset = 'iso-8859-1')
    {
        $this->ProgramCode = $ProgramCode;
        $this->_elements['ProgramCode']['charset'] = $charset;
    }
    function getProductList()
    {
        return $this->ProductList;
    }
    function setProductList($ProductList, $charset = 'iso-8859-1')
    {
        $this->ProductList = $ProductList;
        $this->_elements['ProductList']['charset'] = $charset;
    }
    function getPartnerCustom()
    {
        return $this->PartnerCustom;
    }
    function setPartnerCustom($PartnerCustom, $charset = 'iso-8859-1')
    {
        $this->PartnerCustom = $PartnerCustom;
        $this->_elements['PartnerCustom']['charset'] = $charset;
    }
    function getImageUrl()
    {
        return $this->ImageUrl;
    }
    function setImageUrl($ImageUrl, $charset = 'iso-8859-1')
    {
        $this->ImageUrl = $ImageUrl;
        $this->_elements['ImageUrl']['charset'] = $charset;
    }
    function getMarketingCategory()
    {
        return $this->MarketingCategory;
    }
    function setMarketingCategory($MarketingCategory, $charset = 'iso-8859-1')
    {
        $this->MarketingCategory = $MarketingCategory;
        $this->_elements['MarketingCategory']['charset'] = $charset;
    }
    function getBusinessInfo()
    {
        return $this->BusinessInfo;
    }
    function setBusinessInfo($BusinessInfo, $charset = 'iso-8859-1')
    {
        $this->BusinessInfo = $BusinessInfo;
        $this->_elements['BusinessInfo']['charset'] = $charset;
    }
    function getOwnerInfo()
    {
        return $this->OwnerInfo;
    }
    function setOwnerInfo($OwnerInfo, $charset = 'iso-8859-1')
    {
        $this->OwnerInfo = $OwnerInfo;
        $this->_elements['OwnerInfo']['charset'] = $charset;
    }
    function getBankAccount()
    {
        return $this->BankAccount;
    }
    function setBankAccount($BankAccount, $charset = 'iso-8859-1')
    {
        $this->BankAccount = $BankAccount;
        $this->_elements['BankAccount']['charset'] = $charset;
    }
}
