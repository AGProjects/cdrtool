<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * DoNonReferencedCreditRequestDetailsType
 *
 * @package PayPal
 */
class DoNonReferencedCreditRequestDetailsType extends XSDSimpleType
{
    var $Amount;

    var $NetAmount;

    var $TaxAmount;

    var $ShippingAmount;

    var $CreditCard;

    var $ReceiverEmail;

    var $Comment;

    function DoNonReferencedCreditRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'NetAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TaxAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreditCard' => 
              array (
                'required' => true,
                'type' => 'CreditCardDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReceiverEmail' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Comment' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
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
    function getNetAmount()
    {
        return $this->NetAmount;
    }
    function setNetAmount($NetAmount, $charset = 'iso-8859-1')
    {
        $this->NetAmount = $NetAmount;
        $this->_elements['NetAmount']['charset'] = $charset;
    }
    function getTaxAmount()
    {
        return $this->TaxAmount;
    }
    function setTaxAmount($TaxAmount, $charset = 'iso-8859-1')
    {
        $this->TaxAmount = $TaxAmount;
        $this->_elements['TaxAmount']['charset'] = $charset;
    }
    function getShippingAmount()
    {
        return $this->ShippingAmount;
    }
    function setShippingAmount($ShippingAmount, $charset = 'iso-8859-1')
    {
        $this->ShippingAmount = $ShippingAmount;
        $this->_elements['ShippingAmount']['charset'] = $charset;
    }
    function getCreditCard()
    {
        return $this->CreditCard;
    }
    function setCreditCard($CreditCard, $charset = 'iso-8859-1')
    {
        $this->CreditCard = $CreditCard;
        $this->_elements['CreditCard']['charset'] = $charset;
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
    function getComment()
    {
        return $this->Comment;
    }
    function setComment($Comment, $charset = 'iso-8859-1')
    {
        $this->Comment = $Comment;
        $this->_elements['Comment']['charset'] = $charset;
    }
}
