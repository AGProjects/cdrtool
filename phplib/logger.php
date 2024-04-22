<?php

require 'Monolog/autoload.php';

use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\WebProcessor;

global $logger;

$logger = new Logger('WEB');
$syslog = new SyslogHandler('cdrtool', 'local0');
$formatter = new LineFormatter("%channel%: %message% %extra%");
$syslog->setFormatter($formatter);
$logger->pushHandler($syslog);
$logger->pushProcessor(new WebProcessor(null, ['ip']));

global $browserLogger;
$browserLogger = new Logger('CDRTool');
$console= new BrowserConsoleHandler();
$browserLogger->pushHandler($console);

function changeLoggerChannel($name)
{
    global $logger;
    $logger = $logger->withName($name);
    $handler = $logger->popHandler();
    $formatter = new LineFormatter("%channel%: %message% %extra%", null, false, true);
    $handler->setFormatter($formatter);
    $logger->pushHandler($handler);
}

function logger($message, $level = 'notice')
{
    if ($level == 'notice') {
        notice($message);
    } elseif ($level == 'error') {
        error($message);
    } elseif ($level == 'critical') {
        critical($message);
    }
}

function loggerAndPrint($message, $level = 'notice')
{
    if ($level == 'notice') {
        noticeAndPrint($message);
    }
}

function notice($message)
{
    global $logger;
    $logger->notice($message);
}

function warning($message)
{
    global $logger;
    $logger->warning($message);
}

function error($message)
{
    global $logger;
    $logger->error($message);
}

function critical($message)
{
    global $logger;
    $logger->critical($message);
}

function noticeAndPrint($message)
{
    print "$message";
    notice($message);
}

function warningAndPrint($message)
{
    global $logger;
    print "$message";
    $logger->warning($message);
}

function errorAndPrint($message)
{
    global $logger;
    print "$message";
    $logger->error($message);
}

function criticalAndPrint($message)
{
    print "$message";
    critical($message);
}
