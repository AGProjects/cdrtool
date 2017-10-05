#!/usr/bin/php
<?php

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';

$SipTrace = new SIP_trace("sip_trace");
$SipTrace->purgeRecords();
