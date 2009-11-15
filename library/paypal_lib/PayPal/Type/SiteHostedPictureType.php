<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SiteHostedPictureType
 *
 * @package PayPal
 */
class SiteHostedPictureType extends XSDSimpleType
{
    /**
     * URLs for item picture that are stored/hosted at eBay site.
     */
    var $PictureURL;

    /**
     * Type of display for photos used for PhotoHosting slide show. Here are display
     * options: None = No special Picture Services features. SlideShow = Slideshow of
     * multiple pictures. SuperSize = Super-size format picture. PicturePack = Picture
     * Pack. Default is 'None'.
     */
    var $PhotoDisplay;

    /**
     * This will be either "Featured" or "Gallery".
     */
    var $GalleryType;

    function SiteHostedPictureType()
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
              'PhotoDisplay' => 
              array (
                'required' => false,
                'type' => 'PhotoDisplayCodeType',
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
    function getPhotoDisplay()
    {
        return $this->PhotoDisplay;
    }
    function setPhotoDisplay($PhotoDisplay, $charset = 'iso-8859-1')
    {
        $this->PhotoDisplay = $PhotoDisplay;
        $this->_elements['PhotoDisplay']['charset'] = $charset;
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
