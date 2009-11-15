<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PaginationType
 *
 * @package PayPal
 */
class PaginationType extends XSDSimpleType
{
    var $EntriesPerPage;

    var $PageNumber;

    function PaginationType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'EntriesPerPage' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PageNumber' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getEntriesPerPage()
    {
        return $this->EntriesPerPage;
    }
    function setEntriesPerPage($EntriesPerPage, $charset = 'iso-8859-1')
    {
        $this->EntriesPerPage = $EntriesPerPage;
        $this->_elements['EntriesPerPage']['charset'] = $charset;
    }
    function getPageNumber()
    {
        return $this->PageNumber;
    }
    function setPageNumber($PageNumber, $charset = 'iso-8859-1')
    {
        $this->PageNumber = $PageNumber;
        $this->_elements['PageNumber']['charset'] = $charset;
    }
}
