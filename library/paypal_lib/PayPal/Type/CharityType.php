<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * CharityType
 * 
 * Contains information about a Charity listing.in case of revision - all data can
 * be min occur = 0
 *
 * @package PayPal
 */
class CharityType extends XSDSimpleType
{
    var $CharityName;

    var $CharityNumber;

    var $DonationPercent;

    function CharityType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'CharityName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CharityNumber' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DonationPercent' => 
              array (
                'required' => false,
                'type' => 'float',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCharityName()
    {
        return $this->CharityName;
    }
    function setCharityName($CharityName, $charset = 'iso-8859-1')
    {
        $this->CharityName = $CharityName;
        $this->_elements['CharityName']['charset'] = $charset;
    }
    function getCharityNumber()
    {
        return $this->CharityNumber;
    }
    function setCharityNumber($CharityNumber, $charset = 'iso-8859-1')
    {
        $this->CharityNumber = $CharityNumber;
        $this->_elements['CharityNumber']['charset'] = $charset;
    }
    function getDonationPercent()
    {
        return $this->DonationPercent;
    }
    function setDonationPercent($DonationPercent, $charset = 'iso-8859-1')
    {
        $this->DonationPercent = $DonationPercent;
        $this->_elements['DonationPercent']['charset'] = $charset;
    }
}
