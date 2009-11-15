<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * BankAccountDetailsType
 * 
 * BankAccountDetailsType
 *
 * @package PayPal
 */
class BankAccountDetailsType extends XSDSimpleType
{
    /**
     * Name of bank
     */
    var $Name;

    /**
     * Type of bank account: Checking or Savings
     */
    var $Type;

    /**
     * Merchant ’s bank routing number
     */
    var $RoutingNumber;

    /**
     * Merchant ’s bank account number
     */
    var $AccountNumber;

    function BankAccountDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Name' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Type' => 
              array (
                'required' => true,
                'type' => 'BankAccountTypeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RoutingNumber' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AccountNumber' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getName()
    {
        return $this->Name;
    }
    function setName($Name, $charset = 'iso-8859-1')
    {
        $this->Name = $Name;
        $this->_elements['Name']['charset'] = $charset;
    }
    function getType()
    {
        return $this->Type;
    }
    function setType($Type, $charset = 'iso-8859-1')
    {
        $this->Type = $Type;
        $this->_elements['Type']['charset'] = $charset;
    }
    function getRoutingNumber()
    {
        return $this->RoutingNumber;
    }
    function setRoutingNumber($RoutingNumber, $charset = 'iso-8859-1')
    {
        $this->RoutingNumber = $RoutingNumber;
        $this->_elements['RoutingNumber']['charset'] = $charset;
    }
    function getAccountNumber()
    {
        return $this->AccountNumber;
    }
    function setAccountNumber($AccountNumber, $charset = 'iso-8859-1')
    {
        $this->AccountNumber = $AccountNumber;
        $this->_elements['AccountNumber']['charset'] = $charset;
    }
}
