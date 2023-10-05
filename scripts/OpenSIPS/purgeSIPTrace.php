#!/usr/bin/env php
<?php

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'errors.php';

try {
    $SipTrace = new SIP_trace("sip_trace");
    $SipTrace->purgeRecords();
} catch (DataSourceUndefinedError $e) {
    return;
}
