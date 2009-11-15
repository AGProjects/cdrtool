<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PayerInfoType
 * 
 * PayerInfoType Payer information
 *
 * @package PayPal
 */
class PayerInfoType extends XSDSimpleType
{
    /**
     * Email address of payer
     */
    var $Payer;

    /**
     * Unique customer ID
     */
    var $PayerID;

    /**
     * Status of payer's email address
     */
    var $PayerStatus;

    /**
     * Name of payer
     */
    var $PayerName;

    /**
     * Payment sender's country of residence using standard two-character ISO 3166
     * country codes. Character length and limitations: Two single-byte characters
     */
    var $PayerCountry;

    /**
     * Payer's business name.
     */
    var $PayerBusiness;

    /**
     * Payer's business address
     */
    var $Address;

    /**
     * Business contact telephone number
     */
    var $ContactPhone;

    function PayerInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Payer' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerID' => 
              array (
                'required' => false,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerStatus' => 
              array (
                'required' => false,
                'type' => 'PayPalUserStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerName' => 
              array (
                'required' => true,
                'type' => 'PersonNameType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerCountry' => 
              array (
                'required' => false,
                'type' => 'CountryCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerBusiness' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Address' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ContactPhone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPayer()
    {
        return $this->Payer;
    }
    function setPayer($Payer, $charset = 'iso-8859-1')
    {
        $this->Payer = $Payer;
        $this->_elements['Payer']['charset'] = $charset;
    }
    function getPayerID()
    {
        return $this->PayerID;
    }
    function setPayerID($PayerID, $charset = 'iso-8859-1')
    {
        $this->PayerID = $PayerID;
        $this->_elements['PayerID']['charset'] = $charset;
    }
    function getPayerStatus()
    {
        return $this->PayerStatus;
    }
    function setPayerStatus($PayerStatus, $charset = 'iso-8859-1')
    {
        $this->PayerStatus = $PayerStatus;
        $this->_elements['PayerStatus']['charset'] = $charset;
    }
    function getPayerName()
    {
        return $this->PayerName;
    }
    function setPayerName($PayerName, $charset = 'iso-8859-1')
    {
        $this->PayerName = $PayerName;
        $this->_elements['PayerName']['charset'] = $charset;
    }
    function getPayerCountry()
    {
        return $this->PayerCountry;
    }
    function setPayerCountry($PayerCountry, $charset = 'iso-8859-1')
    {
        $this->PayerCountry = $PayerCountry;
        $this->_elements['PayerCountry']['charset'] = $charset;
    }
    function getPayerBusiness()
    {
        return $this->PayerBusiness;
    }
    function setPayerBusiness($PayerBusiness, $charset = 'iso-8859-1')
    {
        $this->PayerBusiness = $PayerBusiness;
        $this->_elements['PayerBusiness']['charset'] = $charset;
    }
    function getAddress()
    {
        return $this->Address;
    }
    function setAddress($Address, $charset = 'iso-8859-1')
    {
        $this->Address = $Address;
        $this->_elements['Address']['charset'] = $charset;
    }
    function getContactPhone()
    {
        return $this->ContactPhone;
    }
    function setContactPhone($ContactPhone, $charset = 'iso-8859-1')
    {
        $this->ContactPhone = $ContactPhone;
        $this->_elements['ContactPhone']['charset'] = $charset;
    }
}
