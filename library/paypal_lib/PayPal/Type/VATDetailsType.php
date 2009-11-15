<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * VATDetailsType
 * 
 * Contains information required To list a business item. BusinessSeller - only for
 * add item, the RestrictedToBusiness and VATPercent for both get and add, for
 * revise all must be optional
 *
 * @package PayPal
 */
class VATDetailsType extends XSDSimpleType
{
    var $BusinessSeller;

    var $RestrictedToBusiness;

    var $VATPercent;

    function VATDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'BusinessSeller' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RestrictedToBusiness' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'VATPercent' => 
              array (
                'required' => false,
                'type' => 'float',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBusinessSeller()
    {
        return $this->BusinessSeller;
    }
    function setBusinessSeller($BusinessSeller, $charset = 'iso-8859-1')
    {
        $this->BusinessSeller = $BusinessSeller;
        $this->_elements['BusinessSeller']['charset'] = $charset;
    }
    function getRestrictedToBusiness()
    {
        return $this->RestrictedToBusiness;
    }
    function setRestrictedToBusiness($RestrictedToBusiness, $charset = 'iso-8859-1')
    {
        $this->RestrictedToBusiness = $RestrictedToBusiness;
        $this->_elements['RestrictedToBusiness']['charset'] = $charset;
    }
    function getVATPercent()
    {
        return $this->VATPercent;
    }
    function setVATPercent($VATPercent, $charset = 'iso-8859-1')
    {
        $this->VATPercent = $VATPercent;
        $this->_elements['VATPercent']['charset'] = $charset;
    }
}
