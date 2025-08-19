<?php

class CSVWritter
{
    public $csv_directory      = '/var/spool/cdrtool/normalize';
    public $filename_extension = '.csv';
    var $fields             = array();
    var $ready              = false;
    var $cdr_type           = array();
    var $lines              = 0;
    private $cdr_source;
    private $directory;
    private $directory_ready;

    public function __construct($cdr_source = '', $csv_directory = '')
    {
        if ($cdr_source) {
            $this->cdr_source = $cdr_source;
        } else {
            $this->cdr_source = 'unknown';
        }

        if ($csv_directory) {
            if (is_dir($csv_directory)) {
                $this->csv_directory = $csv_directory;
            } else {
                $log=sprintf ("CSV writter error: %s is not a directory\n", $csv_directory);
                syslog(LOG_NOTICE, $log);
                return false;
            }
        }

        $this->directory = $this->csv_directory."/".date("Ymd");

        if (!is_dir($this->directory)) {
            if (!mkdir($this->directory)) {
                $log=sprintf ("CSV writter error: cannot create directory %s\n", $this->directory);
                syslog(LOG_NOTICE, $log);
                return false;
            }
            chmod($this->directory, 0775);
        }

        $this->directory_ready = true;
    }

    function open_file ($filename_suffix = '')
    {
        if ($this->ready) return true;

        if (!$this->directory_ready) return false;

        if (!$filename_suffix) {
            $log=sprintf ("CSV writter error: no filename suffix provided\n");
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $this->filename_prefix = strtolower($this->cdr_source).'-'.date('YmdHi');

        $this->full_path=rtrim($this->directory, '/').'/'.$this->filename_prefix.'-'.$filename_suffix.$this->filename_extension;

        $this->full_path_tmp=$this->full_path.'.tmp';

        if (!$this->fp = fopen($this->full_path_tmp, 'w')) {
            $log=sprintf ("CSV writter error: cannot open %s for writing\n", $this->full_path_tmp);
            syslog(LOG_NOTICE, $log);
            return false;
        }

        $this->ready = true;
        return true;
    }

    function close_file()
    {
        if (!$this->ready) return false;

        fclose($this->fp);

        if (!rename($this->full_path_tmp, $this->full_path)) {
            $log=sprintf ("CSV writter error: cannot rename %s to %s\n", $this->full_path_tmp, $this->full_path);
            syslog(LOG_NOTICE, $log);
        } else {
            $log=sprintf ("%d normalized CDRs written to %s\n", $this->lines, $this->full_path);
            syslog(LOG_NOTICE, $log);
        }
    }

    function write_cdr($CDR)
    {
        if (!$this->ready) return false;

        $line = sprintf(
            "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
            $CDR->id,
            $CDR->callId,
            $CDR->flow,
            $CDR->application,
            $CDR->username,
            $CDR->CanonicalURI,
            $CDR->startTime,
            $CDR->stopTime,
            $CDR->duration,
            $CDR->DestinationId,
            $CDR->BillingPartyId,
            $CDR->ResellerId,
            $CDR->price
        );

        if (!fputs($this->fp, $line)) {
            $this->ready = false;
            return false;
        }

        $this->lines++;

        return true;
    }
}
