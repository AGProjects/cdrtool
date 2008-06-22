#!/usr/bin/php
<?
set_time_limit (0);
ini_set('mbstring.func_overload', '0');
ini_set('output_handler', '');
@ob_end_flush();

define_syslog_variables();
openlog("CDRTool rating", LOG_PID, LOG_LOCAL0);

require('/etc/cdrtool/global.inc');
require('cdr_generic.php');
require('rating.php');
require('daemon.php');
require('ratingServer.php');

if (!strlen($RatingEngine['socketIP']) || !$RatingEngine['socketPort'] || !$RatingEngine['CDRS_class']) {
    die('Please define RatingEngine[socketIP], RatingEngine[socketPort] and RatingEngine[CDRS_class] in global.inc\n');
}

// Init CDRS
$CDR_class  = $DATASOURCES[$RatingEngine['CDRS_class']]["class"];
$CDRS       = new $CDR_class($RatingEngine['CDRS_class']);

// Load rating tables
$CDRS->RatingTables = new RatingTables();
$CDRS->RatingTables->LoadRatingTables();

// Init RatingEngine engine
$RatingEngineServer = new RatingEngine($CDRS);

// Go to the background
$d = new Daemon('ratingEngine','/var/run/ratingEngine.pid');
$d->start();

$daemon = new socketDaemon();
$server = $daemon->create_server('ratingEngineServer', 'ratingEngineClient', $RatingEngine['socketIP'], $RatingEngine['socketPort']);
$daemon->process();

?>
