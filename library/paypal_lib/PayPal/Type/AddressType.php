<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AddressType
 *
 * @package PayPal
 */
class AddressType extends XSDSimpleType
{
    /**
     * Person's name associated with this address.
     */
    var $Name;

    /**
     * First street address.
     */
    var $Street1;

    /**
     * Second street address.
     */
    var $Street2;

    /**
     * Name of city.
     */
    var $CityName;

    /**
     * State or province.
     */
    var $StateOrProvince;

    /**
     * ISO 3166 standard country code
     */
    var $Country;

    /**
     * IMPORTANT: Do not set this element for SetExpressCheckout,
     * DoExpressCheckoutPayment, DoDirectPayment, CreateRecurringPaymentsProfile or
     * UpdateRecurringPaymentsProfile.
     */
    var $CountryName;

    /**
     * Telephone number associated with this address
     */
    var $Phone;

    var $PostalCode;

    /**
     * IMPORTANT: Do not set this element for SetExpressCheckout,
     * DoExpressCheckoutPayment, DoDirectPayment, CreateRecurringPaymentsProfile, or
     * UpdateRecurringPaymentsProfile.
     */
    var $AddressID;

    /**
     * IMPORTANT: Do not set this element for SetExpressCheckout,
     * DoExpressCheckoutPayment, DoDirectPayment, CreateRecurringPaymentsProfile or
     * UpdateRecurringPaymentsProfile.
     */
    var $AddressOwner;

    /**
     * IMPORTANT: Do not set this element for SetExpressCheckout,
     * DoExpressCheckoutPayment, DoDirectPayment, CreateRecurringPaymentsProfile or
     * UpdateRecurringPaymentsProfile.
     */
    var $ExternalAddressID;

    /**
     * IMPORTANT: Do not set this element for SetExpressCheckout,
     * DoExpressCheckoutPayment, DoDirectPayment, CreateRecurringPaymentsProfile or
     * UpdateRecurringPaymentsProfile.
     */
    var $InternationalName;

    /**
     * IMPORTANT: Do not set this element for SetExpressCheckout,
     * DoExpressCheckoutPayment, DoDirectPayment, CreateRecurringPaymentsProfile or
     * UpdateRecurringPaymentsProfile.
     */
    var $InternationalStateAndCity;

    /**
     * IMPORTANT: Do not set this element for SetExpressCheckout,
     * DoExpressCheckoutPayment, DoDirectPayment, CreateRecurringPaymentsProfile or
     * UpdateRecurringPaymentsProfile.
     */
    var $InternationalStreet;

    /**
     * Status of the address on file with PayPal.
     */
    var $AddressStatus;

    function AddressType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Name' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Street1' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Street2' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CityName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StateOrProvince' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Country' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CountryName' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Phone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PostalCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AddressID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AddressOwner' => 
              array (
                'required' => false,
                'type' => 'AddressOwnerCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExternalAddressID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InternationalName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InternationalStateAndCity' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'InternationalStreet' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AddressStatus' => 
              array (
                'required' => false,
                'type' => 'AddressStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getName()
    {
        return $this->Name;
    }
    function setName($Name, $charset = 'iso-8859-1')
    {
        $this->Name = $Name;
        $this->_elements['Name']['charset'] = $charset;
    }
    function getStreet1()
    {
        return $this->Street1;
    }
    function setStreet1($Street1, $charset = 'iso-8859-1')
    {
        $this->Street1 = $Street1;
        $this->_elements['Street1']['charset'] = $charset;
    }
    function getStreet2()
    {
        return $this->Street2;
    }
    function setStreet2($Street2, $charset = 'iso-8859-1')
    {
        $this->Street2 = $Street2;
        $this->_elements['Street2']['charset'] = $charset;
    }
    function getCityName()
    {
        return $this->CityName;
    }
    function setCityName($CityName, $charset = 'iso-8859-1')
    {
        $this->CityName = $CityName;
        $this->_elements['CityName']['charset'] = $charset;
    }
    function getStateOrProvince()
    {
        return $this->StateOrProvince;
    }
    function setStateOrProvince($StateOrProvince, $charset = 'iso-8859-1')
    {
        $this->StateOrProvince = $StateOrProvince;
        $this->_elements['StateOrProvince']['charset'] = $charset;
    }
    function getCountry()
    {
        return $this->Country;
    }
    function setCountry($Country, $charset = 'iso-8859-1')
    {
        $this->Country = $Country;
        $this->_elements['Country']['charset'] = $charset;
    }
    function getCountryName()
    {
        return $this->CountryName;
    }
    function setCountryName($CountryName, $charset = 'iso-8859-1')
    {
        $this->CountryName = $CountryName;
        $this->_elements['CountryName']['charset'] = $charset;
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
    function getPostalCode()
    {
        return $this->PostalCode;
    }
    function setPostalCode($PostalCode, $charset = 'iso-8859-1')
    {
        $this->PostalCode = $PostalCode;
        $this->_elements['PostalCode']['charset'] = $charset;
    }
    function getAddressID()
    {
        return $this->AddressID;
    }
    function setAddressID($AddressID, $charset = 'iso-8859-1')
    {
        $this->AddressID = $AddressID;
        $this->_elements['AddressID']['charset'] = $charset;
    }
    function getAddressOwner()
    {
        return $this->AddressOwner;
    }
    function setAddressOwner($AddressOwner, $charset = 'iso-8859-1')
    {
        $this->AddressOwner = $AddressOwner;
        $this->_elements['AddressOwner']['charset'] = $charset;
    }
    function getExternalAddressID()
    {
        return $this->ExternalAddressID;
    }
    function setExternalAddressID($ExternalAddressID, $charset = 'iso-8859-1')
    {
        $this->ExternalAddressID = $ExternalAddressID;
        $this->_elements['ExternalAddressID']['charset'] = $charset;
    }
    function getInternationalName()
    {
        return $this->InternationalName;
    }
    function setInternationalName($InternationalName, $charset = 'iso-8859-1')
    {
        $this->InternationalName = $InternationalName;
        $this->_elements['InternationalName']['charset'] = $charset;
    }
    function getInternationalStateAndCity()
    {
        return $this->InternationalStateAndCity;
    }
    function setInternationalStateAndCity($InternationalStateAndCity, $charset = 'iso-8859-1')
    {
        $this->InternationalStateAndCity = $InternationalStateAndCity;
        $this->_elements['InternationalStateAndCity']['charset'] = $charset;
    }
    function getInternationalStreet()
    {
        return $this->InternationalStreet;
    }
    function setInternationalStreet($InternationalStreet, $charset = 'iso-8859-1')
    {
        $this->InternationalStreet = $InternationalStreet;
        $this->_elements['InternationalStreet']['charset'] = $charset;
    }
    function getAddressStatus()
    {
        return $this->AddressStatus;
    }
    function setAddressStatus($AddressStatus, $charset = 'iso-8859-1')
    {
        $this->AddressStatus = $AddressStatus;
        $this->_elements['AddressStatus']['charset'] = $charset;
    }
}
