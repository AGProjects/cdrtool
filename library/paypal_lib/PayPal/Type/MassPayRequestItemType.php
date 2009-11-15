<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * MassPayRequestItemType
 * 
 * MassPayRequestItemType
 *
 * @package PayPal
 */
class MassPayRequestItemType extends XSDSimpleType
{
    /**
     * Email address of recipient.
     */
    var $ReceiverEmail;

    /**
     * Phone number of recipient.
     */
    var $ReceiverPhone;

    /**
     * Unique PayPal customer account number. This value corresponds to the value of
     * PayerID returned by GetTransactionDetails.
     */
    var $ReceiverID;

    /**
     * Payment amount. You must set the currencyID attribute to one of the
     * three-character currency codes for any of the supported PayPal currencies.
     */
    var $Amount;

    /**
     * Transaction-specific identification number for tracking in an accounting system.
     */
    var $UniqueId;

    /**
     * Custom note for each recipient.
     */
    var $Note;

    function MassPayRequestItemType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'ReceiverEmail' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ReceiverPhone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ReceiverID' => 
              array (
                'required' => false,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'UniqueId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Note' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getReceiverEmail()
    {
        return $this->ReceiverEmail;
    }
    function setReceiverEmail($ReceiverEmail, $charset = 'iso-8859-1')
    {
        $this->ReceiverEmail = $ReceiverEmail;
        $this->_elements['ReceiverEmail']['charset'] = $charset;
    }
    function getReceiverPhone()
    {
        return $this->ReceiverPhone;
    }
    function setReceiverPhone($ReceiverPhone, $charset = 'iso-8859-1')
    {
        $this->ReceiverPhone = $ReceiverPhone;
        $this->_elements['ReceiverPhone']['charset'] = $charset;
    }
    function getReceiverID()
    {
        return $this->ReceiverID;
    }
    function setReceiverID($ReceiverID, $charset = 'iso-8859-1')
    {
        $this->ReceiverID = $ReceiverID;
        $this->_elements['ReceiverID']['charset'] = $charset;
    }
    function getAmount()
    {
        return $this->Amount;
    }
    function setAmount($Amount, $charset = 'iso-8859-1')
    {
        $this->Amount = $Amount;
        $this->_elements['Amount']['charset'] = $charset;
    }
    function getUniqueId()
    {
        return $this->UniqueId;
    }
    function setUniqueId($UniqueId, $charset = 'iso-8859-1')
    {
        $this->UniqueId = $UniqueId;
        $this->_elements['UniqueId']['charset'] = $charset;
    }
    function getNote()
    {
        return $this->Note;
    }
    function setNote($Note, $charset = 'iso-8859-1')
    {
        $this->Note = $Note;
        $this->_elements['Note']['charset'] = $charset;
    }
}
