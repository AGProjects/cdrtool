<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * RiskFilterDetailsType
 * 
 * Details of Risk Filter.
 *
 * @package PayPal
 */
class RiskFilterDetailsType extends XSDSimpleType
{
    var $Id;

    var $Name;

    var $Description;

    function RiskFilterDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Id' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Name' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Description' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getId()
    {
        return $this->Id;
    }
    function setId($Id, $charset = 'iso-8859-1')
    {
        $this->Id = $Id;
        $this->_elements['Id']['charset'] = $charset;
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
    function getDescription()
    {
        return $this->Description;
    }
    function setDescription($Description, $charset = 'iso-8859-1')
    {
        $this->Description = $Description;
        $this->_elements['Description']['charset'] = $charset;
    }
}
