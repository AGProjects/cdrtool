#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../rating_lib.phtml");

$RatingTables = new RatingTables();
$RatingTables->splitRatingTable();
?>
