#!/usr/bin/env php
<?php

/**
 * This script creates monthly tables radacctYYYYMM
 * and copies CDR data from radacct table into it
 *
 * This script must run daily during a window of low traffic
 *
 * Note:
 * This script has not been tested anymore since the auto-rotation performed by
 * he MySQL stored procedures has been introduced in version 5.0
 */

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'rating.php';

$cdr_source     = "opensips_radius";
$CDR_class      = $DATASOURCES[$cdr_source]["class"];
$CDRS           = new $CDR_class($cdr_source);

$CDRS->rotateTable($argv[1], $argv[2], $argv[3]);
