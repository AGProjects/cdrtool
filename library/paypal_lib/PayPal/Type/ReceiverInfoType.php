<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ReceiverInfoType
 * 
 * ReceiverInfoType Receiver information.
 *
 * @package PayPal
 */
class ReceiverInfoType extends XSDSimpleType
{
    /**
     * Email address or account ID of the payment recipient (the seller). Equivalent to
     * Receiver if payment is sent to primary account.
     */
    var $Business;

    /**
     * Primary email address of the payment recipient (the seller). If you are the
     * recipient of the payment and the payment is sent to your non-primary email
     * address, the value of Receiver is still your primary email address.
     */
    var $Receiver;

    /**
     * Unique account ID of the payment recipient (the seller). This value is the same
     * as the value of the recipient's referral ID.
     */
    var $ReceiverID;

    function ReceiverInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Business' => 
              array (
                'required' => true,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Receiver' => 
              array (
                'required' => true,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReceiverID' => 
              array (
                'required' => true,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBusiness()
    {
        return $this->Business;
    }
    function setBusiness($Business, $charset = 'iso-8859-1')
    {
        $this->Business = $Business;
        $this->_elements['Business']['charset'] = $charset;
    }
    function getReceiver()
    {
        return $this->Receiver;
    }
    function setReceiver($Receiver, $charset = 'iso-8859-1')
    {
        $this->Receiver = $Receiver;
        $this->_elements['Receiver']['charset'] = $charset;
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
}
