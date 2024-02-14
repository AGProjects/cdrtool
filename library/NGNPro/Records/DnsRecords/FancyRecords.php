<?php

class FancyRecords extends DnsRecords
{
    var $fancy = true;

    var $addRecordFunction    = 'addFancyRecord';
    var $deleteRecordFunction = 'deleteFancyRecord';
    var $updateRecordFunction = 'updateFancyRecord';
    var $getRecordsFunction   = 'getFancyRecords';
    var $getRecordFunction    = 'getFancyRecord';

    var $recordTypesTemplate = array();

    var $Fields = array(
        'type'     => array('type'=>'string'),
        'value'    => array('type'=>'string')
    );
}

