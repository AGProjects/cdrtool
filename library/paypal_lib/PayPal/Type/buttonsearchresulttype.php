<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ButtonSearchResultType
 *
 * @package PayPal
 */
class ButtonSearchResultType extends XSDSimpleType
{
    var $HostedButtonID;

    var $ButtonType;

    var $ItemName;

    var $ModifyDate;

    function ButtonSearchResultType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'HostedButtonID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ButtonType' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ModifyDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getHostedButtonID()
    {
        return $this->HostedButtonID;
    }
    function setHostedButtonID($HostedButtonID, $charset = 'iso-8859-1')
    {
        $this->HostedButtonID = $HostedButtonID;
        $this->_elements['HostedButtonID']['charset'] = $charset;
    }
    function getButtonType()
    {
        return $this->ButtonType;
    }
    function setButtonType($ButtonType, $charset = 'iso-8859-1')
    {
        $this->ButtonType = $ButtonType;
        $this->_elements['ButtonType']['charset'] = $charset;
    }
    function getItemName()
    {
        return $this->ItemName;
    }
    function setItemName($ItemName, $charset = 'iso-8859-1')
    {
        $this->ItemName = $ItemName;
        $this->_elements['ItemName']['charset'] = $charset;
    }
    function getModifyDate()
    {
        return $this->ModifyDate;
    }
    function setModifyDate($ModifyDate, $charset = 'iso-8859-1')
    {
        $this->ModifyDate = $ModifyDate;
        $this->_elements['ModifyDate']['charset'] = $charset;
    }
}
