<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ErrorType
 *
 * @package PayPal
 */
class ErrorType extends XSDSimpleType
{
    var $ShortMessage;

    var $LongMessage;

    /**
     * Error code can be used by a receiving application to debugging a response
     * message. These codes will need to be uniquely defined for each application.
     */
    var $ErrorCode;

    /**
     * SeverityCode indicates whether the error is an application level error or if it
     * is informational error, i.e., warning.
     */
    var $SeverityCode;

    /**
     * This optional element may carry additional application-specific error variables
     * that indicate specific information about the error condition particularly in the
     * cases where there are multiple instances of the ErrorType which require
     * additional context.
     */
    var $ErrorParameters;

    function ErrorType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ShortMessage' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'LongMessage' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ErrorCode' => 
              array (
                'required' => true,
                'type' => 'token',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SeverityCode' => 
              array (
                'required' => true,
                'type' => 'SeverityCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ErrorParameters' => 
              array (
                'required' => false,
                'type' => 'ErrorParameterType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getShortMessage()
    {
        return $this->ShortMessage;
    }
    function setShortMessage($ShortMessage, $charset = 'iso-8859-1')
    {
        $this->ShortMessage = $ShortMessage;
        $this->_elements['ShortMessage']['charset'] = $charset;
    }
    function getLongMessage()
    {
        return $this->LongMessage;
    }
    function setLongMessage($LongMessage, $charset = 'iso-8859-1')
    {
        $this->LongMessage = $LongMessage;
        $this->_elements['LongMessage']['charset'] = $charset;
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
    function getSeverityCode()
    {
        return $this->SeverityCode;
    }
    function setSeverityCode($SeverityCode, $charset = 'iso-8859-1')
    {
        $this->SeverityCode = $SeverityCode;
        $this->_elements['SeverityCode']['charset'] = $charset;
    }
    function getErrorParameters()
    {
        return $this->ErrorParameters;
    }
    function setErrorParameters($ErrorParameters, $charset = 'iso-8859-1')
    {
        $this->ErrorParameters = $ErrorParameters;
        $this->_elements['ErrorParameters']['charset'] = $charset;
    }
}
