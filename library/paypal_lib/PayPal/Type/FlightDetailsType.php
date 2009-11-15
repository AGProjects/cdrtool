<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * FlightDetailsType
 * 
 * Details of leg information
 *
 * @package PayPal
 */
class FlightDetailsType extends XSDSimpleType
{
    var $ConjuctionTicket;

    var $ExchangeTicket;

    var $CouponNumber;

    var $ServiceClass;

    var $TravelDate;

    var $CarrierCode;

    var $StopOverPermitted;

    var $DepartureAirport;

    var $ArrivalAirport;

    var $FlightNumber;

    var $DepartureTime;

    var $ArrivalTime;

    var $FareBasisCode;

    var $Fare;

    var $Taxes;

    var $Fee;

    var $EndorsementOrRestrictions;

    function FlightDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ConjuctionTicket' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExchangeTicket' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CouponNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ServiceClass' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TravelDate' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CarrierCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'StopOverPermitted' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DepartureAirport' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ArrivalAirport' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FlightNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DepartureTime' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ArrivalTime' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FareBasisCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Fare' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Taxes' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Fee' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EndorsementOrRestrictions' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getConjuctionTicket()
    {
        return $this->ConjuctionTicket;
    }
    function setConjuctionTicket($ConjuctionTicket, $charset = 'iso-8859-1')
    {
        $this->ConjuctionTicket = $ConjuctionTicket;
        $this->_elements['ConjuctionTicket']['charset'] = $charset;
    }
    function getExchangeTicket()
    {
        return $this->ExchangeTicket;
    }
    function setExchangeTicket($ExchangeTicket, $charset = 'iso-8859-1')
    {
        $this->ExchangeTicket = $ExchangeTicket;
        $this->_elements['ExchangeTicket']['charset'] = $charset;
    }
    function getCouponNumber()
    {
        return $this->CouponNumber;
    }
    function setCouponNumber($CouponNumber, $charset = 'iso-8859-1')
    {
        $this->CouponNumber = $CouponNumber;
        $this->_elements['CouponNumber']['charset'] = $charset;
    }
    function getServiceClass()
    {
        return $this->ServiceClass;
    }
    function setServiceClass($ServiceClass, $charset = 'iso-8859-1')
    {
        $this->ServiceClass = $ServiceClass;
        $this->_elements['ServiceClass']['charset'] = $charset;
    }
    function getTravelDate()
    {
        return $this->TravelDate;
    }
    function setTravelDate($TravelDate, $charset = 'iso-8859-1')
    {
        $this->TravelDate = $TravelDate;
        $this->_elements['TravelDate']['charset'] = $charset;
    }
    function getCarrierCode()
    {
        return $this->CarrierCode;
    }
    function setCarrierCode($CarrierCode, $charset = 'iso-8859-1')
    {
        $this->CarrierCode = $CarrierCode;
        $this->_elements['CarrierCode']['charset'] = $charset;
    }
    function getStopOverPermitted()
    {
        return $this->StopOverPermitted;
    }
    function setStopOverPermitted($StopOverPermitted, $charset = 'iso-8859-1')
    {
        $this->StopOverPermitted = $StopOverPermitted;
        $this->_elements['StopOverPermitted']['charset'] = $charset;
    }
    function getDepartureAirport()
    {
        return $this->DepartureAirport;
    }
    function setDepartureAirport($DepartureAirport, $charset = 'iso-8859-1')
    {
        $this->DepartureAirport = $DepartureAirport;
        $this->_elements['DepartureAirport']['charset'] = $charset;
    }
    function getArrivalAirport()
    {
        return $this->ArrivalAirport;
    }
    function setArrivalAirport($ArrivalAirport, $charset = 'iso-8859-1')
    {
        $this->ArrivalAirport = $ArrivalAirport;
        $this->_elements['ArrivalAirport']['charset'] = $charset;
    }
    function getFlightNumber()
    {
        return $this->FlightNumber;
    }
    function setFlightNumber($FlightNumber, $charset = 'iso-8859-1')
    {
        $this->FlightNumber = $FlightNumber;
        $this->_elements['FlightNumber']['charset'] = $charset;
    }
    function getDepartureTime()
    {
        return $this->DepartureTime;
    }
    function setDepartureTime($DepartureTime, $charset = 'iso-8859-1')
    {
        $this->DepartureTime = $DepartureTime;
        $this->_elements['DepartureTime']['charset'] = $charset;
    }
    function getArrivalTime()
    {
        return $this->ArrivalTime;
    }
    function setArrivalTime($ArrivalTime, $charset = 'iso-8859-1')
    {
        $this->ArrivalTime = $ArrivalTime;
        $this->_elements['ArrivalTime']['charset'] = $charset;
    }
    function getFareBasisCode()
    {
        return $this->FareBasisCode;
    }
    function setFareBasisCode($FareBasisCode, $charset = 'iso-8859-1')
    {
        $this->FareBasisCode = $FareBasisCode;
        $this->_elements['FareBasisCode']['charset'] = $charset;
    }
    function getFare()
    {
        return $this->Fare;
    }
    function setFare($Fare, $charset = 'iso-8859-1')
    {
        $this->Fare = $Fare;
        $this->_elements['Fare']['charset'] = $charset;
    }
    function getTaxes()
    {
        return $this->Taxes;
    }
    function setTaxes($Taxes, $charset = 'iso-8859-1')
    {
        $this->Taxes = $Taxes;
        $this->_elements['Taxes']['charset'] = $charset;
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
    function getEndorsementOrRestrictions()
    {
        return $this->EndorsementOrRestrictions;
    }
    function setEndorsementOrRestrictions($EndorsementOrRestrictions, $charset = 'iso-8859-1')
    {
        $this->EndorsementOrRestrictions = $EndorsementOrRestrictions;
        $this->_elements['EndorsementOrRestrictions']['charset'] = $charset;
    }
}
