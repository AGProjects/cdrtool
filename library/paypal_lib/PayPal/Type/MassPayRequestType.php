<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * MassPayRequestType
 *
 * @package PayPal
 */
class MassPayRequestType extends AbstractRequestType
{
    /**
     * Subject line of the email sent to all recipients. This subject is not contained
     * in the input file; you must create it with your application.
     */
    var $EmailSubject;

    /**
     * Indicates how you identify the recipients of payments in all MassPayItems:
     * either by EmailAddress (ReceiverEmail in MassPayItem), PhoneNumber
     * (ReceiverPhone in MassPayItem), or by UserID (ReceiverID in MassPayItem).
     */
    var $ReceiverType;

    /**
     * Known as BN code, to track the partner referred merchant transactions.
     */
    var $ButtonSource;

    /**
     * Details of each payment. A single MassPayRequest can include up to 250
     * MassPayItems.
     */
    var $MassPayItem;

    function MassPayRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'EmailSubject' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ReceiverType' => 
              array (
                'required' => false,
                'type' => 'ReceiverInfoCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonSource' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'MassPayItem' => 
              array (
                'required' => true,
                'type' => 'MassPayRequestItemType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getEmailSubject()
    {
        return $this->EmailSubject;
    }
    function setEmailSubject($EmailSubject, $charset = 'iso-8859-1')
    {
        $this->EmailSubject = $EmailSubject;
        $this->_elements['EmailSubject']['charset'] = $charset;
    }
    function getReceiverType()
    {
        return $this->ReceiverType;
    }
    function setReceiverType($ReceiverType, $charset = 'iso-8859-1')
    {
        $this->ReceiverType = $ReceiverType;
        $this->_elements['ReceiverType']['charset'] = $charset;
    }
    function getButtonSource()
    {
        return $this->ButtonSource;
    }
    function setButtonSource($ButtonSource, $charset = 'iso-8859-1')
    {
        $this->ButtonSource = $ButtonSource;
        $this->_elements['ButtonSource']['charset'] = $charset;
    }
    function getMassPayItem()
    {
        return $this->MassPayItem;
    }
    function setMassPayItem($MassPayItem, $charset = 'iso-8859-1')
    {
        $this->MassPayItem = $MassPayItem;
        $this->_elements['MassPayItem']['charset'] = $charset;
    }
}
