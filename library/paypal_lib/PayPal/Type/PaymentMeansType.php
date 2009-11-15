<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PaymentMeansType
 *
 * @package PayPal
 */
class PaymentMeansType extends XSDSimpleType
{
    var $TypeCodeID;

    function PaymentMeansType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'TypeCodeID' => 
              array (
                'required' => true,
                'type' => 'SellerPaymentMethodCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getTypeCodeID()
    {
        return $this->TypeCodeID;
    }
    function setTypeCodeID($TypeCodeID, $charset = 'iso-8859-1')
    {
        $this->TypeCodeID = $TypeCodeID;
        $this->_elements['TypeCodeID']['charset'] = $charset;
    }
}
