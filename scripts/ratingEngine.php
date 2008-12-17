#!/usr/bin/php
<?
set_time_limit (0);
ini_set('mbstring.func_overload', '0');
ini_set('output_handler', '');
@ob_end_flush();

require('/etc/cdrtool/global.inc');
require('cdr_generic.php');
require('rating.php');
require('rating_server.php');

if (!strlen($RatingEngine['socketIP']) || !$RatingEngine['socketPort'] || !$RatingEngine['cdr_source']) {
    $log=sprintf("Please define \$RatingEngine['socketIP'], \$RatingEngine['socketPort'] and \$RatingEngine['cdr_source'] in /etc/cdrtool/global.inc\n");
    syslog(LOG_NOTICE,$log);
    die ($log);
}

if (!is_array($DATASOURCES[$RatingEngine['cdr_source']])) {
    $log=sprintf("Datasource '%s' does not exist in /etc/cdrtool/global.inc\n",$RatingEngine['cdr_source']);
    syslog(LOG_NOTICE,$log);
    die ($log);
}

// Init CDRS
$CDR_class  = $DATASOURCES[$RatingEngine['cdr_source']]["class"];
$CDRS       = new $CDR_class($RatingEngine['cdr_source']);

// Load rating tables
$CDRS->RatingTables = new RatingTables();
$CDRS->RatingTables->LoadRatingTables();

// Init RatingEngine engine
$RatingEngineServer = new RatingEngine($CDRS);

// Go to the background
$d = new Daemon('/var/run/ratingEngine.pid');
$d->start();

$daemon = new socketDaemon();
$server = $daemon->create_server('ratingEngineServer', 'ratingEngineClient', $RatingEngine['socketIP'], $RatingEngine['socketPort']);
$daemon->process();

?>
