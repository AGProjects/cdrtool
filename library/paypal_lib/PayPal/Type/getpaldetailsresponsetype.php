<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * GetPalDetailsResponseType
 *
 * @package PayPal
 */
class GetPalDetailsResponseType extends AbstractResponseType
{
    var $Pal;

    var $Locale;

    function GetPalDetailsResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'Pal' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Locale' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getPal()
    {
        return $this->Pal;
    }
    function setPal($Pal, $charset = 'iso-8859-1')
    {
        $this->Pal = $Pal;
        $this->_elements['Pal']['charset'] = $charset;
    }
    function getLocale()
    {
        return $this->Locale;
    }
    function setLocale($Locale, $charset = 'iso-8859-1')
    {
        $this->Locale = $Locale;
        $this->_elements['Locale']['charset'] = $charset;
    }
}
