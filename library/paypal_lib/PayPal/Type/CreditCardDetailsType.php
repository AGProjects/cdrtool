<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CreditCardDetailsType
 * 
 * CreditCardDetailsType Information about a Credit Card.
 *
 * @package PayPal
 */
class CreditCardDetailsType extends XSDSimpleType
{
    var $CreditCardType;

    var $CreditCardNumber;

    var $ExpMonth;

    var $ExpYear;

    var $CardOwner;

    var $CVV2;

    var $StartMonth;

    var $StartYear;

    var $IssueNumber;

    var $ThreeDSecureRequest;

    function CreditCardDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'CreditCardType' => 
              array (
                'required' => false,
                'type' => 'CreditCardTypeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreditCardNumber' => 
              array (
                'required' => false,
                'type' => 'string',
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
              'CardOwner' => 
              array (
                'required' => false,
                'type' => 'PayerInfoType',
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
              'ThreeDSecureRequest' => 
              array (
                'required' => false,
                'type' => 'ThreeDSecureRequestType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCreditCardType()
    {
        return $this->CreditCardType;
    }
    function setCreditCardType($CreditCardType, $charset = 'iso-8859-1')
    {
        $this->CreditCardType = $CreditCardType;
        $this->_elements['CreditCardType']['charset'] = $charset;
    }
    function getCreditCardNumber()
    {
        return $this->CreditCardNumber;
    }
    function setCreditCardNumber($CreditCardNumber, $charset = 'iso-8859-1')
    {
        $this->CreditCardNumber = $CreditCardNumber;
        $this->_elements['CreditCardNumber']['charset'] = $charset;
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
    function getCardOwner()
    {
        return $this->CardOwner;
    }
    function setCardOwner($CardOwner, $charset = 'iso-8859-1')
    {
        $this->CardOwner = $CardOwner;
        $this->_elements['CardOwner']['charset'] = $charset;
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
    function getThreeDSecureRequest()
    {
        return $this->ThreeDSecureRequest;
    }
    function setThreeDSecureRequest($ThreeDSecureRequest, $charset = 'iso-8859-1')
    {
        $this->ThreeDSecureRequest = $ThreeDSecureRequest;
        $this->_elements['ThreeDSecureRequest']['charset'] = $charset;
    }
}
