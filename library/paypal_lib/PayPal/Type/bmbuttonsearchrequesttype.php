<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * BMButtonSearchRequestType
 *
 * @package PayPal
 */
class BMButtonSearchRequestType extends AbstractRequestType
{
    /**
     * The earliest transaction date at which to start the search. No wildcards are
     * allowed.
     */
    var $StartDate;

    /**
     * The latest transaction date to be included in the search
     */
    var $EndDate;

    function BMButtonSearchRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'StartDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'EndDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getStartDate()
    {
        return $this->StartDate;
    }
    function setStartDate($StartDate, $charset = 'iso-8859-1')
    {
        $this->StartDate = $StartDate;
        $this->_elements['StartDate']['charset'] = $charset;
    }
    function getEndDate()
    {
        return $this->EndDate;
    }
    function setEndDate($EndDate, $charset = 'iso-8859-1')
    {
        $this->EndDate = $EndDate;
        $this->_elements['EndDate']['charset'] = $charset;
    }
}
