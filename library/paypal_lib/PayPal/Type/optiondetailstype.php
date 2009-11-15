<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * OptionDetailsType
 *
 * @package PayPal
 */
class OptionDetailsType extends XSDSimpleType
{
    /**
     * Option Name.
     */
    var $OptionName;

    var $OptionSelectionDetails;

    function OptionDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'OptionName' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'OptionSelectionDetails' => 
              array (
                'required' => false,
                'type' => 'OptionSelectionDetailsType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getOptionName()
    {
        return $this->OptionName;
    }
    function setOptionName($OptionName, $charset = 'iso-8859-1')
    {
        $this->OptionName = $OptionName;
        $this->_elements['OptionName']['charset'] = $charset;
    }
    function getOptionSelectionDetails()
    {
        return $this->OptionSelectionDetails;
    }
    function setOptionSelectionDetails($OptionSelectionDetails, $charset = 'iso-8859-1')
    {
        $this->OptionSelectionDetails = $OptionSelectionDetails;
        $this->_elements['OptionSelectionDetails']['charset'] = $charset;
    }
}
