<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * FaultDetailsType
 *
 * @package PayPal
 */
class FaultDetailsType extends XSDSimpleType
{
    /**
     * Error code can be used by a receiving application to debugging a SOAP response
     * message that contain one or more SOAP Fault detail objects, i.e., fault detail
     * sub-elements. These codes will need to be uniquely defined for each fault
     * scenario.
     */
    var $ErrorCode;

    /**
     * Severity indicates whether the error is a serious fault or if it is
     * informational error, i.e., warning.
     */
    var $Severity;

    var $DetailedMessage;

    function FaultDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ErrorCode' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Severity' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DetailedMessage' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getErrorCode()
    {
        return $this->ErrorCode;
    }
    function setErrorCode($ErrorCode, $charset = 'iso-8859-1')
    {
        $this->ErrorCode = $ErrorCode;
        $this->_elements['ErrorCode']['charset'] = $charset;
    }
    function getSeverity()
    {
        return $this->Severity;
    }
    function setSeverity($Severity, $charset = 'iso-8859-1')
    {
        $this->Severity = $Severity;
        $this->_elements['Severity']['charset'] = $charset;
    }
    function getDetailedMessage()
    {
        return $this->DetailedMessage;
    }
    function setDetailedMessage($DetailedMessage, $charset = 'iso-8859-1')
    {
        $this->DetailedMessage = $DetailedMessage;
        $this->_elements['DetailedMessage']['charset'] = $charset;
    }
}
