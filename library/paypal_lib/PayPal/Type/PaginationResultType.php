<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PaginationResultType
 *
 * @package PayPal
 */
class PaginationResultType extends XSDSimpleType
{
    var $TotalNumberOfPages;

    var $TotalNumberOfEntries;

    function PaginationResultType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'TotalNumberOfPages' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TotalNumberOfEntries' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getTotalNumberOfPages()
    {
        return $this->TotalNumberOfPages;
    }
    function setTotalNumberOfPages($TotalNumberOfPages, $charset = 'iso-8859-1')
    {
        $this->TotalNumberOfPages = $TotalNumberOfPages;
        $this->_elements['TotalNumberOfPages']['charset'] = $charset;
    }
    function getTotalNumberOfEntries()
    {
        return $this->TotalNumberOfEntries;
    }
    function setTotalNumberOfEntries($TotalNumberOfEntries, $charset = 'iso-8859-1')
    {
        $this->TotalNumberOfEntries = $TotalNumberOfEntries;
        $this->_elements['TotalNumberOfEntries']['charset'] = $charset;
    }
}
