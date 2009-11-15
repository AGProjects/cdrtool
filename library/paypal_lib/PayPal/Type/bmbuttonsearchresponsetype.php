<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * BMButtonSearchResponseType
 *
 * @package PayPal
 */
class BMButtonSearchResponseType extends AbstractResponseType
{
    var $ButtonSearchResult;

    function BMButtonSearchResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'ButtonSearchResult' => 
              array (
                'required' => false,
                'type' => 'ButtonSearchResultType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getButtonSearchResult()
    {
        return $this->ButtonSearchResult;
    }
    function setButtonSearchResult($ButtonSearchResult, $charset = 'iso-8859-1')
    {
        $this->ButtonSearchResult = $ButtonSearchResult;
        $this->_elements['ButtonSearchResult']['charset'] = $charset;
    }
}
