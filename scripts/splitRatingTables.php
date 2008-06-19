#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
require($path."/../global.inc");
require("rating_lib.phtml");

$RatingTables = new RatingTables();
$RatingTables->splitRatingTable();

?>
