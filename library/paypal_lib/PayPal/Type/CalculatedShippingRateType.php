<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CalculatedShippingRateType
 *
 * @package PayPal
 */
class CalculatedShippingRateType extends XSDSimpleType
{
    /**
     * Potal/zip code from where package will be shipped.
     */
    var $OriginatingPostalCode;

    /**
     * Indicates an item that cannot go through the stamping machine at the shipping
     * service office (a value of True) and requires special or fragile handling. Only
     * returned if ShippingType = 2.
     */
    var $ShippingIrregular;

    /**
     * contains information about shipping fees per each shipping service chosen by the
     * seller
     */
    var $CarrierDetails;

    /**
     * May need to be moved into details - wait for George! The size of the package to
     * be shipped. Possible values are: None Letter Large envelope USPS flat rate
     * envelope Package/thick envelope USPS large package/oversize 1 Very large
     * package/oversize 2 UPS Letter
     */
    var $ShippingPackage;

    /**
     * Shipping weight unit of measure (major). If unit of weight is kilogram (i.e.,
     * metric system) this would be the exact weight value in kilogram (i.e., complete
     * decimal number, e.g., 2.23 kg). Only returned if ShippingType is 2.
     */
    var $WeightMajor;

    /**
     * Shipping weight unit of measure (minor). If unit of weight is in pounds and/or
     * ounces, this would be the exact weight value in ounces (i.e., complete decimal
     * number, e.g., 8.2 or 8.0 ounces). Only returned if ShippingType is 2.
     */
    var $WeightMinor;

    function CalculatedShippingRateType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'OriginatingPostalCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingIrregular' => 
              array (
                'required' => false,
                'type' => 'boolean',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CarrierDetails' => 
              array (
                'required' => false,
                'type' => 'ShippingCarrierDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingPackage' => 
              array (
                'required' => false,
                'type' => 'ShippingPackageCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'WeightMajor' => 
              array (
                'required' => false,
                'type' => 'MeasureType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'WeightMinor' => 
              array (
                'required' => false,
                'type' => 'MeasureType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getOriginatingPostalCode()
    {
        return $this->OriginatingPostalCode;
    }
    function setOriginatingPostalCode($OriginatingPostalCode, $charset = 'iso-8859-1')
    {
        $this->OriginatingPostalCode = $OriginatingPostalCode;
        $this->_elements['OriginatingPostalCode']['charset'] = $charset;
    }
    function getShippingIrregular()
    {
        return $this->ShippingIrregular;
    }
    function setShippingIrregular($ShippingIrregular, $charset = 'iso-8859-1')
    {
        $this->ShippingIrregular = $ShippingIrregular;
        $this->_elements['ShippingIrregular']['charset'] = $charset;
    }
    function getCarrierDetails()
    {
        return $this->CarrierDetails;
    }
    function setCarrierDetails($CarrierDetails, $charset = 'iso-8859-1')
    {
        $this->CarrierDetails = $CarrierDetails;
        $this->_elements['CarrierDetails']['charset'] = $charset;
    }
    function getShippingPackage()
    {
        return $this->ShippingPackage;
    }
    function setShippingPackage($ShippingPackage, $charset = 'iso-8859-1')
    {
        $this->ShippingPackage = $ShippingPackage;
        $this->_elements['ShippingPackage']['charset'] = $charset;
    }
    function getWeightMajor()
    {
        return $this->WeightMajor;
    }
    function setWeightMajor($WeightMajor, $charset = 'iso-8859-1')
    {
        $this->WeightMajor = $WeightMajor;
        $this->_elements['WeightMajor']['charset'] = $charset;
    }
    function getWeightMinor()
    {
        return $this->WeightMinor;
    }
    function setWeightMinor($WeightMinor, $charset = 'iso-8859-1')
    {
        $this->WeightMinor = $WeightMinor;
        $this->_elements['WeightMinor']['charset'] = $charset;
    }
}
