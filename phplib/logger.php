<?php

require 'Monolog/autoload.php';

use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\LineFormatter;

global $logger;

$logger = new Logger('WEB');
$syslog = new SyslogHandler('cdrtool', 'local0');
$formatter = new LineFormatter("%channel%: %message% %extra%");
$syslog->setFormatter($formatter);
$logger->pushHandler($syslog);

function logger($message, $level = 'notice')
{
    global $logger;
    if ($level == 'notice') {
        notice($message);
    }
}

function notice($message)
{
    global $logger;
    $logger->notice($message);
}
