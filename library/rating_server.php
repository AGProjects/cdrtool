<?php

if (function_exists('pcntl_async_signals')) {
    pcntl_async_signals(true);
} else {
    declare(ticks = 1);
}

function signalHandler($sig)
{
    $log = sprintf("Received signal %s", $sig);
    logger($log);

    switch ($sig) {
        case SIGTERM:
            logger("Rating Engine is exiting ...");
            exit(0);
        case SIGKILL:
            logger("Rating Engine is exiting ...");
            exit(0);
        case SIGUSR1:
            break;
        default:
            // handle all other signals
    }
}

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Daemon
{
    public $pidFile;

    public function __construct($pidFile = false)
    {
        $this->pidFile  = $pidFile;
    }

    public function start()
    {
        global $logger;

        $console_log = new StreamHandler('php://stdout');
        $formatter = new LineFormatter("[%level_name%] %channel%: %message% %extra%", null, true, true);
        $console_log->setFormatter($formatter);
        $logger->pushHandler($console_log);

        if ($this->pidFile !== false && file_exists($this->pidFile)) {
            $pf = fopen($this->pidFile, 'r');
            if (!$pf) {
                $log = sprintf("Error: Unable to read pidfile %s\n", $this->pidFile);
                critical($log);

                exit(-1);
            }

            $c = fgets($pf);
            fclose($pf);

            if ($c === false) {
                $log = sprintf("Error: Unable to read pidfile %s\n", $this->pidFile);
                critical($log);

                exit(-1);
            }

            $pid = intval($c);
            if (posix_kill($pid, 0) === true) {
                $log = sprintf("Error: Another process is already running on pid %d\n", $pid);
                critical($log);

                exit(-1);
            }
        }

        // do the Unix double fork magic
        $pid = pcntl_fork();
        if ($pid == -1) {
            $log = sprintf("Error: Couldn't fork!\n");
            critical($log);
            exit(-1);
        }

        if ($pid > 0) {
            // this is the parent. nothing to do
            exit(0);
        }

        // decouple from the parent environment
        chdir('/');
        if (posix_setsid() == -1) {
            $log=sprintf("Error: Could not detach from terminal\n");
            critical($log);
            exit(-1);
        }

        umask(022);

        // and now the second fork
        $pid = pcntl_fork();
        if ($pid == -1) {
            $log = sprintf("Error: Couldn't fork!\n");
            critical($log);
            exit(-1);
        }
        if ($pid > 0) {
            // this is the parent. nothing to do
            exit(0);
        }

        // this doesn't really work. it seems php is unable to close these
        //fclose(STDIN);
        //fclose(STDOUT);
        //fclose(STDERR);

        if ($this->pidFile) {
            $pf = fopen($this->pidFile, 'w');
            if ($pf === false) {
                $log = sprintf("Error: Unable to write pidfile %s\n", $this->pidFile);
                critical($log);
                exit(-1);
            }

            fwrite($pf, sprintf("%d\n", posix_getpid()));
            fclose($pf);
            register_shutdown_function(array(&$this, "removePid"));
        }

        //pcntl_signal(SIGCHLD, array(&$this, 'signalHandler'));
        //pcntl_signal(SIGTERM, array(&$this, 'signalHandler'));
        //pcntl_signal(SIGKILL, array(&$this, 'signalHandler'));

        // for some reason these interfere badly with socket_select()
        pcntl_signal(SIGTERM, "signalHandler", true);
        //  pcntl_signal(SIGKILL, "signalHandler", true);
        pcntl_signal(SIGUSR1, "signalHandler", true);
        logger("Rating Engine moved to the background");
        $logger->popHandler();
    }

    private function removePid()
    {
        if (file_exists($this->pid)) unlink($this->pid);
    }
}

/*
phpSocketDaemon 1.0
Copyright (C) 2006 Chris Chabot <chabotc@xs4all.nl>
See http://www.chabotc.nl/ for more information

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/


class SocketClient extends SocketCDR
{
    public $connecting     = false;
    public $disconnected   = false;
    public $read_buffer    = '';
    public $write_buffer   = '';

    public function connect($remote_address, $remote_port)
    {
        $this->connecting = true;
        try {
            parent::connect($remote_address, $remote_port);
        } catch (SocketException $e) {
            echo "Caught exception: ".$e->getMessage()."\n";
        }
    }

    public function write($buffer, $length = 4096)
    {
        $this->write_buffer .= $buffer;
        $this->doWrite();
    }

    public function doWrite()
    {
        $length = strlen($this->write_buffer);
        try {
            $written = parent::write($this->write_buffer, $length);
            if ($written < $length) {
                $this->write_buffer = substr($this->write_buffer, $written);
            } else {
                $this->write_buffer = '';
            }
            $this->onWrite();
            return true;
        } catch (SocketException $e) {
            $old_socket = ($this->socket instanceof \Socket ? spl_object_id($this->socket) : (int)$this->socket);
            $this->close();
            $this->socket       = $old_socket;
            $this->disconnected = true;
            $this->onDisconnect();
            return false;
        }
        return false;
    }

    public function read($length = 4096)
    {
        try {
            $this->read_buffer .= parent::read($length);
            $this->onRead();
        } catch (SocketException $e) {
            $old_socket = ($this->socket instanceof \Socket ? spl_object_id($this->socket) : (int)$this->socket);
            $this->close();
            $this->socket       = $old_socket;
            $this->disconnected = true;
            $this->onDisconnect();
        }
    }

    public function onConnect()
    {
    }

    public function onDisconnect()
    {
    }

    public function onRead()
    {
    }

    public function onWrite()
    {
    }

    public function onTimer()
    {
    }
}

class SocketServer extends SocketCDR
{
    public $startTime;
    protected $client_class;

    public function __construct($client_class, $bind_address = 0, $bind_port = 0, $domain = AF_INET, $type = SOCK_STREAM, $protocol = SOL_TCP)
    {
        parent::__construct($bind_address, $bind_port, $domain, $type, $protocol);
        $this->client_class = $client_class;
        $this->listen();
        if (!$bind_address) {
            $bind_address_print='0.0.0.0';
        } else {
            $bind_address_print=$bind_address;
        }
        $this->startTime=time();
        $log=sprintf("Rating Engine listening on %s:%s", $bind_address_print, $bind_port);
        logger($log);
    }

    public function accept()
    {
        $client = new $this->client_class(parent::accept(), $this);
        if (!is_subclass_of($client, 'SocketServerClient')) {
            throw new SocketException("Invalid serverClient class specified! Has to be a subclass of SocketServerClient");
        }
        $this->onAccept($client);
        return $client;
    }

    // override if desired
    public function onAccept(SocketServerClient $client)
    {
    }
}

class SocketServerClient extends SocketClient
{
    public $socket;
    public $remote_address;
    public $remote_port;
    public $local_addr;
    public $local_port;
    public $parentServer;
    public $ratingEngine;
    public $ratingEngineSettings;

    public function __construct($socket, $parentServer)
    {
        $this->socket         = $socket;
        $this->parentServer   = &$parentServer;

        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (!socket_getsockname($this->socket, $this->local_addr, $this->local_port)) {
            throw new SocketException("Could not retrieve local address & port: ".socket_strerror(socket_last_error($this->socket)));
        } elseif (!socket_getpeername($this->socket, $this->remote_address, $this->remote_port)) {
            throw new SocketException("Could not retrieve remote address & port: ".socket_strerror(socket_last_error($this->socket)));
        }

        global $RatingEngineServer, $RatingEngine;

        $this->ratingEngine = & $RatingEngineServer;
        $this->ratingEngineSettings = $RatingEngine;

        $this->setNonBlock();
        $this->onConnect();
    }
}

class SocketDaemon
{
    public $servers = array();
    public $clients = array();

    public function createServer($server_class, $client_class, $bind_address = 0, $bind_port = 0)
    {
        $server = new $server_class($client_class, $bind_address, $bind_port);
        if (!is_subclass_of($server, 'SocketServer')) {
            throw new SocketException("Invalid server class specified! Has to be a subclass of SocketServer");
        }
        $this->servers[$this->getSocketId($server->socket)] = $server;
        return $server;
    }

    public function createClient($client_class, $remote_address, $remote_port, $bind_address = 0, $bind_port = 0)
    {
        $client = new $client_class($bind_address, $bind_port);
        if (!is_subclass_of($client, 'SocketClient')) {
            throw new SocketException("Invalid client class specified! Has to be a subclass of SocketClient");
        }
        $client->setNonBlock(true);
        $client->connect($remote_address, $remote_port);
        $this->clients[$this->getSocketId($client->socket)] = $client;
        return $client;
    }

    private function getSocketId($socket)
    {
        return $socket instanceof \Socket ? spl_object_id($socket) : (int)$socket;
    }

    private function createReadSet()
    {
        $ret = array();
        foreach ($this->clients as $socket) {
            $ret[] = $socket->socket;
        }
        foreach ($this->servers as $socket) {
            $ret[] = $socket->socket;
        }
        return $ret;
    }

    private function createWriteSet()
    {
        $ret = array();
        foreach ($this->clients as $socket) {
            if (!empty($socket->write_buffer) || $socket->connecting) {
                $ret[] = $socket->socket;
            }
        }
        foreach ($this->servers as $socket) {
            if (!empty($socket->write_buffer)) {
                $ret[] = $socket->socket;
            }
        }
        return $ret;
    }

    private function createExceptionSet()
    {
        $ret = array();
        foreach ($this->clients as $socket) {
            $ret[] = $socket->socket;
        }
        foreach ($this->servers as $socket) {
            $ret[] = $socket->socket;
        }
        return $ret;
    }


    private function cleanSockets()
    {
        foreach ($this->clients as $socket) {
            if ($socket->disconnected || !is_resource($socket->socket)) {
                if (isset($this->clients[$this->getSocketId($socket->socket)])) {
                    unset($this->clients[$this->getSocketId($socket->socket)]);
                }
            }
        }
    }

    private function getClass($socket)
    {
        if (isset($this->clients[$this->getSocketId($socket)])) {
            return $this->clients[$this->getSocketId($socket)];
        } elseif (isset($this->servers[$this->getSocketId($socket)])) {
            return $this->servers[$this->getSocketId($socket)];
        } else {
            throw (new SocketException("Could not locate socket class for $socket"));
        }
    }

    public function process()
    {
        // if SocketClient is in write set, and $socket->connecting === true, set connecting to false and call onConnect
        $read_set      = $this->createReadSet();
        $write_set     = $this->createWriteSet();
        $exception_set = $this->createExceptionSet();
        $event_time    = time();
        while (($events = socket_select($read_set, $write_set, $exception_set, 2)) !== false) {
            if ($events > 0) {
                foreach ($read_set as $socket) {
                    $socket = $this->getClass($socket);
                    if (is_subclass_of($socket, 'SocketServer')) {
                        $client = $socket->accept();
                        $this->clients[$this->getSocketId($client->socket)] = $client;
                    } elseif (is_subclass_of($socket, 'SocketClient')) {
                        // regular onRead event
                        $socket->read();
                    }
                }
                foreach ($write_set as $socket) {
                    $socket = $this->getClass($socket);
                    if (is_subclass_of($socket, 'SocketClient')) {
                        if ($socket->connecting === true) {
                            $socket->onConnect();
                            $socket->connecting = false;
                        }
                        $socket->doWrite();
                    }
                }
                foreach ($exception_set as $socket) {
                    $socket = $this->getClass($socket);
                    if (is_subclass_of($socket, 'SocketClient')) {
                        $socket->onDisconnect();
                        if (isset($this->clients[$this->getSocketId($socket->socket)])) {
                            unset($this->clients[$this->getSocketId($socket->socket)]);
                        }
                    }
                }
            }
            if (time() - $event_time > 1) {
                // only do this if more then a second passed, else we'd keep looping this for every bit received
                foreach ($this->clients as $socket) {
                    $socket->onTimer();
                }
                $event_time = time();
            }
            $this->cleanSockets();
            $read_set      = $this->createReadSet();
            $write_set     = $this->createWriteSet();
            $exception_set = $this->createExceptionSet();
        }
    }
}

class SocketException extends Exception
{
}

class SocketCDR
{
    public $socket;
    public $bind_address;
    public $bind_port;
    public $domain;
    public $type;
    public $protocol;
    public $local_addr;
    public $local_port;
    public $read_buffer    = '';
    public $write_buffer   = '';
    public $remote_address = null;
    public $remote_port    = null;

    private function getSocketId($socket)
    {
        return $socket instanceof \Socket ? spl_object_id($socket) : (int)$socket;
    }

    public function __construct($bind_address = 0, $bind_port = 0, $domain = AF_INET, $type = SOCK_STREAM, $protocol = SOL_TCP)
    {
        $this->bind_address = $bind_address;
        $this->bind_port    = $bind_port;
        $this->domain       = $domain;
        $this->type         = $type;
        $this->protocol     = $protocol;
        if (($this->socket = @socket_create($domain, $type, $protocol)) === false) {
            throw new SocketException("Could not create socket: ".socket_strerror(socket_last_error($this->socket)));
        }
        if (!@socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            throw new SocketException("Could not set SO_REUSEADDR: ".$this->getError());
        }
        if (!@socket_bind($this->socket, $bind_address, $bind_port)) {
            throw new SocketException("Could not bind socket to [$bind_address - $bind_port]: ".socket_strerror(socket_last_error($this->socket)));
        }
        if (!@socket_getsockname($this->socket, $this->local_addr, $this->local_port)) {
            throw new SocketException("Could not retrieve local address & port: ".socket_strerror(socket_last_error($this->socket)));
        }
        $this->setNonBlock(true);
    }

    public function __destruct()
    {
        if (is_resource($this->socket) || $this->socket instanceof \Socket) {
            $this->close();
        }
    }

    public function getError()
    {
        $error = socket_strerror(socket_last_error($this->socket));
        socket_clear_error($this->socket);
        return $error;
    }

    public function close()
    {
        if (is_resource($this->socket) || $this->socket instanceof \Socket) {
            @socket_shutdown($this->socket, 2);
            @socket_close($this->socket);
        }
        $this->socket = $this->getSocketId($this->socket);
    }

    public function write($buffer, $length = 4096)
    {
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (($ret = @socket_write($this->socket, $buffer, $length)) === false) {
            throw new SocketException("Could not write to socket: ".$this->getError());
        }
        return $ret;
    }

    public function read($length = 4096)
    {
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (($ret = @socket_read($this->socket, $length, PHP_BINARY_READ)) == false) {
            throw new SocketException("Could not read from socket: ".$this->getError());
        }
        return $ret;
    }

    public function connect($remote_address, $remote_port)
    {
        $this->remote_address = $remote_address;
        $this->remote_port    = $remote_port;
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (!@socket_connect($this->socket, $remote_address, $remote_port)) {
            throw new SocketException("Could not connect to {$remote_address} - {$remote_port}: ".$this->getError());
        }
    }

    public function listen($backlog = 128)
    {
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (!@socket_listen($this->socket, $backlog)) {
            throw new SocketException("Could not listen to {$this->bind_address} - {$this->bind_port}: ".$this->getError());
        }
    }

    public function accept()
    {
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (($client = socket_accept($this->socket)) === false) {
            throw new SocketException("Could not accept connection to {$this->bind_address} - {$this->bind_port}: ".$this->getError());
        }
        return $client;
    }

    public function setNonBlock()
    {
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (!@socket_set_nonblock($this->socket)) {
            throw new SocketException("Could not set socket non_block: ".$this->getError());
        }
    }

    public function setBlock()
    {
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (!@socket_set_block($this->socket)) {
            throw new SocketException("Could not set socket non_block: ".$this->getError());
        }
    }

    public function setRecieveTimeout($sec, $usec)
    {
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (!@socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $sec, "usec" => $usec))) {
            throw new SocketException("Could not set socket recieve timeout: ".$this->getError());
        }
    }

    public function setReuseAddress($reuse = true)
    {
        $reuse = $reuse ? 1 : 0;
        if (!is_resource($this->socket) && !($this->socket instanceof \Socket)) {
            throw new SocketException("Invalid socket or resource");
        } elseif (!@socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, $reuse)) {
            throw new SocketException("Could not set SO_REUSEADDR to '$reuse': ".$this->getError());
        }
    }
}

class RatingEngineServer extends SocketServer
{
    public $requests = array();
    public $connected_clients = array();
}

class RatingEngineClient extends SocketServerClient
{
    private function handleRequest($request)
    {
        $this->parentServer->requests[$this->remote_address]++;

        $b = microtime(true);
        $output  = $this->ratingEngine->processNetworkInput($request);
        $output .= "\n\n";

        if ($this->ratingEngineSettings['log_delay']) {
            $e=microtime(true);
            $d=$e-$b;
            if ($d >= $this->ratingEngineSettings['log_delay']) {
                $log = sprintf("%s request took %.4f seconds", $this->ratingEngine->method, $d);
                syslog(LOG_NOTICE, $log);
            }
        }

        $log = sprintf("Output %s", $output);
        syslog(LOG_NOTICE, $log);
        return $output;
    }

    public function onRead()
    {
        $tinput = trim($this->read_buffer);
        if ($tinput == 'exit' || $tinput =='quit') {
            $this->onDisconnect();
            $this->close();
        } elseif (strtolower($tinput) == 'showclients') {
            $output = '';
            $j = 0;

            $uptime=time()-$this->parentServer->startTime;

            if (count($this->parentServer->connected_clients)) {
                $output .= "\nClients:\n\n";
                foreach ($this->parentServer->connected_clients as $_client) {
                    $j++;
                    $myself=$this->remote_address.":".$this->remote_port;
                    if ($_client == $myself) {
                        $output .= sprintf("%d. %s (myself)\n", $j, $_client);
                    } else {
                        $output .= sprintf("%d. %s\n", $j, $_client);
                    }
                }
            }

            if (count($this->parentServer->requests)) {
                $output .= "\nRequests:\n\n";

                $requests=0;
                foreach (array_keys($this->parentServer->requests) as $_client_ip) {
                    $output .= sprintf("%d requests from %s\n", $this->parentServer->requests[$_client_ip], $_client_ip);
                    $requests = $requests + $this->parentServer->requests[$_client_ip];
                }
            }

            $output .= "\nStatistics:\n\n";

            $output .= sprintf("Total requests: %d\n", $requests);
            $output .= sprintf("Uptime: %d seconds\n", $uptime);
            if ($uptime) $output .= sprintf("Load: %0.2f/s\n", $requests / $uptime);

            $output .= "\n\n";
            $this->write($output);
            $this->read_buffer  = '';
        } elseif ($tinput) {
            $this->write($this->handleRequest($tinput));
            $this->read_buffer  = '';
        }
    }

    public function onConnect()
    {
        if ($this->remote_address != '127.0.0.1') {
            if (is_array($this->ratingEngineSettings['allow'])) {
                $allow_connection = false;
                foreach ($this->ratingEngineSettings['allow'] as $_allow) {
                    if (preg_match("/^$_allow/", $this->remote_address)) {
                        $log = sprintf("Client %s allowed by server configuration %s", $this->remote_address, $_allow);
                        syslog(LOG_NOTICE, $log);
                        $allow_connection = true;
                        break;
                    }
                }
                if (!$allow_connection) {
                    $log = sprintf("Client %s disallowed by server configuration", $this->remote_address);
                    syslog(LOG_NOTICE, $log);
                    $this->close();
                    return true;
                }
            }
        }

        $_client = $this->remote_address.":".$this->remote_port;

        $this->parentServer->connected_clients[] = $_client;
        $this->parentServer->connected_clients = array_unique($this->parentServer->connected_clients);

        $log = sprintf("Client connection from %s:%s", $this->remote_address, $this->remote_port);
        syslog(LOG_NOTICE, $log);
    }

    public function onDisconnect()
    {
        $new_clients = array();
        foreach ($this->parentServer->connected_clients as $_client) {
            $_connected_client=$this->remote_address.":".$this->remote_port;
            if ($_connected_client == $_client) continue;
            $new_clients[]=$_client;
        }

        $this->parentServer->connected_clients=array_unique($new_clients);

        $log = sprintf("Client disconnection from %s:%s", $this->remote_address, $this->remote_port);
        syslog(LOG_NOTICE, $log);
    }

    public function onWrite()
    {
    }

    public function onTimer()
    {
    }
}
?>
