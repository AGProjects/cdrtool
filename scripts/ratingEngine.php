#!/usr/bin/env php
<?php
set_time_limit(0);
ini_set('mbstring.func_overload', '0');
ini_set('output_handler', '');
@ob_end_flush();

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'rating.php';
require 'rating_server.php';

// override logger for rating engine
use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\LineFormatter;

global $logger;
$logger = new Logger('RatingEngine');
$syslog = new SyslogHandler('cdrtool', 'local0');
$formatter = new LineFormatter("%channel%: %message% %extra%", null, false, true);
$syslog->setFormatter($formatter);
$logger->pushHandler($syslog);

// Init Rating Engine
logger("Starting CDRTool Rating Engine...");

$RatingEngineServer = new RatingEngine();

if (!$RatingEngineServer->init_ok) {
    critical('Error: Cannot start Rating Engine, fix the errors and try again');
    exit;
}

logger("Rating Engine initialized");

// Go to the background
$d = new Daemon('/var/run/ratingEngine.pid');
$d->start();

$daemon = new SocketDaemon();
$server = $daemon->createServer(
    'RatingEngineServer',
    'RatingEngineClient',
    $RatingEngine['socketIP'],
    $RatingEngine['socketPort']
);

logger("Rating Engine is now ready to serve network requests");

$daemon->process();
