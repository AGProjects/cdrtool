<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * MerchantPullInfoType
 * 
 * MerchantPullInfoType Information about the merchant pull.
 *
 * @package PayPal
 */
class MerchantPullInfoType extends XSDSimpleType
{
    /**
     * Current status of billing agreement
     */
    var $MpStatus;

    /**
     * Monthly maximum payment amount
     */
    var $MpMax;

    /**
     * The value of the mp_custom variable that you specified in a FORM submission to
     * PayPal during the creation or updating of a customer billing agreement
     */
    var $MpCustom;

    /**
     * The value of the mp_desc variable (description of goods or services) associated
     * with the billing agreement
     */
    var $Desc;

    /**
     * Invoice value as set by BillUserRequest API call
     */
    var $Invoice;

    /**
     * Custom field as set by BillUserRequest API call
     */
    var $Custom;

    /**
     * Note: This field is no longer used and is always empty.
     */
    var $PaymentSourceID;

    function MerchantPullInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'MpStatus' => 
              array (
                'required' => true,
                'type' => 'MerchantPullStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MpMax' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MpCustom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Desc' => 
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
              'PaymentSourceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getMpStatus()
    {
        return $this->MpStatus;
    }
    function setMpStatus($MpStatus, $charset = 'iso-8859-1')
    {
        $this->MpStatus = $MpStatus;
        $this->_elements['MpStatus']['charset'] = $charset;
    }
    function getMpMax()
    {
        return $this->MpMax;
    }
    function setMpMax($MpMax, $charset = 'iso-8859-1')
    {
        $this->MpMax = $MpMax;
        $this->_elements['MpMax']['charset'] = $charset;
    }
    function getMpCustom()
    {
        return $this->MpCustom;
    }
    function setMpCustom($MpCustom, $charset = 'iso-8859-1')
    {
        $this->MpCustom = $MpCustom;
        $this->_elements['MpCustom']['charset'] = $charset;
    }
    function getDesc()
    {
        return $this->Desc;
    }
    function setDesc($Desc, $charset = 'iso-8859-1')
    {
        $this->Desc = $Desc;
        $this->_elements['Desc']['charset'] = $charset;
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
    function getPaymentSourceID()
    {
        return $this->PaymentSourceID;
    }
    function setPaymentSourceID($PaymentSourceID, $charset = 'iso-8859-1')
    {
        $this->PaymentSourceID = $PaymentSourceID;
        $this->_elements['PaymentSourceID']['charset'] = $charset;
    }
}
