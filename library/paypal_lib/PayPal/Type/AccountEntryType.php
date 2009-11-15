<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AccountEntryType
 *
 * @package PayPal
 */
class AccountEntryType extends XSDSimpleType
{
    /**
     * Balance as of a given entry, can be 0.00.
     */
    var $Balance;

    /**
     * Credit Amount for a detail entry, can be 0.00.
     */
    var $Credit;

    /**
     * Date entry was posted, in GMT.
     */
    var $Date;

    /**
     * Debit Amount for this detail entry, can be 0.00.
     */
    var $Debit;

    /**
     * Item number if transaction is associated with an auction or 0 if no item is
     * associated with an account entry.
     */
    var $ItemID;

    /**
     * Memo line for an account entry, can be empty string.
     */
    var $Memo;

    /**
     * eBay reference number for an account entry.
     */
    var $RefNumber;

    /**
     * Integer code for account details entry type. This element element specifies an
     * index to a table of explanations for accounting charges.
     */
    var $AccountEntryDetailsType;

    function AccountEntryType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Balance' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Credit' => 
              array (
                'required' => true,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Date' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Debit' => 
              array (
                'required' => true,
                'type' => 'AmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemID' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Memo' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RefNumber' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AccountEntryDetailsType' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBalance()
    {
        return $this->Balance;
    }
    function setBalance($Balance, $charset = 'iso-8859-1')
    {
        $this->Balance = $Balance;
        $this->_elements['Balance']['charset'] = $charset;
    }
    function getCredit()
    {
        return $this->Credit;
    }
    function setCredit($Credit, $charset = 'iso-8859-1')
    {
        $this->Credit = $Credit;
        $this->_elements['Credit']['charset'] = $charset;
    }
    function getDate()
    {
        return $this->Date;
    }
    function setDate($Date, $charset = 'iso-8859-1')
    {
        $this->Date = $Date;
        $this->_elements['Date']['charset'] = $charset;
    }
    function getDebit()
    {
        return $this->Debit;
    }
    function setDebit($Debit, $charset = 'iso-8859-1')
    {
        $this->Debit = $Debit;
        $this->_elements['Debit']['charset'] = $charset;
    }
    function getItemID()
    {
        return $this->ItemID;
    }
    function setItemID($ItemID, $charset = 'iso-8859-1')
    {
        $this->ItemID = $ItemID;
        $this->_elements['ItemID']['charset'] = $charset;
    }
    function getMemo()
    {
        return $this->Memo;
    }
    function setMemo($Memo, $charset = 'iso-8859-1')
    {
        $this->Memo = $Memo;
        $this->_elements['Memo']['charset'] = $charset;
    }
    function getRefNumber()
    {
        return $this->RefNumber;
    }
    function setRefNumber($RefNumber, $charset = 'iso-8859-1')
    {
        $this->RefNumber = $RefNumber;
        $this->_elements['RefNumber']['charset'] = $charset;
    }
    function getAccountEntryDetailsType()
    {
        return $this->AccountEntryDetailsType;
    }
    function setAccountEntryDetailsType($AccountEntryDetailsType, $charset = 'iso-8859-1')
    {
        $this->AccountEntryDetailsType = $AccountEntryDetailsType;
        $this->_elements['AccountEntryDetailsType']['charset'] = $charset;
    }
}
