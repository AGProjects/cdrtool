#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../rating_lib.phtml");

if (!reloadSipAccountsWithQuota()) {
	die("Error: Cannot connect to network Rating Engine $errstr ($errno). \n");
} else {
    printf ("Accounts have been reload. See syslog for more information. \n");
}
?>
