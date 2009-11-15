<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * PaymentType
 *
 * @package PayPal
 */
class PaymentType extends XSDSimpleType
{
    var $PaymentMeans;

    function PaymentType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PaymentMeans' => 
              array (
                'required' => true,
                'type' => 'PaymentMeansType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPaymentMeans()
    {
        return $this->PaymentMeans;
    }
    function setPaymentMeans($PaymentMeans, $charset = 'iso-8859-1')
    {
        $this->PaymentMeans = $PaymentMeans;
        $this->_elements['PaymentMeans']['charset'] = $charset;
    }
}
