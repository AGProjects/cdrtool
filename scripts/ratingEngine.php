#!/usr/bin/php
<?php
set_time_limit(0);
ini_set('mbstring.func_overload', '0');
ini_set('output_handler', '');
@ob_end_flush();

require('/etc/cdrtool/global.inc');
require('cdr_generic.php');
require('rating.php');
require('rating_server.php');

// Init Rating Engine
syslog(LOG_NOTICE, "Starting CDRTool Rating Engine...");

$RatingEngineServer = new RatingEngine();

if (!$RatingEngineServer->init_ok) {
    syslog(LOG_NOTICE, 'Error: Cannot start Rating Engine, fix the errors and try again');
    exit;
}

syslog(LOG_NOTICE, "Rating Engine started sucesfully, going to background...");

// Go to the background
$d = new Daemon('/var/run/ratingEngine.pid');
$d->start();

$daemon = new socketDaemon();
$server = $daemon->create_server(
    'ratingEngineServer',
    'ratingEngineClient',
    $RatingEngine['socketIP'],
    $RatingEngine['socketPort']
);

syslog(LOG_NOTICE, "Rating Engine is now ready to serve network requests");

$daemon->process();

?>
