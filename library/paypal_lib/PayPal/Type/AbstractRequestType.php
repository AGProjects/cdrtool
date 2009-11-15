<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AbstractRequestType
 * 
 * Base type definition of request payload that can carry any type of payload
 * content with optional versioning information and detail level requirements.
 *
 * @package PayPal
 */
class AbstractRequestType extends XSDSimpleType
{
    /**
     * This specifies the required detail level that is needed by a client application
     * pertaining to a particular data component (e.g., Item, Transaction, etc.). The
     * detail level is specified in the DetailLevelCodeType which has all the
     * enumerated values of the detail level for each component.
     */
    var $DetailLevel;

    /**
     * This should be the standard RFC 3066 language identification tag, e.g., en_US.
     */
    var $ErrorLanguage;

    /**
     * This refers to the version of the request payload schema.
     */
    var $Version;

    function AbstractRequestType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'DetailLevel' => 
              array (
                'required' => false,
                'type' => 'DetailLevelCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ErrorLanguage' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Version' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getDetailLevel()
    {
        return $this->DetailLevel;
    }
    function setDetailLevel($DetailLevel, $charset = 'iso-8859-1')
    {
        $this->DetailLevel = $DetailLevel;
        $this->_elements['DetailLevel']['charset'] = $charset;
    }
    function getErrorLanguage()
    {
        return $this->ErrorLanguage;
    }
    function setErrorLanguage($ErrorLanguage, $charset = 'iso-8859-1')
    {
        $this->ErrorLanguage = $ErrorLanguage;
        $this->_elements['ErrorLanguage']['charset'] = $charset;
    }
    function getVersion()
    {
        return $this->Version;
    }
    function setVersion($Version, $charset = 'iso-8859-1')
    {
        $this->Version = $Version;
        $this->_elements['Version']['charset'] = $charset;
    }
}
