<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * ListingDesignerType
 * 
 * Identifies the Layout and the Theme template associated with the item. in case
 * of revision - all data can be min occur = 0
 *
 * @package PayPal
 */
class ListingDesignerType extends XSDSimpleType
{
    /**
     * Identifies the Layout template associated with the item.
     */
    var $LayoutID;

    /**
     * A value of true for OptimalPictureSize indicates that the picture URL will be
     * enlarged to fit description of the item.
     */
    var $OptimalPictureSize;

    /**
     * Identifies the Theme template associated with the item.
     */
    var $ThemeID;

    function ListingDesignerType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'LayoutID' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OptimalPictureSize' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ThemeID' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getLayoutID()
    {
        return $this->LayoutID;
    }
    function setLayoutID($LayoutID, $charset = 'iso-8859-1')
    {
        $this->LayoutID = $LayoutID;
        $this->_elements['LayoutID']['charset'] = $charset;
    }
    function getOptimalPictureSize()
    {
        return $this->OptimalPictureSize;
    }
    function setOptimalPictureSize($OptimalPictureSize, $charset = 'iso-8859-1')
    {
        $this->OptimalPictureSize = $OptimalPictureSize;
        $this->_elements['OptimalPictureSize']['charset'] = $charset;
    }
    function getThemeID()
    {
        return $this->ThemeID;
    }
    function setThemeID($ThemeID, $charset = 'iso-8859-1')
    {
        $this->ThemeID = $ThemeID;
        $this->_elements['ThemeID']['charset'] = $charset;
    }
}
