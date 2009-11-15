<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * BMManageButtonStatusResponseType
 *
 * @package PayPal
 */
class BMManageButtonStatusResponseType extends AbstractResponseType
{
    function BMManageButtonStatusResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
    }

}
