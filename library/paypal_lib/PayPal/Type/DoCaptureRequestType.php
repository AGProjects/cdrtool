<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoCaptureRequestType
 *
 * @package PayPal
 */
class DoCaptureRequestType extends AbstractRequestType
{
    /**
     * The authorization identification number of the payment you want to capture.
     */
    var $AuthorizationID;

    /**
     * Amount to authorize. You must set the currencyID attribute to USD.
     */
    var $Amount;

    /**
     * Indicates if this capture is the last capture you intend to make. The default is
     * Complete. If CompleteType is Complete, any remaining amount of the original
     * reauthorized transaction is automatically voided.
     */
    var $CompleteType;

    /**
     * An informational note about this settlement that is displayed to the payer in
     * email and in transaction history.
     */
    var $Note;

    /**
     * Your invoice number or other identification number.
     */
    var $InvoiceID;

    var $EnhancedData;

    /**
     * dynamic descriptor
     */
    var $Descriptor;

    function DoCaptureRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'AuthorizationID' => 
              array (
                'required' => true,
                'type' => 'AuthorizationId',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'CompleteType' => 
              array (
                'required' => true,
                'type' => 'CompleteCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Note' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'EnhancedData' => 
              array (
                'required' => false,
                'type' => 'EnhancedDataType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Descriptor' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getAuthorizationID()
    {
        return $this->AuthorizationID;
    }
    function setAuthorizationID($AuthorizationID, $charset = 'iso-8859-1')
    {
        $this->AuthorizationID = $AuthorizationID;
        $this->_elements['AuthorizationID']['charset'] = $charset;
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
    function getCompleteType()
    {
        return $this->CompleteType;
    }
    function setCompleteType($CompleteType, $charset = 'iso-8859-1')
    {
        $this->CompleteType = $CompleteType;
        $this->_elements['CompleteType']['charset'] = $charset;
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
    function getInvoiceID()
    {
        return $this->InvoiceID;
    }
    function setInvoiceID($InvoiceID, $charset = 'iso-8859-1')
    {
        $this->InvoiceID = $InvoiceID;
        $this->_elements['InvoiceID']['charset'] = $charset;
    }
    function getEnhancedData()
    {
        return $this->EnhancedData;
    }
    function setEnhancedData($EnhancedData, $charset = 'iso-8859-1')
    {
        $this->EnhancedData = $EnhancedData;
        $this->_elements['EnhancedData']['charset'] = $charset;
    }
    function getDescriptor()
    {
        return $this->Descriptor;
    }
    function setDescriptor($Descriptor, $charset = 'iso-8859-1')
    {
        $this->Descriptor = $Descriptor;
        $this->_elements['Descriptor']['charset'] = $charset;
    }
}
