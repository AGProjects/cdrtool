#!/usr/bin/php
<?
require("/etc/cdrtool/global.inc");
require("rating_lib.phtml");

$RatingTables = new RatingTables();
$RatingTables->splitRatingTable();

?>
