<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ReferenceCreditCardDetailsType
 * 
 * CreditCardDetailsType for DCC Reference Transaction Information about a Credit
 * Card.
 *
 * @package PayPal
 */
class ReferenceCreditCardDetailsType extends XSDSimpleType
{
    var $CreditCardNumberType;

    var $ExpMonth;

    var $ExpYear;

    var $CardOwnerName;

    var $BillingAddress;

    var $CVV2;

    var $StartMonth;

    var $StartYear;

    var $IssueNumber;

    function ReferenceCreditCardDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'CreditCardNumberType' => 
              array (
                'required' => false,
                'type' => 'CreditCardNumberTypeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExpMonth' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExpYear' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CardOwnerName' => 
              array (
                'required' => false,
                'type' => 'PersonNameType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingAddress' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CVV2' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StartMonth' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StartYear' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'IssueNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCreditCardNumberType()
    {
        return $this->CreditCardNumberType;
    }
    function setCreditCardNumberType($CreditCardNumberType, $charset = 'iso-8859-1')
    {
        $this->CreditCardNumberType = $CreditCardNumberType;
        $this->_elements['CreditCardNumberType']['charset'] = $charset;
    }
    function getExpMonth()
    {
        return $this->ExpMonth;
    }
    function setExpMonth($ExpMonth, $charset = 'iso-8859-1')
    {
        $this->ExpMonth = $ExpMonth;
        $this->_elements['ExpMonth']['charset'] = $charset;
    }
    function getExpYear()
    {
        return $this->ExpYear;
    }
    function setExpYear($ExpYear, $charset = 'iso-8859-1')
    {
        $this->ExpYear = $ExpYear;
        $this->_elements['ExpYear']['charset'] = $charset;
    }
    function getCardOwnerName()
    {
        return $this->CardOwnerName;
    }
    function setCardOwnerName($CardOwnerName, $charset = 'iso-8859-1')
    {
        $this->CardOwnerName = $CardOwnerName;
        $this->_elements['CardOwnerName']['charset'] = $charset;
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
    function getCVV2()
    {
        return $this->CVV2;
    }
    function setCVV2($CVV2, $charset = 'iso-8859-1')
    {
        $this->CVV2 = $CVV2;
        $this->_elements['CVV2']['charset'] = $charset;
    }
    function getStartMonth()
    {
        return $this->StartMonth;
    }
    function setStartMonth($StartMonth, $charset = 'iso-8859-1')
    {
        $this->StartMonth = $StartMonth;
        $this->_elements['StartMonth']['charset'] = $charset;
    }
    function getStartYear()
    {
        return $this->StartYear;
    }
    function setStartYear($StartYear, $charset = 'iso-8859-1')
    {
        $this->StartYear = $StartYear;
        $this->_elements['StartYear']['charset'] = $charset;
    }
    function getIssueNumber()
    {
        return $this->IssueNumber;
    }
    function setIssueNumber($IssueNumber, $charset = 'iso-8859-1')
    {
        $this->IssueNumber = $IssueNumber;
        $this->_elements['IssueNumber']['charset'] = $charset;
    }
}
