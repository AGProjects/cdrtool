<?
// This library can be used to display the status of mysql replication of
// arbitrary number of mysql servers and instructions about how to fix
// a broken replication process

class DBx extends DB_Sql {
  function DBx($host='localhost', $user, $password) {
      $this->Host = $host;
      $this->User = $user;
      $this->Password = $password;
      $this->Database = 'mysql';
      parent::DB_Sql();
  }
}

class MySQLReplicationStatus {
	var $slave_status_query="show slave status";
    var $master_status_query="show master status";

    function MySQLReplicationStatus($host,$clusters) {

        $db = new DBx ($clusters[$host]['ip'],$clusters[$host]['user'],$clusters[$host]['password']);

        if (!$db->query($this->slave_status_query)) {
            printf ("<p><font color=red>Error from MySQL server %s: %s (%s) for query: %s</font>",$clusters[$host]['ip'],$db->Error,$db->Errno,$this->slave_status_query);
            return false;
        }

		$db->next_record();

        $this->slave_master         = $db->f('Master_Host');
        $this->slave_user           = $db->f('Master_User');
        $this->slave_master_port    = $db->f('Master_Port');
        $this->slave_log_file	    = $db->f('Master_Log_File');
        $this->slave_position       = $db->f('Read_Master_Log_Pos');
        $this->slave_sql_running	= $db->f('Slave_SQL_Running');
        $this->slave_io_running	    = $db->f('Slave_IO_Running');
        $this->slave_last_errno	    = $db->f('Last_Errno');
        $this->slave_last_error 	= $db->f('Last_Error');
        $this->slave_seconds_behind = $db->f('Seconds_Behind_Master');

        if (!$db->query($this->master_status_query)) {
            printf ("<p><font color=red>MySQL error: %s (%s) for query: %s</font>",$db->Error,$db->Errno,$this->slave_status_query);
            return;
        }

		$db->next_record();

    	$this->master_position	    = $db->f('Position');
    	$this->master_log_file	    = $db->f('File');
        $this->slave_of     	    = $clusters[$host]['slave_of'];
        $this->color         	    = $clusters[$host]['color'];
    }
}

class ReplicationOverview {
    var $status=array();

    function ReplicationOverview($clusters=array()) {
    	$this->clusters = $clusters;

        $this->cluster = $_REQUEST['cluster'];
        $this->repair['server_to_repair']  = $_REQUEST['server'];

		$cluster_names=array_keys($this->clusters);

        if (!$this->cluster) $this->cluster = $cluster_names[0];

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            $this->status[$key]= new MySQLReplicationStatus($key,$this->clusters[$this->cluster]);
        }

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
        	foreach (array_keys($this->clusters[$this->cluster]) as $key2) {
                if ($key == $key2) continue;
                if ($this->clusters[$this->cluster][$key2]['slave_of'] == $key) {
                    $this->is_master[$key]++;
                }
            }
        }

      	if ($this->repair['server_to_repair']) {

    		$this->repair['master_server']=$this->clusters[$this->cluster][$this->repair['server_to_repair']]['slave_of'];

            foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            	if ($this->repair['server_to_repair'] == $this->clusters[$this->cluster][$key]['slave_of']) {
                	$this->repair['has_slave']= $key;
                    break;
                }
            }

            foreach (array_keys($this->clusters[$this->cluster]) as $key) {
                if ($this->clusters[$this->cluster][$key]['slave_of'] == $this->repair['master_server'] && $key != $this->repair['server_to_repair']) {
                    $this->repair['snapshot_server'] = $key;
                    break;
                }
            }

            if (!$this->repair['snapshot_server']) {
                $this->repair['snapshot_server']=$this->repair['master_server'];
            }

			if ($this->repair['snapshot_server'] != $this->repair['master_server']) {
                if ($this->clusters[$this->cluster][$this->repair['snapshot_server']]['active_master']) {
                	$this->repair['snapshot_server'] = $this->repair['master_server'];
                }
            }

        }

    }

    function showOverview() {

        print "<p>
        Available MySQL clusters: ";

        foreach (array_keys($this->clusters) as $key) {
            printf (" <a href=%s?cluster=%s>$key</a> ",$_SERVER['PHP_SELF'],$key);
        }
        

        printf ("
        <table border=0>
        <tr>
        <td></td>
        <td colspan=%s class=border align=center><h1>%s</h1></td>
        </tr>
        <tr>
        <th></th>
        ",
        count($this->clusters[$this->cluster]),
        $this->cluster
        );

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            printf ("<th class=border>%s (%s)",$key,$this->clusters[$this->cluster][$key]['ip']);
            if ($this->repair['server_to_repair'] != $key) {
            	printf ("<br><a href=%s?server=%s&cluster=%s>How to repair me</a></th>",$_SERVER['PHP_SELF'],urlencode($key),urlencode($this->cluster));
            } else {
            	printf ("<br><a href=%s?server=%s&cluster=%s>Repair instructions</a></th>",$_SERVER['PHP_SELF'],urlencode($key),urlencode($this->cluster));
            }
        }
        
        print "</tr>
        ";

        print "<tr>
        <td></td>";
        
        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            print "<td class=border valign=top>";
        
            print "<table border=1>";

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

			if ($this->is_master[$key]) {
            	printf ("<tr><td colspan=2 bgcolor=lightgrey><b>Master status</b></td></tr>");
                printf ("<tr><td class=border>Log file</td><td><font color=%s>%s</font></td></tr>",$this->status[$key]->color,$this->status[$key]->master_log_file);
                printf ("<tr><td class=border>Position</td><td>%s</td></tr>",$this->status[$key]->master_position);
            } else {
            	printf ("<tr><td colspan=2><font color=lightgrey>Master status</font></td></tr>");
                printf ("<tr><td class=border><font color=lightgrey>Log file</font></td><td><font color=lightgrey>%s</font></td></tr>",$this->status[$key]->master_log_file);
                printf ("<tr><td class=border><font color=lightgrey>Position</font></td><td><font color=lightgrey>%s</font></td></tr>",$this->status[$key]->master_position);
            }

            printf ("<tr><td colspan=2 bgcolor=lightgrey><b>Slave of %s status</b></td></tr>",$this->status[$key]->slave_of);

            printf ("<tr><td class=border>Master host</td><td>%s</td></tr>",$this->status[$key]->slave_master);
            printf ("<tr><td class=border>Master port</td><td>%s</td></tr>",$this->status[$key]->slave_master_port);
            printf ("<tr><td class=border>Log file</td><td><font color=%s>%s</font></td></tr>",$this->status[$this->status[$key]->slave_of]->color,$this->status[$key]->slave_log_file);
            printf ("<tr><td class=border>Position</td><td>%s</td></tr>",$this->status[$key]->slave_position);
            printf ("<tr><td class=border>SQL thread</td><td bgcolor=%s>%s</td></tr>",$sql_color,$this->status[$key]->slave_sql_running);
            printf ("<tr><td class=border>IO thread</td><td bgcolor=%s>%s</td></tr>",$io_color,$this->status[$key]->slave_io_running);
            printf ("<tr><td class=border>Delay</td><td>%s</td></tr>",$this->status[$key]->slave_seconds_behind);
            printf ("<tr><td class=border>Last error</td><td>%s</td></tr>",$this->status[$key]->slave_last_error);
            printf ("<tr><td class=border>Last errno</td><td>%s</td></tr>",$this->status[$key]->slave_last_errno);

            print "</table>
            ";
        
            print "</td>";
        }
        
        print "</tr>";

 		$this->printInstructions();

        print "
        </table>
        ";
 }

    function printStep ($hostname,$instructions='',$downtime=false) {
        $this->step++;
        print "<tr>
        <td>$this->step
        </td>";

        foreach (array_keys($this->clusters[$this->cluster]) as $key) {
            if ($downtime && $key==$this->repair['master_server']) {
                $bgcolor="red";
            } else {
                $bgcolor="white";
            }
            if ($key==$hostname) {
                print "<td class=border bgcolor=$bgcolor><pre>$instructions</pre></td>";
            } else {
            	if ($downtime && $key==$this->repair['master_server']) {
                	print "<td class=border bgcolor=$bgcolor valign=middle align=center><b><font color=white>Downtime</font></b></td>";
                } else {
                	print "<td class=border></td>";
                }
            }
        }
        print "</tr>";

    }


    function printInstructions() {

    if (!$this->repair['server_to_repair']) return;

$text="The ssh public keys for user root must be shared <br>between machines. Test scp as root between machines. ";
$text.=sprintf("<br>Using %s to take snapshot.",$this->repair['snapshot_server']);

$this->printStep($this->repair['master_server'],$text);

    if ($this->repair['snapshot_server'] != $this->repair['master_server']) {
        // lock master and wait for snapshot server to catch up with the master server
$text=sprintf("
mysql -u root -p

flush tables with read lock;

show master status;

# note file & possition %s.file %s.pos
<font color=red># do not exit the mysql console</font>

",$this->repair['master_server'],$this->repair['master_server']);

$this->printStep($this->repair['master_server'],$text);

$text=sprintf("

mysql -u root -p

show slave status;
# wait until same possition as %s

slave stop;
",$this->repair['master_server']);

$this->printStep($this->repair['snapshot_server'],$text,true);

$text="unlock tables;";

$this->printStep($this->repair['master_server'],$text);


    } else {
$text=sprintf("

mysql -u root -p

slave stop;

show master status;
# note file & possition %s.file %s.pos
",$this->repair['snapshot_server'],
$this->repair['snapshot_server']
);

$this->printStep($this->repair['snapshot_server'],$text);

    }


$text="

/etc/init.d/monit stop

/etc/init.d/mysql stop

";

$this->printStep($this->repair['snapshot_server'],$text);

$text="

/etc/init.d/monit stop

/etc/init.d/mysql stop

";

$this->printStep($this->repair['server_to_repair'],$text);

$text=sprintf("
rsync -avzP --delete /var/lib/mysql %s:/var/lib/

",$this->repair['server_to_repair']);

$this->printStep($this->repair['snapshot_server'],$text);

$text=sprintf("

/etc/init.d/mysql start

/etc/init.d/monit start
",$this->repair['server_to_repair']);

$this->printStep($this->repair['snapshot_server'],$text);

$text=sprintf("
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

slave start;

",$this->repair['server_to_repair'],
$this->repair['server_to_repair'],
$this->clusters[$this->cluster][$this->repair['master_server']]['ip'],
$this->clusters[$this->cluster][$this->repair['master_server']]['replication_user'],
$this->clusters[$this->cluster][$this->repair['master_server']]['replication_password'],
$this->repair['master_server'],
$this->repair['master_server']
);
$this->printStep($this->repair['server_to_repair'],$text);

$text=sprintf("

mysql -u root -p

stop slave;

CHANGE MASTER TO MASTER_HOST='%s',
MASTER_USER='%s',
MASTER_PASSWORD='%s',
MASTER_LOG_FILE='%s.file',
MASTER_LOG_POS=%s.pos;

slave start;
",
$this->clusters[$this->cluster][$this->repair['server_to_repair']]['ip'],
$this->clusters[$this->cluster][$this->repair['server_to_repair']]['replication_user'],
$this->clusters[$this->cluster][$this->repair['server_to_repair']]['replication_password'],
$this->repair['server_to_repair'],
$this->repair['server_to_repair']
);

if ($this->clusters[$this->cluster][$this->repair['master_server']]['slave_of'] == $this->repair['server_to_repair']) {
	$this->printStep($this->repair['master_server'],$text);
}

}

}
?>
