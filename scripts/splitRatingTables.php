#!/usr/bin/php
<?
require("/etc/cdrtool/global.inc");
require("rating.php");

$RatingTables = new RatingTables();
$RatingTables->splitRatingTable();

?>
