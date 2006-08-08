#!/usr/bin/php
<?
# This script import RADIUS records from FREERADIUS generated files
# It accepts two arguments, first argument is the radacct file with
# the full path, the second argument is an optional timezone against
# which the timestamps are calculated
# (defaults to CDRTool[provider][timezone] from global.inc)
# Example: importRadius.php /tmp/detail-20040221 Europe/Amsterdam

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../cdrlib.phtml");

$cdr_source = "cisco";
$CDR_class  = $DATASOURCES[$cdr_source]["class"];
$CDRS       = new $CDR_class($cdr_source);

if (checkArguments($argv)) {
    $CDRS->import($argv[1],$argv[2]);
    print "Normalizing cdrs: ";
    $results=$CDRS->NormalizeCDRS();
    print "$results[total] not yet normalized calls,$results[success] normalized,$results[failure] skipped\n";
}

function checkArguments($argv) {
    $PHP_SELF=$_SERVER['PHP_SELF'];
	$c=count($argv);
    if (!count($argv) || count($argv) < 2 || count($argv) > 3) {
    	print "Syntax: $PHP_SELF radacctFile [timezone]\n";
		print "Example: $PHP_SELF /tmp/detail-20040221 Europe/Amsterdam\n";
        return 0;
    }

    if (!is_readable($argv[1])) {
        print "Error: cannot open file $argv[1]\n";
        return 0;
    }

    if ($argv[2]) {
        $TZfile=$path."../../timezones";
        $tzones=explode("\n",file_get_contents($TZfile));
        if (!in_array($argv[2],$tzones)) {
	        print "Error: unexisting timezone $argv[2]. See timezones file for available timezones.\n";
            return 0;
        }
    }
    return 1;
}
?>
