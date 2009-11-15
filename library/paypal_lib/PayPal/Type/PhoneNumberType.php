<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PhoneNumberType
 *
 * @package PayPal
 */
class PhoneNumberType extends XSDSimpleType
{
    /**
     * Country code associated with this phone number.
     */
    var $CountryCode;

    /**
     * Phone number associated with this phone.
     */
    var $PhoneNumber;

    /**
     * Extension associated with this phone number.
     */
    var $Extension;

    function PhoneNumberType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'CountryCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PhoneNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Extension' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCountryCode()
    {
        return $this->CountryCode;
    }
    function setCountryCode($CountryCode, $charset = 'iso-8859-1')
    {
        $this->CountryCode = $CountryCode;
        $this->_elements['CountryCode']['charset'] = $charset;
    }
    function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }
    function setPhoneNumber($PhoneNumber, $charset = 'iso-8859-1')
    {
        $this->PhoneNumber = $PhoneNumber;
        $this->_elements['PhoneNumber']['charset'] = $charset;
    }
    function getExtension()
    {
        return $this->Extension;
    }
    function setExtension($Extension, $charset = 'iso-8859-1')
    {
        $this->Extension = $Extension;
        $this->_elements['Extension']['charset'] = $charset;
    }
}
