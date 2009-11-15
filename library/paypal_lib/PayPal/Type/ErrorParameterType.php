<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ErrorParameterType
 *
 * @package PayPal
 */
class ErrorParameterType extends XSDSimpleType
{
    /**
     * Value of the application-specific error parameter. Specifies
     * application-specific error parameter name.
     */
    var $Value;

    function ErrorParameterType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Value' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
        $this->_attributes = array_merge($this->_attributes,
            array (
              'ParamID' => 
              array (
                'name' => 'ParamID',
                'type' => 'xs:string',
              ),
            ));
    }

    function getValue()
    {
        return $this->Value;
    }
    function setValue($Value, $charset = 'iso-8859-1')
    {
        $this->Value = $Value;
        $this->_elements['Value']['charset'] = $charset;
    }
}
