<?php
/**
 * This library can be used to display the status of mysql replication of
 * arbitrary number of mysql servers and instructions about how to fix
 * a broken replication process
 */

class DBx extends DB_Sql {
    public function __construct($user, $password, $host = 'localhost')
    {
        $this->Host = $host;
        $this->User = $user;
        $this->Password = $password;
        $this->Database = 'mysql';
        parent::__construct();
    }
}

class MySQLReplicationStatus {
    private $slave_status_query = "show slave status";
    private $master_status_query = "show master status";

    public $slave_master         = '';
    public $slave_user           = '';
    public $slave_master_port    = '';
    public $slave_log_file       = '';
    public $slave_position       = '';
    public $slave_sql_running    = '';
    public $slave_io_running     = '';
    public $slave_last_errno     = '';
    public $slave_last_error     = '';
    public $slave_seconds_behind = '';
    public $using_gtid           = '';
    public $gtid_io_pos          = '';
    public $replicate_wild_table = '';

    public $master_position = '';
    public $master_log_file = '';
    public $slave_of = '';
    public $color = '';

    public function __construct($host, $clusters)
    {
        $db = new DBx($clusters[$host]['user'], $clusters[$host]['password'], $clusters[$host]['ip']);

        $this->slave_of = $clusters[$host]['slave_of'];
        $this->color = $clusters[$host]['color'];

        if (!$db->query($this->slave_status_query)) {
            printf(
                "<p class='alert alert-danger'><strong>Error from MySQL server %s</strong>: %s (%s) for query: %s</p>",
                $clusters[$host]['ip'],
                $db->Error,
                $db->Errno,
                $this->slave_status_query
            );
            return false;
        }

        $db->next_record();

        $this->slave_master         = $db->f('Master_Host');
        $this->slave_user           = $db->f('Master_User');
        $this->slave_master_port    = $db->f('Master_Port');
        $this->slave_log_file       = $db->f('Master_Log_File');
        $this->slave_position       = $db->f('Read_Master_Log_Pos');
        $this->slave_sql_running    = $db->f('Slave_SQL_Running');
        $this->slave_io_running     = $db->f('Slave_IO_Running');
        $this->slave_last_errno     = $db->f('Last_Errno');
        $this->slave_last_error     = $db->f('Last_Error');
        $this->slave_seconds_behind = $db->f('Seconds_Behind_Master');
        $this->using_gtid           = $db->f('Using_Gtid');
        $this->gtid_io_pos          = $db->f('Gtid_IO_Pos');
        $this->replicate_wild_table = $db->f('Replicate_Wild_Do_Table');

        if (!$db->query($this->master_status_query)) {
            printf(
                "<p class='alert alert-danger'><strong>MySQL error</strong>: %s (%s) for query: %s</p>",
                $db->Error,
                $db->Errno,
                $this->master_status_query
            );
            return;
        }

        $db->next_record();

        $this->master_position = $db->f('Position');
        $this->master_log_file = $db->f('File');
    }
}

class ReplicationOverview {
    private $status = array();
    private $cluster;
    private $repair = [];

    protected $clusters = [];

    public function __construct($clusters = array())
    {
        $this->clusters = $clusters;

        $this->cluster = isset($_REQUEST['cluster']) ? $_REQUEST['cluster'] : '';
        $this->repair['server_to_repair'] = isset($_REQUEST['server']) ? $_REQUEST['server'] : '';

        $cluster_names=array_keys($this->clusters);

        if (!$this->cluster) {
            $this->cluster = $cluster_names[0];
        }

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            $this->status[$key] = new MySQLReplicationStatus($key, $this->clusters[$this->cluster]);
        }

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            foreach (array_keys($this->clusters[$this->cluster]) as $key2) {
                if ($key == $key2) {
                    continue;
                }
                if ($this->clusters[$this->cluster][$key2]['slave_of'] == $key) {
                    if (!isset($this->is_master)) {
                        $this->is_master = array();
                    }
                    if (!array_key_exists($key, $this->is_master)) {
                        $this->is_master[$key]=0;
                    }
                    $this->is_master[$key]++;
                }
            }
        }

        if ($this->repair['server_to_repair']) {
            $this->repair['master_server'] = $this->clusters[$this->cluster][$this->repair['server_to_repair']]['slave_of'];

            foreach (array_keys($this->clusters[$this->cluster]) as $key) {
                if ($this->repair['server_to_repair'] == $this->clusters[$this->cluster][$key]['slave_of']) {
                    $this->repair['has_slave'] = $key;
                    break;
                }
            }

            foreach (array_keys($this->clusters[$this->cluster]) as $key) {
                if ($this->clusters[$this->cluster][$key]['slave_of'] == $this->repair['master_server'] && $key != $this->repair['server_to_repair']) {
                    $this->repair['snapshot_server'] = $key;
                    break;
                }
            }

            if (!array_key_exists('snapshot_server', $this->repair)) {
                $this->repair['snapshot_server'] = $this->repair['master_server'];
            }
            if ($this->repair['snapshot_server'] != $this->repair['master_server']) {
                if (array_key_exists('active_master', $this->clusters[$this->cluster][$this->repair['snapshot_server']])) {
                    $this->repair['snapshot_server'] = $this->repair['master_server'];
                }
            }
        }
    }

    public function showOverview()
    {
        print "<p>Available MySQL clusters: ";

        foreach (array_keys($this->clusters) as $key) {
            printf(" <a href=%s?cluster=%s>$key</a> ", $_SERVER['PHP_SELF'], $key);
        }


        printf(
            "
            <table border=0>
            <thead>
            <tr>
            <td></td>
            <td colspan=%s class=border align=left style='border-bottom: solid 1px rgb(238, 238, 238);'><h2>%s</h2></td>
            </tr>
            <tr>
            <th></th>
            ",
            count($this->clusters[$this->cluster]),
            $this->cluster
        );

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            printf(
                '<th style="padding-top:5px" align="left"><span class="label" style="background-color: %s">%s</span> (%s)',
                $this->status[$key]->color,
                $key,
                $this->clusters[$this->cluster][$key]['ip']
            );
            if ($this->repair['server_to_repair'] != $key) {
                printf("<br><a href=%s?server=%s&cluster=%s>How to repair me</a></th>", $_SERVER['PHP_SELF'], urlencode($key), urlencode($this->cluster));
            } else {
                printf("<br><a href=%s?server=%s&cluster=%s>Repair instructions</a></th>", $_SERVER['PHP_SELF'], urlencode($key), urlencode($this->cluster));
            }
        }

        print "</tr></thead>
        ";

        print "<tr>
        <td></td>";

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            print '<td style="vertical-align: top">';
            print "<table class='table table-bordered table-condensed'>";

            if ($this->status[$key]->slave_sql_running == 'No') {
                $sql_color="red";
            } else {
                $sql_color="white";
            }

            if ($this->status[$key]->slave_io_running == 'No') {
                $io_color="red";
            } else {
                $io_color="white";
            }

            if (array_key_exists($key, $this->is_master)) {
                printf("<tr><td colspan=2 bgcolor=lightgrey><b>Master status</b></td></tr>");
                printf(
                    "<tr><td class=border>Log file</td><td><font color=%s>%s</font></td></tr>",
                    $this->status[$key]->color,
                    $this->status[$key]->master_log_file
                );
                printf("<tr><td class=border>Position</td><td>%s</td></tr>", $this->status[$key]->master_position);
            } else {
                if (!$this->isPartialSlave($key)) {
                    printf("<tr><td colspan=2><font color=lightgrey>Master status</font></td></tr>");
                    printf(
                        "<tr><td class=border><font color=lightgrey>Log file</font></td><td><font color=lightgrey>%s</font></td></tr>",
                        $this->status[$key]->master_log_file
                    );
                    printf(
                        "<tr><td class=border><font color=lightgrey>Position</font></td><td><font color=lightgrey>%s</font></td></tr>",
                        $this->status[$key]->master_position
                    );
                }
            }

            if (!$this->isPartialSlave($key)) {
                printf("<tr><td colspan=2 bgcolor=lightgrey><b>Slave of %s status</b></td></tr>", $this->status[$key]->slave_of);
            } else {
                printf("<tr><td colspan=2 bgcolor=lightgrey><b>Partial slave of %s status</b></td></tr>", $this->status[$key]->slave_of);
            }

            printf("<tr><td class=border>Master host</td><td>%s</td></tr>", $this->status[$key]->slave_master);
            printf("<tr><td class=border>Master port</td><td>%s</td></tr>", $this->status[$key]->slave_master_port);
            printf(
                "<tr><td class=border>Log file</td><td><font color=%s>%s</font></td></tr>",
                $this->status[$this->status[$key]->slave_of]->color,
                $this->status[$key]->slave_log_file
            );
            if ($this->status[$key]->using_gtid != '') {
                printf("<tr><td class=border>Using GTID</td><td>%s</td></tr>", $this->status[$key]->using_gtid);
                printf("<tr><td class=border>GTID IO Pos</td><td>%s</td></tr>", $this->status[$key]->gtid_io_pos);
            }
            if ($this->isPartialSlave($key)) {
                printf("<tr><td class=border>Replicate Wild Do Table</td><td>%s</td></tr>", $this->status[$key]->replicate_wild_table);
            }
            printf("<tr><td class=border>Position</td><td>%s</td></tr>", $this->status[$key]->slave_position);
            printf("<tr><td class=border>SQL thread</td><td bgcolor=%s>%s</td></tr>", $sql_color, $this->status[$key]->slave_sql_running);
            printf("<tr><td class=border>IO thread</td><td bgcolor=%s>%s</td></tr>", $io_color, $this->status[$key]->slave_io_running);
            printf("<tr><td class=border>Delay</td><td>%s</td></tr>", $this->status[$key]->slave_seconds_behind);
            printf("<tr><td class=border>Last error</td><td>%s</td></tr>", $this->status[$key]->slave_last_error);
            printf("<tr><td class=border>Last errno</td><td>%s</td></tr>", $this->status[$key]->slave_last_errno);

            print "</table>";
            print "</td>";
        }
        print "</tr>";

        $this->printInstructions();

        print "</table>";
    }

    private function isPartialSlave($key)
    {
        return $this->status[$key]->replicate_wild_table != '';
    }


    private function printStep($hostname, $instructions = '', $downtime = false)
    {
        $this->step++;
        print "
            <tr>
        <td>$this->step
        </td>";

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            if ($downtime && $key == $this->repair['master_server']) {
                $bgcolor="alert alert-error";
            }
            if ($key==$hostname) {
                print "<td><pre>$instructions</pre></td>";
            } else {
                if ($downtime && $key==$this->repair['master_server']) {
                    print "<td valign=middle align=center><p class='$bgcolor'><strong>Downtime</strong></p></td>";
                } else {
                    print "<td></td>";
                }
            }
        }
        print "</tr>";
    }


    private function printInstructions()
    {
        if (!$this->repair['server_to_repair']) {
            return;
        }

        $text = sprintf(
            "
The ssh public keys for user root must be shared
between machines. Test scp as root between machines.
Using %s to take snapshot.",
            $this->repair['snapshot_server']
        );
        $this->printStep($this->repair['master_server'], $text);

        if ($this->repair['snapshot_server'] != $this->repair['master_server']) {
            // lock master and wait for snapshot server to catch up with the master server
            $text = sprintf(
                "
mysql -u root -p

flush tables with read lock;

show master status;

# note file & position %s.file %s.pos
# do not exit the mysql console",
                $this->repair['master_server'],
                $this->repair['master_server']
            );
            $this->printStep($this->repair['master_server'], $text);

            $text = sprintf(
                "
mysql -u root -p

show slave status;

# wait until same position as %s

slave stop;",
                $this->repair['master_server']
            );
            $this->printStep($this->repair['snapshot_server'], $text, true);

            $text = "unlock tables;";
            $this->printStep($this->repair['master_server'], $text);
        } else {
            $text = sprintf(
            "
Initial copy of the mysql files in bulk, this will 
lower the downtime at next step.");
        $this->printStep($this->repair['master_server'], $text);

        $text = sprintf("rsync -avzP --progress --delete /var/lib/mysql %s:/var/lib/", $this->repair['server_to_repair']);
        $this->printStep($this->repair['snapshot_server'], $text);

            $text = sprintf(
                "
mysql -u root -p

slave stop;

show master status;
# note file & possition %s.file %s.pos",
                $this->repair['snapshot_server'],
                $this->repair['snapshot_server']
            );
            $this->printStep($this->repair['snapshot_server'], $text);
        }

        $text = "
/etc/init.d/monit stop

/etc/init.d/mysql stop";
        $this->printStep($this->repair['snapshot_server'], $text);

        $text = "
<font color=red><b>OUTAGE starts here.</b></font>";
        $this->printStep($this->repair['snapshot_server'], $text);

        $text = "
/etc/init.d/monit stop

/etc/init.d/mysql stop";
        $this->printStep($this->repair['server_to_repair'], $text);

        $text = sprintf("rsync -avzP --delete --progress /var/lib/mysql %s:/var/lib/", $this->repair['server_to_repair']);
        $this->printStep($this->repair['snapshot_server'], $text);

        $text = "
/etc/init.d/mysql start

/etc/init.d/monit start";
        $this->printStep($this->repair['snapshot_server'], $text);

        $text = "
<font color=green><b>OUTAGE ends here.</b></font>";
        $this->printStep($this->repair['snapshot_server'], $text);

        $text = sprintf(
            "
(cd /var/lib/mysql/; rm *.info *relay-bin*)

/etc/init.d/mysql start

/etc/init.d/monit start

mysql -u root -p

show master status;
# note %s.file %s.pos

CHANGE MASTER TO MASTER_HOST='%s',
MASTER_USER='%s',
MASTER_PASSWORD='%s',
MASTER_LOG_FILE='%s.file',
MASTER_LOG_POS=%s.pos;

slave start;",
            $this->repair['server_to_repair'],
            $this->repair['server_to_repair'],
            $this->clusters[$this->cluster][$this->repair['master_server']]['ip'],
            $this->clusters[$this->cluster][$this->repair['master_server']]['replication_user'],
            $this->clusters[$this->cluster][$this->repair['master_server']]['replication_password'],
            $this->repair['master_server'],
            $this->repair['master_server']
        );
        $this->printStep($this->repair['server_to_repair'], $text);

        $text=sprintf(
            "
mysql -u root -p

stop slave;

CHANGE MASTER TO MASTER_HOST='%s',
MASTER_USER='%s',
MASTER_PASSWORD='%s',
MASTER_LOG_FILE='%s.file',
MASTER_LOG_POS=%s.pos;

slave start;",
            $this->clusters[$this->cluster][$this->repair['server_to_repair']]['ip'],
            $this->clusters[$this->cluster][$this->repair['server_to_repair']]['replication_user'],
            $this->clusters[$this->cluster][$this->repair['server_to_repair']]['replication_password'],
            $this->repair['server_to_repair'],
            $this->repair['server_to_repair']
        );
        if ($this->clusters[$this->cluster][$this->repair['master_server']]['slave_of'] == $this->repair['server_to_repair']) {
            $this->printStep($this->repair['master_server'], $text);
        }
    }
}
?>
