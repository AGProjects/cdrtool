<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * AirlineItineraryType
 * 
 * AID for Airlines
 *
 * @package PayPal
 */
class AirlineItineraryType extends XSDSimpleType
{
    var $PassengerName;

    var $IssueDate;

    var $TravelAgencyName;

    var $TravelAgencyCode;

    var $TicketNumber;

    var $IssuingCarrierCode;

    var $CustomerCode;

    var $TotalFare;

    var $TotalTaxes;

    var $TotalFee;

    var $RestrictedTicket;

    var $ClearingSequence;

    var $ClearingCount;

    var $FlightDetails;

    function AirlineItineraryType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PassengerName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'IssueDate' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TravelAgencyName' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TravelAgencyCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TicketNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'IssuingCarrierCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CustomerCode' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TotalFare' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TotalTaxes' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TotalFee' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RestrictedTicket' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ClearingSequence' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ClearingCount' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FlightDetails' => 
              array (
                'required' => false,
                'type' => 'FlightDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPassengerName()
    {
        return $this->PassengerName;
    }
    function setPassengerName($PassengerName, $charset = 'iso-8859-1')
    {
        $this->PassengerName = $PassengerName;
        $this->_elements['PassengerName']['charset'] = $charset;
    }
    function getIssueDate()
    {
        return $this->IssueDate;
    }
    function setIssueDate($IssueDate, $charset = 'iso-8859-1')
    {
        $this->IssueDate = $IssueDate;
        $this->_elements['IssueDate']['charset'] = $charset;
    }
    function getTravelAgencyName()
    {
        return $this->TravelAgencyName;
    }
    function setTravelAgencyName($TravelAgencyName, $charset = 'iso-8859-1')
    {
        $this->TravelAgencyName = $TravelAgencyName;
        $this->_elements['TravelAgencyName']['charset'] = $charset;
    }
    function getTravelAgencyCode()
    {
        return $this->TravelAgencyCode;
    }
    function setTravelAgencyCode($TravelAgencyCode, $charset = 'iso-8859-1')
    {
        $this->TravelAgencyCode = $TravelAgencyCode;
        $this->_elements['TravelAgencyCode']['charset'] = $charset;
    }
    function getTicketNumber()
    {
        return $this->TicketNumber;
    }
    function setTicketNumber($TicketNumber, $charset = 'iso-8859-1')
    {
        $this->TicketNumber = $TicketNumber;
        $this->_elements['TicketNumber']['charset'] = $charset;
    }
    function getIssuingCarrierCode()
    {
        return $this->IssuingCarrierCode;
    }
    function setIssuingCarrierCode($IssuingCarrierCode, $charset = 'iso-8859-1')
    {
        $this->IssuingCarrierCode = $IssuingCarrierCode;
        $this->_elements['IssuingCarrierCode']['charset'] = $charset;
    }
    function getCustomerCode()
    {
        return $this->CustomerCode;
    }
    function setCustomerCode($CustomerCode, $charset = 'iso-8859-1')
    {
        $this->CustomerCode = $CustomerCode;
        $this->_elements['CustomerCode']['charset'] = $charset;
    }
    function getTotalFare()
    {
        return $this->TotalFare;
    }
    function setTotalFare($TotalFare, $charset = 'iso-8859-1')
    {
        $this->TotalFare = $TotalFare;
        $this->_elements['TotalFare']['charset'] = $charset;
    }
    function getTotalTaxes()
    {
        return $this->TotalTaxes;
    }
    function setTotalTaxes($TotalTaxes, $charset = 'iso-8859-1')
    {
        $this->TotalTaxes = $TotalTaxes;
        $this->_elements['TotalTaxes']['charset'] = $charset;
    }
    function getTotalFee()
    {
        return $this->TotalFee;
    }
    function setTotalFee($TotalFee, $charset = 'iso-8859-1')
    {
        $this->TotalFee = $TotalFee;
        $this->_elements['TotalFee']['charset'] = $charset;
    }
    function getRestrictedTicket()
    {
        return $this->RestrictedTicket;
    }
    function setRestrictedTicket($RestrictedTicket, $charset = 'iso-8859-1')
    {
        $this->RestrictedTicket = $RestrictedTicket;
        $this->_elements['RestrictedTicket']['charset'] = $charset;
    }
    function getClearingSequence()
    {
        return $this->ClearingSequence;
    }
    function setClearingSequence($ClearingSequence, $charset = 'iso-8859-1')
    {
        $this->ClearingSequence = $ClearingSequence;
        $this->_elements['ClearingSequence']['charset'] = $charset;
    }
    function getClearingCount()
    {
        return $this->ClearingCount;
    }
    function setClearingCount($ClearingCount, $charset = 'iso-8859-1')
    {
        $this->ClearingCount = $ClearingCount;
        $this->_elements['ClearingCount']['charset'] = $charset;
    }
    function getFlightDetails()
    {
        return $this->FlightDetails;
    }
    function setFlightDetails($FlightDetails, $charset = 'iso-8859-1')
    {
        $this->FlightDetails = $FlightDetails;
        $this->_elements['FlightDetails']['charset'] = $charset;
    }
}
