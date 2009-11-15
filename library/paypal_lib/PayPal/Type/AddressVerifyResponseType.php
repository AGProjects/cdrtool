<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * AddressVerifyResponseType
 *
 * @package PayPal
 */
class AddressVerifyResponseType extends AbstractResponseType
{
    /**
     * Confirmation of a match, with one of the following tokens:
     */
    var $ConfirmationCode;

    /**
     * PayPal has compared the postal address you want to verify with the postal
     * address on file at PayPal.
     */
    var $StreetMatch;

    /**
     * PayPal has compared the zip code you want to verify with the zip code on file
     * for the email address.
     */
    var $ZipMatch;

    /**
     * Two-character country code (ISO 3166) on file for the PayPal email address.
     */
    var $CountryCode;

    /**
     * The token prevents a buyer from using any street address other than the address
     * on file at PayPal during additional purchases he might make from the merchant.
     * It contains encrypted information about the user ’s street address and email
     * address. You can pass the value of the token with the Buy Now button HTML
     * address_api_token variable so that PayPal prevents the buyer from using any
     * street address or email address other than those verified by PayPal. The token
     * is valid for 24 hours.
     */
    var $PayPalToken;

    function AddressVerifyResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'ConfirmationCode' => 
              array (
                'required' => true,
                'type' => 'AddressStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'StreetMatch' => 
              array (
                'required' => true,
                'type' => 'MatchStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ZipMatch' => 
              array (
                'required' => false,
                'type' => 'MatchStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'CountryCode' => 
              array (
                'required' => false,
                'type' => 'CountryCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'PayPalToken' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getConfirmationCode()
    {
        return $this->ConfirmationCode;
    }
    function setConfirmationCode($ConfirmationCode, $charset = 'iso-8859-1')
    {
        $this->ConfirmationCode = $ConfirmationCode;
        $this->_elements['ConfirmationCode']['charset'] = $charset;
    }
    function getStreetMatch()
    {
        return $this->StreetMatch;
    }
    function setStreetMatch($StreetMatch, $charset = 'iso-8859-1')
    {
        $this->StreetMatch = $StreetMatch;
        $this->_elements['StreetMatch']['charset'] = $charset;
    }
    function getZipMatch()
    {
        return $this->ZipMatch;
    }
    function setZipMatch($ZipMatch, $charset = 'iso-8859-1')
    {
        $this->ZipMatch = $ZipMatch;
        $this->_elements['ZipMatch']['charset'] = $charset;
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
    function getPayPalToken()
    {
        return $this->PayPalToken;
    }
    function setPayPalToken($PayPalToken, $charset = 'iso-8859-1')
    {
        $this->PayPalToken = $PayPalToken;
        $this->_elements['PayPalToken']['charset'] = $charset;
    }
}
