<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * GetBoardingDetailsResponseType
 *
 * @package PayPal
 */
class GetBoardingDetailsResponseType extends AbstractResponseType
{
    var $GetBoardingDetailsResponseDetails;

    function GetBoardingDetailsResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'GetBoardingDetailsResponseDetails' => 
              array (
                'required' => true,
                'type' => 'GetBoardingDetailsResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getGetBoardingDetailsResponseDetails()
    {
        return $this->GetBoardingDetailsResponseDetails;
    }
    function setGetBoardingDetailsResponseDetails($GetBoardingDetailsResponseDetails, $charset = 'iso-8859-1')
    {
        $this->GetBoardingDetailsResponseDetails = $GetBoardingDetailsResponseDetails;
        $this->_elements['GetBoardingDetailsResponseDetails']['charset'] = $charset;
    }
}
