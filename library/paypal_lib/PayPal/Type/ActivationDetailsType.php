<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ActivationDetailsType
 *
 * @package PayPal
 */
class ActivationDetailsType extends XSDSimpleType
{
    var $InitialAmount;

    var $FailedInitialAmountAction;

    function ActivationDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'InitialAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FailedInitialAmountAction' => 
              array (
                'required' => false,
                'type' => 'FailedPaymentActionType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getInitialAmount()
    {
        return $this->InitialAmount;
    }
    function setInitialAmount($InitialAmount, $charset = 'iso-8859-1')
    {
        $this->InitialAmount = $InitialAmount;
        $this->_elements['InitialAmount']['charset'] = $charset;
    }
    function getFailedInitialAmountAction()
    {
        return $this->FailedInitialAmountAction;
    }
    function setFailedInitialAmountAction($FailedInitialAmountAction, $charset = 'iso-8859-1')
    {
        $this->FailedInitialAmountAction = $FailedInitialAmountAction;
        $this->_elements['FailedInitialAmountAction']['charset'] = $charset;
    }
}
