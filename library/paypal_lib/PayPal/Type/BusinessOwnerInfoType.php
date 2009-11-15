<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * BusinessOwnerInfoType
 * 
 * BusinessOwnerInfoType
 *
 * @package PayPal
 */
class BusinessOwnerInfoType extends XSDSimpleType
{
    /**
     * Details about the business owner
     */
    var $Owner;

    /**
     * Business owner ’s home telephone number
     */
    var $HomePhone;

    /**
     * Business owner ’s mobile telephone number
     */
    var $MobilePhone;

    /**
     * Business owner ’s social security number
     */
    var $SSN;

    function BusinessOwnerInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Owner' => 
              array (
                'required' => false,
                'type' => 'PayerInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'HomePhone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MobilePhone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SSN' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getOwner()
    {
        return $this->Owner;
    }
    function setOwner($Owner, $charset = 'iso-8859-1')
    {
        $this->Owner = $Owner;
        $this->_elements['Owner']['charset'] = $charset;
    }
    function getHomePhone()
    {
        return $this->HomePhone;
    }
    function setHomePhone($HomePhone, $charset = 'iso-8859-1')
    {
        $this->HomePhone = $HomePhone;
        $this->_elements['HomePhone']['charset'] = $charset;
    }
    function getMobilePhone()
    {
        return $this->MobilePhone;
    }
    function setMobilePhone($MobilePhone, $charset = 'iso-8859-1')
    {
        $this->MobilePhone = $MobilePhone;
        $this->_elements['MobilePhone']['charset'] = $charset;
    }
    function getSSN()
    {
        return $this->SSN;
    }
    function setSSN($SSN, $charset = 'iso-8859-1')
    {
        $this->SSN = $SSN;
        $this->_elements['SSN']['charset'] = $charset;
    }
}
