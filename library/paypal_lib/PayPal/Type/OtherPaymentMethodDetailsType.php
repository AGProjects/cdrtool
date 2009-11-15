<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * OtherPaymentMethodDetailsType
 * 
 * Lists the Payment Methods (other than PayPal) that the use can pay with e.g.
 * Money Order.
 *
 * @package PayPal
 */
class OtherPaymentMethodDetailsType extends XSDSimpleType
{
    /**
     * The identifier of the Payment Method.
     */
    var $OtherPaymentMethodId;

    /**
     * Valid values are 'Method', 'SubMethod'.
     */
    var $OtherPaymentMethodType;

    /**
     * The name of the Payment Method.
     */
    var $OtherPaymentMethodLabel;

    /**
     * The short description of the Payment Method, goes along with the label.
     */
    var $OtherPaymentMethodLabelDescription;

    /**
     * The title for the long description.
     */
    var $OtherPaymentMethodLongDescriptionTitle;

    /**
     * The long description of the Payment Method.
     */
    var $OtherPaymentMethodLongDescription;

    /**
     * The icon of the Payment Method.
     */
    var $OtherPaymentMethodIcon;

    /**
     * If this flag is true, then OtherPaymentMethodIcon is required to have a valid
     * value; the label will be hidden and only ICON will be shown.
     */
    var $OtherPaymentMethodHideLabel;

    function OtherPaymentMethodDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'OtherPaymentMethodId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OtherPaymentMethodType' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OtherPaymentMethodLabel' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OtherPaymentMethodLabelDescription' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OtherPaymentMethodLongDescriptionTitle' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OtherPaymentMethodLongDescription' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OtherPaymentMethodIcon' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OtherPaymentMethodHideLabel' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getOtherPaymentMethodId()
    {
        return $this->OtherPaymentMethodId;
    }
    function setOtherPaymentMethodId($OtherPaymentMethodId, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethodId = $OtherPaymentMethodId;
        $this->_elements['OtherPaymentMethodId']['charset'] = $charset;
    }
    function getOtherPaymentMethodType()
    {
        return $this->OtherPaymentMethodType;
    }
    function setOtherPaymentMethodType($OtherPaymentMethodType, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethodType = $OtherPaymentMethodType;
        $this->_elements['OtherPaymentMethodType']['charset'] = $charset;
    }
    function getOtherPaymentMethodLabel()
    {
        return $this->OtherPaymentMethodLabel;
    }
    function setOtherPaymentMethodLabel($OtherPaymentMethodLabel, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethodLabel = $OtherPaymentMethodLabel;
        $this->_elements['OtherPaymentMethodLabel']['charset'] = $charset;
    }
    function getOtherPaymentMethodLabelDescription()
    {
        return $this->OtherPaymentMethodLabelDescription;
    }
    function setOtherPaymentMethodLabelDescription($OtherPaymentMethodLabelDescription, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethodLabelDescription = $OtherPaymentMethodLabelDescription;
        $this->_elements['OtherPaymentMethodLabelDescription']['charset'] = $charset;
    }
    function getOtherPaymentMethodLongDescriptionTitle()
    {
        return $this->OtherPaymentMethodLongDescriptionTitle;
    }
    function setOtherPaymentMethodLongDescriptionTitle($OtherPaymentMethodLongDescriptionTitle, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethodLongDescriptionTitle = $OtherPaymentMethodLongDescriptionTitle;
        $this->_elements['OtherPaymentMethodLongDescriptionTitle']['charset'] = $charset;
    }
    function getOtherPaymentMethodLongDescription()
    {
        return $this->OtherPaymentMethodLongDescription;
    }
    function setOtherPaymentMethodLongDescription($OtherPaymentMethodLongDescription, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethodLongDescription = $OtherPaymentMethodLongDescription;
        $this->_elements['OtherPaymentMethodLongDescription']['charset'] = $charset;
    }
    function getOtherPaymentMethodIcon()
    {
        return $this->OtherPaymentMethodIcon;
    }
    function setOtherPaymentMethodIcon($OtherPaymentMethodIcon, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethodIcon = $OtherPaymentMethodIcon;
        $this->_elements['OtherPaymentMethodIcon']['charset'] = $charset;
    }
    function getOtherPaymentMethodHideLabel()
    {
        return $this->OtherPaymentMethodHideLabel;
    }
    function setOtherPaymentMethodHideLabel($OtherPaymentMethodHideLabel, $charset = 'iso-8859-1')
    {
        $this->OtherPaymentMethodHideLabel = $OtherPaymentMethodHideLabel;
        $this->_elements['OtherPaymentMethodHideLabel']['charset'] = $charset;
    }
}
