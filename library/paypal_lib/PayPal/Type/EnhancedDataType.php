<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * EnhancedDataType
 * 
 * Enhanced Data Information. Example: AID for Airlines
 *
 * @package PayPal
 */
class EnhancedDataType extends XSDSimpleType
{
    var $AirlineItinerary;

    function EnhancedDataType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AirlineItinerary' => 
              array (
                'required' => false,
                'type' => 'AirlineItineraryType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAirlineItinerary()
    {
        return $this->AirlineItinerary;
    }
    function setAirlineItinerary($AirlineItinerary, $charset = 'iso-8859-1')
    {
        $this->AirlineItinerary = $AirlineItinerary;
        $this->_elements['AirlineItinerary']['charset'] = $charset;
    }
}
