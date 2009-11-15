<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ModifiedFieldType
 *
 * @package PayPal
 */
class ModifiedFieldType extends XSDSimpleType
{
    var $Field;

    var $ModifyType;

    function ModifiedFieldType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Field' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ModifyType' => 
              array (
                'required' => false,
                'type' => 'ModifyCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getField()
    {
        return $this->Field;
    }
    function setField($Field, $charset = 'iso-8859-1')
    {
        $this->Field = $Field;
        $this->_elements['Field']['charset'] = $charset;
    }
    function getModifyType()
    {
        return $this->ModifyType;
    }
    function setModifyType($ModifyType, $charset = 'iso-8859-1')
    {
        $this->ModifyType = $ModifyType;
        $this->_elements['ModifyType']['charset'] = $charset;
    }
}
