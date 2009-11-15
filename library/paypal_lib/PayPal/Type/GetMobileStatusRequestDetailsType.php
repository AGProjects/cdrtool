<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * GetMobileStatusRequestDetailsType
 *
 * @package PayPal
 */
class GetMobileStatusRequestDetailsType extends XSDSimpleType
{
    /**
     * Phone number for status inquiry
     */
    var $Phone;

    function GetMobileStatusRequestDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Phone' => 
              array (
                'required' => true,
                'type' => 'PhoneNumberType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPhone()
    {
        return $this->Phone;
    }
    function setPhone($Phone, $charset = 'iso-8859-1')
    {
        $this->Phone = $Phone;
        $this->_elements['Phone']['charset'] = $charset;
    }
}
