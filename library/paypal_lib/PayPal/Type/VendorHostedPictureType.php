<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * VendorHostedPictureType
 *
 * @package PayPal
 */
class VendorHostedPictureType extends XSDSimpleType
{
    /**
     * URLs for item picture that are stored/hosted at eBay site.
     */
    var $PictureURL;

    /**
     * URL for a picture for the gallery. If the GalleryFeatured argument is true, a
     * value must be supplied for either the GalleryURL or the PictureURL argument. In
     * either case: (a) If a URL is provided for only PictureURL, it is used as the
     * Gallery thumbnail. (b) If a URL is provided for both GalleryURL and PictureURL,
     * then the picture indicated in GalleryURL is used as the thumbnail. The image
     * used for the Gallery thumbnail (specified in the GalleryURL or PictureURL
     * argument) must be in one of the graphics formats JPEG, BMP, TIF, or GIF.
     */
    var $GalleryURL;

    /**
     * This will be either "Featured" or "Gallery".
     */
    var $GalleryType;

    function VendorHostedPictureType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PictureURL' => 
              array (
                'required' => false,
                'type' => 'anyURI',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'GalleryURL' => 
              array (
                'required' => false,
                'type' => 'anyURI',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'GalleryType' => 
              array (
                'required' => false,
                'type' => 'GalleryTypeCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPictureURL()
    {
        return $this->PictureURL;
    }
    function setPictureURL($PictureURL, $charset = 'iso-8859-1')
    {
        $this->PictureURL = $PictureURL;
        $this->_elements['PictureURL']['charset'] = $charset;
    }
    function getGalleryURL()
    {
        return $this->GalleryURL;
    }
    function setGalleryURL($GalleryURL, $charset = 'iso-8859-1')
    {
        $this->GalleryURL = $GalleryURL;
        $this->_elements['GalleryURL']['charset'] = $charset;
    }
    function getGalleryType()
    {
        return $this->GalleryType;
    }
    function setGalleryType($GalleryType, $charset = 'iso-8859-1')
    {
        $this->GalleryType = $GalleryType;
        $this->_elements['GalleryType']['charset'] = $charset;
    }
}
