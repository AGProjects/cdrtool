<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * FeesType
 * 
 * Following are the current set of eBay fee types AuctionLengthFee BoldFee
 * BuyItNowFee CategoryFeaturedFee FeaturedFee FeaturedGalleryFee
 * FixedPriceDurationFee GalleryFee GiftIconFee HighLightFee InsertionFee
 * ListingDesignerFee ListingFee PhotoDisplayFee PhotoFee ReserveFee SchedulingFee
 * ThirtyDaysAucFee Instances of this type could hold one or more supported types
 * of fee.
 *
 * @package PayPal
 */
class FeesType extends XSDSimpleType
{
    var $Fee;

    function FeesType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Fee' => 
              array (
                'required' => true,
                'type' => 'FeeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getFee()
    {
        return $this->Fee;
    }
    function setFee($Fee, $charset = 'iso-8859-1')
    {
        $this->Fee = $Fee;
        $this->_elements['Fee']['charset'] = $charset;
    }
}
