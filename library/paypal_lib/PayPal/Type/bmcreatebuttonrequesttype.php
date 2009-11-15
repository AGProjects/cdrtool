<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * BMCreateButtonRequestType
 *
 * @package PayPal
 */
class BMCreateButtonRequestType extends AbstractRequestType
{
    /**
     * Type of Button to create.
     */
    var $ButtonType;

    /**
     * button code.
     */
    var $ButtonCode;

    /**
     * Button sub type.
     */
    var $ButtonSubType;

    /**
     * Button Variable information
     */
    var $ButtonVar;

    var $OptionDetails;

    /**
     * Details of each option for the button.
     */
    var $TextBox;

    /**
     * Button image to use.
     */
    var $ButtonImage;

    /**
     * Button URL for custom button image.
     */
    var $ButtonImageURL;

    /**
     * Text to use on Buy Now Button.
     */
    var $BuyNowText;

    /**
     * Text to use on Subscribe button.
     */
    var $SubscribeText;

    /**
     * Button Country.
     */
    var $ButtonCountry;

    /**
     * Button language code.
     */
    var $ButtonLanguage;

    function BMCreateButtonRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'ButtonType' => 
              array (
                'required' => false,
                'type' => 'ButtonTypeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonCode' => 
              array (
                'required' => false,
                'type' => 'ButtonCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonSubType' => 
              array (
                'required' => false,
                'type' => 'ButtonSubTypeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonVar' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'OptionDetails' => 
              array (
                'required' => false,
                'type' => 'OptionDetailsType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TextBox' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonImage' => 
              array (
                'required' => false,
                'type' => 'ButtonImageType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonImageURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'BuyNowText' => 
              array (
                'required' => false,
                'type' => 'BuyNowTextType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'SubscribeText' => 
              array (
                'required' => false,
                'type' => 'SubscribeTextType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonCountry' => 
              array (
                'required' => false,
                'type' => 'CountryCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ButtonLanguage' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getButtonType()
    {
        return $this->ButtonType;
    }
    function setButtonType($ButtonType, $charset = 'iso-8859-1')
    {
        $this->ButtonType = $ButtonType;
        $this->_elements['ButtonType']['charset'] = $charset;
    }
    function getButtonCode()
    {
        return $this->ButtonCode;
    }
    function setButtonCode($ButtonCode, $charset = 'iso-8859-1')
    {
        $this->ButtonCode = $ButtonCode;
        $this->_elements['ButtonCode']['charset'] = $charset;
    }
    function getButtonSubType()
    {
        return $this->ButtonSubType;
    }
    function setButtonSubType($ButtonSubType, $charset = 'iso-8859-1')
    {
        $this->ButtonSubType = $ButtonSubType;
        $this->_elements['ButtonSubType']['charset'] = $charset;
    }
    function getButtonVar()
    {
        return $this->ButtonVar;
    }
    function setButtonVar($ButtonVar, $charset = 'iso-8859-1')
    {
        $this->ButtonVar = $ButtonVar;
        $this->_elements['ButtonVar']['charset'] = $charset;
    }
    function getOptionDetails()
    {
        return $this->OptionDetails;
    }
    function setOptionDetails($OptionDetails, $charset = 'iso-8859-1')
    {
        $this->OptionDetails = $OptionDetails;
        $this->_elements['OptionDetails']['charset'] = $charset;
    }
    function getTextBox()
    {
        return $this->TextBox;
    }
    function setTextBox($TextBox, $charset = 'iso-8859-1')
    {
        $this->TextBox = $TextBox;
        $this->_elements['TextBox']['charset'] = $charset;
    }
    function getButtonImage()
    {
        return $this->ButtonImage;
    }
    function setButtonImage($ButtonImage, $charset = 'iso-8859-1')
    {
        $this->ButtonImage = $ButtonImage;
        $this->_elements['ButtonImage']['charset'] = $charset;
    }
    function getButtonImageURL()
    {
        return $this->ButtonImageURL;
    }
    function setButtonImageURL($ButtonImageURL, $charset = 'iso-8859-1')
    {
        $this->ButtonImageURL = $ButtonImageURL;
        $this->_elements['ButtonImageURL']['charset'] = $charset;
    }
    function getBuyNowText()
    {
        return $this->BuyNowText;
    }
    function setBuyNowText($BuyNowText, $charset = 'iso-8859-1')
    {
        $this->BuyNowText = $BuyNowText;
        $this->_elements['BuyNowText']['charset'] = $charset;
    }
    function getSubscribeText()
    {
        return $this->SubscribeText;
    }
    function setSubscribeText($SubscribeText, $charset = 'iso-8859-1')
    {
        $this->SubscribeText = $SubscribeText;
        $this->_elements['SubscribeText']['charset'] = $charset;
    }
    function getButtonCountry()
    {
        return $this->ButtonCountry;
    }
    function setButtonCountry($ButtonCountry, $charset = 'iso-8859-1')
    {
        $this->ButtonCountry = $ButtonCountry;
        $this->_elements['ButtonCountry']['charset'] = $charset;
    }
    function getButtonLanguage()
    {
        return $this->ButtonLanguage;
    }
    function setButtonLanguage($ButtonLanguage, $charset = 'iso-8859-1')
    {
        $this->ButtonLanguage = $ButtonLanguage;
        $this->_elements['ButtonLanguage']['charset'] = $charset;
    }
}
