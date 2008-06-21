<?

// tick use required as of PHP 4.3.0
declare(ticks = 1);

function signalHandler($sig)
{
    print "Program received signal $sig.";
    switch ($sig) {
    case SIGTERM:
    case SIGKILL:
        // handle shutdown tasks
        print " Exiting\n";
        exit(0);
    case SIGUSR1:
        break;
    default:
        // handle all other signals
    }
}

class Daemon {
    var $name;
    var $pid;
    var $syslog;
    var $path;

    function Daemon($name='<daemon>', $pidFile=false, $useSyslog=true) {
        /*
        error_reporting(0);
        set_time_limit(0);
        ob_implicit_flush();
        */

        $this->name = $name;
        $this->pid  = $pidFile;
        $this->syslog = $useSyslog;
        $this->path = dirname(realpath($_SERVER['PHP_SELF']));
        if ($pidFile!==false && $pidFile[0]!='/') {
            if ($pidFile[0]=='.' && $pidFile[1]=='/') {
                $this->pid = $this->path . '/' . substr($pidFile, 2);
            } else {
                $this->pid = $this->path . '/' . $pidFile;
            }
        }
    }

    function start() {
        // check pidfile
        $pidfile = $this->pid;
        if ($pidfile!==false && file_exists($pidfile)) {
            $pf = fopen($pidfile, 'r');
            if (!$pf) {
                print "Unable to read pidfile $pidfile\n";
                exit(-1);
            }
            $c = fgets($pf);
            fclose($pf);
            if ($c===false) {
                print "Unable to read pidfile $pidfile\n";
                exit(-1);
            }
            $pid = intval($c);
            if (posix_kill($pid, 0)===true) {
                print "Another process is already running on pid $pid\n";
                exit(-1);
            }
        }

        // do the Unix double fork magic
        $pid = pcntl_fork();
        if ($pid == -1) {
            print "Couldn't fork!\n";
            exit(-1);
        }
        if ($pid > 0) {
            // this is the parent. nothing to do
            exit(0);
        }

        // decouple from the parent environment
        chdir('/');
        posix_setsid();
        umask(022);

        // and now the second fork
        $pid = pcntl_fork();
        if ($pid == -1) {
            print "Couldn't fork!\n";
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

        if ($pidfile) {
            $pf = fopen($pidfile, 'w');
            fwrite($pf, sprintf("%d\n", posix_getpid()));
            fclose($pf);
            register_shutdown_function(array(&$this, "removePid"));
        }
        
      	// pcntl_signal(SIGCHLD, array(&$this, 'signalHandler'));
      	// pcntl_signal(SIGTERM, array(&$this, 'signalHandler'));
      	// pcntl_signal(SIGKILL, array(&$this, 'signalHandler'));


        // for some reason these interfere badly with socket_select()
        //pcntl_signal(SIGTERM, "signalHandler", true);
        //pcntl_signal(SIGKILL, "signalHandler", true);
        //pcntl_signal(SIGUSR1, "signalHandler", true);

    }

    function removePid() {
        unlink($this->pid);
    }
}
?>
