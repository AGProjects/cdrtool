<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * RecurringPaymentsProfileDetailsType
 *
 * @package PayPal
 */
class RecurringPaymentsProfileDetailsType extends XSDSimpleType
{
    /**
     * Subscriber name - if missing, will use name in buyer's account
     */
    var $SubscriberName;

    /**
     * Subscriber address - if missing, will use address in buyer's account
     */
    var $SubscriberShippingAddress;

    /**
     * When does this Profile begin billing?
     */
    var $BillingStartDate;

    /**
     * Your own unique invoice or tracking number.
     */
    var $ProfileReference;

    function RecurringPaymentsProfileDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'SubscriberName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SubscriberShippingAddress' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'BillingStartDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ProfileReference' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getSubscriberName()
    {
        return $this->SubscriberName;
    }
    function setSubscriberName($SubscriberName, $charset = 'iso-8859-1')
    {
        $this->SubscriberName = $SubscriberName;
        $this->_elements['SubscriberName']['charset'] = $charset;
    }
    function getSubscriberShippingAddress()
    {
        return $this->SubscriberShippingAddress;
    }
    function setSubscriberShippingAddress($SubscriberShippingAddress, $charset = 'iso-8859-1')
    {
        $this->SubscriberShippingAddress = $SubscriberShippingAddress;
        $this->_elements['SubscriberShippingAddress']['charset'] = $charset;
    }
    function getBillingStartDate()
    {
        return $this->BillingStartDate;
    }
    function setBillingStartDate($BillingStartDate, $charset = 'iso-8859-1')
    {
        $this->BillingStartDate = $BillingStartDate;
        $this->_elements['BillingStartDate']['charset'] = $charset;
    }
    function getProfileReference()
    {
        return $this->ProfileReference;
    }
    function setProfileReference($ProfileReference, $charset = 'iso-8859-1')
    {
        $this->ProfileReference = $ProfileReference;
        $this->_elements['ProfileReference']['charset'] = $charset;
    }
}
