<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDSimpleType.php';

/**
 * SchedulingInfoType
 * 
 * Contains information for Scheduling limits for the user. All dtails must be
 * present,unless we will have revise call one day, just in case we might let's
 * make min occur = 0
 *
 * @package PayPal
 */
class SchedulingInfoType extends XSDSimpleType
{
    var $MaxScheduledMinutes;

    var $MinScheduledMinutes;

    var $MaxScheduledItems;

    function SchedulingInfoType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'MaxScheduledMinutes' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MinScheduledMinutes' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MaxScheduledItems' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getMaxScheduledMinutes()
    {
        return $this->MaxScheduledMinutes;
    }
    function setMaxScheduledMinutes($MaxScheduledMinutes, $charset = 'iso-8859-1')
    {
        $this->MaxScheduledMinutes = $MaxScheduledMinutes;
        $this->_elements['MaxScheduledMinutes']['charset'] = $charset;
    }
    function getMinScheduledMinutes()
    {
        return $this->MinScheduledMinutes;
    }
    function setMinScheduledMinutes($MinScheduledMinutes, $charset = 'iso-8859-1')
    {
        $this->MinScheduledMinutes = $MinScheduledMinutes;
        $this->_elements['MinScheduledMinutes']['charset'] = $charset;
    }
    function getMaxScheduledItems()
    {
        return $this->MaxScheduledItems;
    }
    function setMaxScheduledItems($MaxScheduledItems, $charset = 'iso-8859-1')
    {
        $this->MaxScheduledItems = $MaxScheduledItems;
        $this->_elements['MaxScheduledItems']['charset'] = $charset;
    }
}
