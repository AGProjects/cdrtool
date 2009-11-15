<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * GetPalDetailsRequestType
 *
 * @package PayPal
 */
class GetPalDetailsRequestType extends AbstractRequestType
{
    function GetPalDetailsRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
    }

}
