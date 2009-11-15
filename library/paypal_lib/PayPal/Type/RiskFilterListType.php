<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * RiskFilterListType
 * 
 * Details of Risk Filter.
 *
 * @package PayPal
 */
class RiskFilterListType extends XSDSimpleType
{
    var $Filters;

    function RiskFilterListType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Filters' => 
              array (
                'required' => true,
                'type' => 'RiskFilterDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getFilters()
    {
        return $this->Filters;
    }
    function setFilters($Filters, $charset = 'iso-8859-1')
    {
        $this->Filters = $Filters;
        $this->_elements['Filters']['charset'] = $charset;
    }
}
