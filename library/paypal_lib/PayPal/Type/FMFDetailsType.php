<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * FMFDetailsType
 * 
 * Thes are filters that could result in accept/deny/pending action.
 *
 * @package PayPal
 */
class FMFDetailsType extends XSDSimpleType
{
    var $AcceptFilters;

    var $PendingFilters;

    var $DenyFilters;

    var $ReportFilters;

    function FMFDetailsType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AcceptFilters' => 
              array (
                'required' => false,
                'type' => 'RiskFilterListType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PendingFilters' => 
              array (
                'required' => false,
                'type' => 'RiskFilterListType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'DenyFilters' => 
              array (
                'required' => false,
                'type' => 'RiskFilterListType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReportFilters' => 
              array (
                'required' => false,
                'type' => 'RiskFilterListType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAcceptFilters()
    {
        return $this->AcceptFilters;
    }
    function setAcceptFilters($AcceptFilters, $charset = 'iso-8859-1')
    {
        $this->AcceptFilters = $AcceptFilters;
        $this->_elements['AcceptFilters']['charset'] = $charset;
    }
    function getPendingFilters()
    {
        return $this->PendingFilters;
    }
    function setPendingFilters($PendingFilters, $charset = 'iso-8859-1')
    {
        $this->PendingFilters = $PendingFilters;
        $this->_elements['PendingFilters']['charset'] = $charset;
    }
    function getDenyFilters()
    {
        return $this->DenyFilters;
    }
    function setDenyFilters($DenyFilters, $charset = 'iso-8859-1')
    {
        $this->DenyFilters = $DenyFilters;
        $this->_elements['DenyFilters']['charset'] = $charset;
    }
    function getReportFilters()
    {
        return $this->ReportFilters;
    }
    function setReportFilters($ReportFilters, $charset = 'iso-8859-1')
    {
        $this->ReportFilters = $ReportFilters;
        $this->_elements['ReportFilters']['charset'] = $charset;
    }
}
