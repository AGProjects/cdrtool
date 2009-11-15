<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PersonNameType
 *
 * @package PayPal
 */
class PersonNameType extends XSDSimpleType
{
    var $Salutation;

    var $FirstName;

    var $MiddleName;

    var $LastName;

    var $Suffix;

    function PersonNameType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Salutation' => 
              array (
                'required' => false,
                'type' => 'SalutationType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FirstName' => 
              array (
                'required' => false,
                'type' => 'NameType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MiddleName' => 
              array (
                'required' => false,
                'type' => 'NameType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LastName' => 
              array (
                'required' => false,
                'type' => 'NameType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Suffix' => 
              array (
                'required' => false,
                'type' => 'SuffixType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getSalutation()
    {
        return $this->Salutation;
    }
    function setSalutation($Salutation, $charset = 'iso-8859-1')
    {
        $this->Salutation = $Salutation;
        $this->_elements['Salutation']['charset'] = $charset;
    }
    function getFirstName()
    {
        return $this->FirstName;
    }
    function setFirstName($FirstName, $charset = 'iso-8859-1')
    {
        $this->FirstName = $FirstName;
        $this->_elements['FirstName']['charset'] = $charset;
    }
    function getMiddleName()
    {
        return $this->MiddleName;
    }
    function setMiddleName($MiddleName, $charset = 'iso-8859-1')
    {
        $this->MiddleName = $MiddleName;
        $this->_elements['MiddleName']['charset'] = $charset;
    }
    function getLastName()
    {
        return $this->LastName;
    }
    function setLastName($LastName, $charset = 'iso-8859-1')
    {
        $this->LastName = $LastName;
        $this->_elements['LastName']['charset'] = $charset;
    }
    function getSuffix()
    {
        return $this->Suffix;
    }
    function setSuffix($Suffix, $charset = 'iso-8859-1')
    {
        $this->Suffix = $Suffix;
        $this->_elements['Suffix']['charset'] = $charset;
    }
}
