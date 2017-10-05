#!/usr/bin/php
<?php
require("/etc/cdrtool/global.inc");
require('cdr_generic.php');
require("rating.php");

if (!reloadRatingEngineTables()) {
    die("Error: Cannot connect to network Rating Engine $errstr ($errno). \n");
} else {
    printf(
        "Rating tables will be reloaded during next normalization. See syslog for more information. \n",
        $RatingEngine["socketIP"],
        $RatingEngine["socketPort"]
    );
}
?>
