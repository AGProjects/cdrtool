#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");

system('clear');

if ($CDRTool['replicated_databases']) {
    print "\nPlease migrate global.inc setting \$CDRTool['replicated_databases'] to \$CDRTool['mysql_clusters']. See setup/global.inc.in for example\n\n";
    exit;
}

$slaveStatusQuery  = "show slave status";
$masterStatusQuery = "show master status";
$replicationStatus = array();

$clusters=array_keys($CDRTool['mysql_clusters']);

foreach ($clusters as $_cluster) {
    $replicationStatus=array();
    
    $title= "\nMySQL cluster: $_cluster\n\n";
    print $title;

    $databases=$CDRTool['mysql_clusters'][$_cluster];
    
    foreach ($databases as $_database) {
        if (!class_exists($_database)) continue;
        $_db = new $_database;
    
        if ($_db->query($slaveStatusQuery)) {
            $_db->next_record();
            $replicationStatus[$_db->Host]['slave_status']=array(
                'Master_Host'	   => $_db->f('Master_Host'),
                'Master_User'	   => $_db->f('Master_User'),
                'Master_Port'	   => $_db->f('Master_Port'),
                'Log_File'	   => $_db->f('Master_Log_File'),
                'Position'    	   => $_db->f('Read_Master_Log_Pos'),
                'Slave_SQL_Running'=> $_db->f('Slave_SQL_Running'),
                'Slave_IO_Running' => $_db->f('Slave_IO_Running'),
                'Last_errno'	   => $_db->f('Last_Errno'),
                'Last_error'	   => $_db->f('Last_Error'),
                'Skip_counter'	   => $_db->f('Skip_counter'),
                'Connection'       => array('Host'     => $_db->Host,
                                            'User'     => $_db->User,
                                            'Password' => $_db->Password
                                           )
                );
    
        } else {
            printf ("Error for query '%s' on server %s: %s(%s)\n",$slaveStatusQuery,$_database,$_db->Error,$_db->Errno);
            $replicationStatus[$_db->Host]['slave_status']=array();
            break;
        }
    
        if ($_db->query($masterStatusQuery)) {
            $_db->next_record();
            $replicationStatus[$_db->Host]['master_status']=array(
                'Position'	=> $_db->f('Position'),
                'Log_File'	=> $_db->f('File')
                );
        } else {
            printf ("Error for query '%s' on server %s: %s(%s)\n",$masterStatusQuery,$_database,$_db->Error,$_db->Errno);
            $replicationStatus[$_db->Host]['master_status']=array();
            break;
        }
    }
    
    //print_r($replicationStatus);
    
    $j=0;
    foreach (array_keys($replicationStatus) as $_db) {
    
        if (count($replicationStatus[$_db]['slave_status'])) {
            if ($j) print "\n";
            $title="Replication status of slave server $_db\n";
            print $title;
            $t=1;
            while ($t < strlen($title)) {
                print "=";
                $t++;
            }
            print "\n";
            if ($replicationStatus[$_db]['slave_status']['Slave_SQL_Running'] == 'Yes' &&
                $replicationStatus[$_db]['slave_status']['Slave_IO_Running'] == 'Yes' &&
                $replicationStatus[$_db]['slave_status']['Log_File'] == $replicationStatus[$replicationStatus[$_db]['slave_status']['Master_Host']]['master_status']['Log_File']
                ) {
                printf ("Master %s ----> Slave %s, logfile %s:%s, replication OK\n",$replicationStatus[$_db]['slave_status']['Master_Host'],$_db,$replicationStatus[$_db]['slave_status']['Log_File'],$replicationStatus[$_db]['slave_status']['Position']);
            } else {
                printf ("Master %s ----> Slave %s, replication failed. \n\nThe replication parameters that failed are:\n\n",$replicationStatus[$_db]['slave_status']['Master_Host'],$_db);
    
                if (!$replicationStatus[$replicationStatus[$_db]['slave_status']['Master_Host']]['master_status']) { 
                  printf ("Error: server %s is not defined. Please use the same IPs or hostnames used during replication setup.\n\n",$replicationStatus[$_db]['slave_status']['Master_Host']);
                  continue;
                }
                
                if ($replicationStatus[$_db]['slave_status']['Slave_SQL_Running'] != 'Yes') {
                    printf ("%s = %s\n",'Slave_SQL_Running',$replicationStatus[$_db]['slave_status']['Slave_SQL_Running']);
                }
    
                if ($replicationStatus[$_db]['slave_status']['Slave_IO_Running'] != 'Yes') {
                    printf ("%s = %s\n",'Slave_IO_Running',$replicationStatus[$_db]['slave_status']['Slave_IO_Running']);
                }
    
                if ($replicationStatus[$_db]['slave_status']['Log_File'] != $replicationStatus[$replicationStatus[$_db]['slave_status']['Master_Host']]['master_status']['Log_File']) {
                    printf ("Master log file %s != slave log file %s\n",$replicationStatus[$replicationStatus[$_db]['slave_status']['Master_Host']]['master_status']['Log_File'],$replicationStatus[$_db]['slave_status']['Log_File']);
                }
    
                if ($replicationStatus[$_db]['slave_status']['Position'] != $replicationStatus[$replicationStatus[$_db]['slave_status']['Master_Host']]['master_status']['Position']) {
                    printf ("Master log position %s != slave log position %s\n",$replicationStatus[$replicationStatus[$_db]['slave_status']['Master_Host']]['master_status']['Position'],$replicationStatus[$_db]['slave_status']['Position']);
                }
    
                printf ("\nTo restart the replication process run this command on %s: \n\n",$replicationStatus[$_db]['slave_status']['Connection']['Host']);
                printf ("CHANGE MASTER to \nMASTER_HOST     = '%s', \nMASTER_USER     = '%s', \nMASTER_PASSWORD = '%s', \nMASTER_LOG_FILE = '%s', \nMASTER_LOG_POS  = %s;\n",
                $replicationStatus[$_db]['slave_status']['Master_Host'],
                $replicationStatus[$_db]['slave_status']['Connection']['User'],
                $replicationStatus[$_db]['slave_status']['Connection']['Password'],
                $replicationStatus[$replicationStatus[$_db]['slave_status']['Master_Host']]['master_status']['Log_File'],
                $replicationStatus[$replicationStatus[$_db]['slave_status']['Master_Host']]['master_status']['Position']
                );
            }
            $j++;
        }
    }
}

?>
