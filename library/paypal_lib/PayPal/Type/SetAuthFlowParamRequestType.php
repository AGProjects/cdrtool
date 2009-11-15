<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * SetAuthFlowParamRequestType
 *
 * @package PayPal
 */
class SetAuthFlowParamRequestType extends AbstractRequestType
{
    var $SetAuthFlowParamRequestDetails;

    function SetAuthFlowParamRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'SetAuthFlowParamRequestDetails' => 
              array (
                'required' => true,
                'type' => 'SetAuthFlowParamRequestDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getSetAuthFlowParamRequestDetails()
    {
        return $this->SetAuthFlowParamRequestDetails;
    }
    function setSetAuthFlowParamRequestDetails($SetAuthFlowParamRequestDetails, $charset = 'iso-8859-1')
    {
        $this->SetAuthFlowParamRequestDetails = $SetAuthFlowParamRequestDetails;
        $this->_elements['SetAuthFlowParamRequestDetails']['charset'] = $charset;
    }
}
