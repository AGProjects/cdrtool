#!/usr/bin/php
<?
// CDRTool rating engine is a daemon that provides pricing information based
// on CDRTool rating tables via the network using a TCP socket defined in
// global.inc You may use a B2BUA in combination with this program to
// achieve prepaid functionality.
//
// Available functions
// 1. Calculate the price of a finished SIP session.
// 2. Show the maximum session time allowed based on caller information
//    and destination, the user quota is defined in ser.ser_quota table
// 3. Update balance of a prepaid subscriber based on a finished session
// 4. Add balance to a prepaid subscriber
// 5. Get current balance for a prepaid subscriber
// 6. Get last calls for a subscriber
// 7. Reload rating tables (useful after a CSV import or WEB change)
//
// For the syntax telnet to the daemon port and type Help
//
// - From and To pairs may contain extra SIP related signaling
// parameters as their are stripped out by the normalization engine
// - For MaxSessionTime Value must be an integer in seconds
// - For Balance updates Value can be any number
// - You may supply the pairs key=value in any order providing all are
// present on the same line, multiple spaces are allowed
// - Lines must be terminated with \n
// - When checking MaxSessionTime set Duration to the maximum allowed
// duration independent
//
// The daemon returns an integer in seconds, locked or none when checking
// MaxSessionTime or textual information for other Actions, line is terminated by \n
// The daemon does not close the connection with the client unless you
// explicitly provide an exit\n command, this is useful under heavy loads
// the output stops after two lines containing only \n
//
// Set time limit to indefinite execution
set_time_limit (0);
define_syslog_variables();
openlog("CDRTool", LOG_PID, LOG_LOCAL0);

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../cdrlib.phtml");

if ( $RatingEngine['socketIP'] && $RatingEngine['socketPort'] && $RatingEngine['CDRS_class']) {
    $address 	= $RatingEngine['socketIP'];
    $port 	= $RatingEngine['socketPort'];
    $cdr_source = $RatingEngine['CDRS_class'];
}

if (!$address || !$port || !$cdr_source) {
    // Set the IP and Port we will listen on
    // make sure you protect access to this socket by external means
    die('Please define RatingEngine[socketIP], RatingEngine[socketPort] and RatingEngine[CDRS_class] in global.inc');
}

// Load daemon functionality
require_once(dirname(realpath($_SERVER['PHP_SELF'])). '/daemon.php');
$d = new Daemon('ratingEngine','/var/run/ratingEngine.pid');
$d->start();

$max_clients = 50;

// Array that will hold client information
$clients = array(); 

// Create the server socket 
$sock = socket_create(AF_INET, SOCK_STREAM, 0);

// set to reuse address after exit
$reuse = socket_get_option($sock, SOL_SOCKET, SO_REUSEADDR) | 1;
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, $reuse);

// Bind the socket to an address/port 
socket_bind($sock, $address, $port) or die('Could not bind socket to $address:$port'); 

// Start listening for connections 
socket_listen($sock, $max_clients);

$_versionFile=$CDRTool['Path']."/version";
$version=trim(file_get_contents($_versionFile));

syslog(LOG_NOTICE,"CDRTool $version rating engine started, listening on $address:$port");

// Init CDRS
$CDR_class  = $DATASOURCES[$cdr_source]["class"];
$CDRS       = new $CDR_class($cdr_source);

// Load rating tables
$CDRS->RatingTables = new RatingTables();
$CDRS->RatingTables->LoadRatingTables();

// Init RatingEngine engine
$RatingEngine = new RatingEngine($CDRS);
$RatingEngine->loadPrepaidAccounts();

// Start main loop here
while (true) { 
    // Setup clients listen socket for reading 
    $read = array();
    $read[0] = $sock; // always listen on server socket
    foreach ($clients as $client) {
        $read[] = $client;
    }
    // Set up a blocking call to socket_select() 
    $write = $except = array();
    $ready = socket_select($read, $write, $except, 1, 0);

    if ($ready == 0)
        continue;

    foreach ($read as $con) {
        if ($con == $sock) {
            $newcon = socket_accept($sock);
            $clients[] = $newcon;

            /*
            if (socket_getpeername($newcon, $ip, $port)) {
                $log=sprintf ("Connection from %s:%d", $ip, $port);
	            syslog(LOG_NOTICE, $log);
            }
            */

        } else {
            $pos = array_search($con, $clients);
            $input = socket_read($con, 8192); 
            if ($input == null) {
                // Zero length string meaning disconnected
                socket_close($con);
                unset($clients[$pos]);
                continue;
            } 
            $tinput = trim($input);
            if ($tinput == 'exit' || $tinput =='quit') {
                // requested disconnect 
                socket_shutdown($con, 2);
                unset($clients[$pos]);
            } elseif ($tinput) {
                $output=$RatingEngine->processNetworkInput($tinput);
                $output=$output."\n\n";
                socket_write($con, $output);
            }
        }
    }
}
?>
