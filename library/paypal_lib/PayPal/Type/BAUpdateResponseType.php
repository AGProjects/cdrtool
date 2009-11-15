<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * BAUpdateResponseType
 *
 * @package PayPal
 */
class BAUpdateResponseType extends AbstractResponseType
{
    var $BAUpdateResponseDetails;

    function BAUpdateResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'BAUpdateResponseDetails' => 
              array (
                'required' => true,
                'type' => 'BAUpdateResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBAUpdateResponseDetails()
    {
        return $this->BAUpdateResponseDetails;
    }
    function setBAUpdateResponseDetails($BAUpdateResponseDetails, $charset = 'iso-8859-1')
    {
        $this->BAUpdateResponseDetails = $BAUpdateResponseDetails;
        $this->_elements['BAUpdateResponseDetails']['charset'] = $charset;
    }
}
