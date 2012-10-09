<?
class PrepaidCards {
	var $whereResellerFilter= " (1=1) ";

    function PrepaidCards () {
        global $auth;
        global $CDRTool;
        $this->loginname = $auth->auth["uname"];
        $this->CDRTool   = $CDRTool;
		$this->db        = new DB_CDRTool;
		$this->db1       = new DB_CDRTool;

        if ($this->CDRTool['filter']['reseller']) {
        	$this->whereResellerFilter = sprintf ("reseller_id = %d",$this->CDRTool['filter']['reseller']);
        }
    }

    function showGenerateForm() {

        $this->PHP_SELF = $_SERVER['PHP_SELF'];

        print "<form action=$this->PHP_SELF method=posti class='form-horizontal'>
            <div class=control-group>
                <label class=control-label>
                Batch name
                </label>
                <div class=controls>
                    <input type=text name=batch>
                </div>
            </div>

            <div class=control-group>
                <label class=control-label>
                Begins with
                </label>
                <div class=controls>
                    <input type=text name=start>
                </div>
            </div>
        
            <div class=control-group>
                <label class=control-label>
                    Number of digits
                </label>
                <div class=controls>
            
            <select name=digits>
            	<option>11
            	<option>12
            	<option>13
            	<option>14
            	<option>15
            </select>
                </div>
            </div>
            
        
            <div class=control-group>
                <label class=control-label>
                    Number of cards
                </label>
                <div class=controls>
            <input type=text size=6 name=nr_cards value=100>
                </div>
            </div>
        
            <div class=control-group>
                <label class=control-label>
                    Value
                </label>
                <div class=controls>
            <input type=text size=6 name=value value=10>
                </div>
            </div>
        
            <div class=control-group>
                <label class=control-label>
                    Reseller
                </label>
                <div class=controls>
            <input type=text size=6 name=reseller value=>
                </div>
            </div>
        
            <div class='form-actions'> 
                <input class='btn btn-warning' type=submit value=Generate>
            </div>
        
            <input type=hidden name=action value=generate>
        </form>
        ";
    }
    
    function generate() {
		$start    = $_REQUEST['start'];
        $nr_cards = $_REQUEST['nr_cards'];
        $digits   = $_REQUEST['digits'];
        $batch    = $_REQUEST['batch'];
        $value    = $_REQUEST['value'];

        if ($this->CDRTool['filter']['reseller']) {
            $reseller=$this->CDRTool['filter']['reseller'];
        } else {
        	$reseller = $_REQUEST['reseller'];
        }

        if (!$digits) {
            print "<p>Error: No digits specified!";
            return 0;
        }

        $card_len=$digits;

        if (!$nr_cards) $nr_cards = 10;
        if (!$value)    $value    = 10;
    
        $alf=array("0","1","2","3","4","5","6","7","8","9");

        print "<p>";
        printf ("Request generation of %d cards or %d digits in value of %s",$nr_cards,$card_len,$value);
    
        $now = date("Y-m-d_H:i:s");

        if (!$batch) {
            $batch_name=$now."-cards_".$nr_cards."-value_".$value;
        } else {
            $batch_name=$now."-".$batch."-cards_".$nr_cards."-value_".$value;
        }

        $generated   = 0;
        $failed		 = 0;
        $j   		 = 0;
        $random_len  = $card_len-strlen($start);
        $initial_len = strlen($start);

        while ($generated < $nr_cards) {
            $j++;

            if ($j > 5 * $nr_cards) {
                print "Error: could not generate $nr_cards unique random cards";
                break;
            }

            $card=$start;
            $i=0;

            while($i < $random_len) {
                srand((double)microtime()*1000000);
                if ($i==0) {
                    $randval = rand(1,9);
                } else {
                    $randval = rand(0,9);
                }
                $card=$card.$alf[$randval];
                $i++;
            }

            $query=sprintf("insert into prepaid_cards
            (number,value,date_batch,batch,service,reseller_id)
            values ('%s','%s',NOW(),'%s','sip',%d)",

            addslashes($card),
            addslashes($value),
            addslashes($batch_name),
            intval($reseller)
            );

            dprint($query);

            if ($this->db->query($query) && $this->db->affected_rows()) {
            	$generated++;
            } else {
                $failed++;
            }
        }

        if ($generated) {
            print "<p><font color=blue>Generated $generated cards</font>";

            $log_query=sprintf("insert into log
            (date,login,ip,datasource,results,description,reseller_id)
            values
            (NOW(),'%s','%s','Prepaid generator','%d','Batch %s created',%d)",
            addslashes($this->loginname),
            addslashes($_SERVER['REMOTE_ADDR']),
            addslashes($generated),
            addslashes($batch_name),
            intval($reseller)
            );
 
            dprint($log_query);
            $this->db->query($log_query);
        }
    }

    function export($batch) {

        if (!$batch) return false;

		$available  = $_REQUEST['available'];

    	$query = sprintf("select * from prepaid_cards where batch = '%s' and %s",addslashes($batch),$this->whereResellerFilter);

        if ($available == "yes") {
            $query .= " and value  > 0";
            $query .= " order by id ASC ";

        } elseif ($available == "no") {
            $query .= " and value  = 0";
            $query .= " order by date_active DESC ";
        }

        dprint($query);

        print "Id,Number,Batch,Value\n";

        $this->db->query($query);

		$rows= $this->db->num_rows();

        while ($this->db->next_record()) {
            $i++;
            $id          = $this->db->f('id');
            $batch       = $this->db->f('batch');
            $number      = $this->db->f('number');
            $value       = $this->db->f('value');
            $date_active = $this->db->f('date_active');

            print "$id,$number,$batch,$value\n";
        }

        $log_query=sprintf("insert into log
        (date,login,ip,datasource,results,description,reseller_id)
        values
        (NOW(),'%s','%s','Prepaid generator','%d','Batch %s exported',%d)",
        addslashes($this->loginname),
        addslashes($_SERVER['REMOTE_ADDR']),
        addslashes($rows),
        addslashes($batch),
        $this->CDRTool['filter']['reseller']
        );

        dprint($log_query);
        $this->db->query($log_query);

	}

    function deleteBatch($batch, $confirm) {

        if (!$batch) return false;

        if (!$confirm) {
            $batch_enc=urlencode($batch);
            print "
            <p>
            <a href=$this->PHP_SELF?batch=$batch_enc&action=delete&confirm=1>Confirm</a> <font color=red>deletion of batch $batch</font>
            <p>
            ";
            return;
        }

    	$query=sprintf("delete from prepaid_cards where batch = '%s' and %s",addslashes($batch),$this->whereResellerFilter);
        $this->db->query($query);

        if ($this->db->affected_rows()) {
            $log_query=sprintf("insert into log
            (date,login,ip,datasource,results,description,reseller_id)
            values
            (NOW(),'%s','%s','Prepaid generator','%d','Batch %s deleted',%d)",
            addslashes($this->loginname),
            addslashes($_SERVER['REMOTE_ADDR']),
            addslashes($this->db->affected_rows()),
            addslashes($batch),
            $this->CDRTool['filter']['reseller']
            );
    
            dprint($log_query);
            $this->db->query($log_query);
            return true;

        }

        return false;
    }

    function blockBatch($batch) {

        if (!$batch) return false;

    	$query=sprintf("update prepaid_cards set blocked = '1' where batch = '%s' and %s",addslashes($batch),$this->whereResellerFilter);
        $this->db->query($query);
    }

    function deblockBatch($batch) {

        if (!$batch) return false;

    	$query=sprintf("update prepaid_cards set blocked = '0' where batch = '%s' and %s",addslashes($batch),$this->whereResellerFilter);
        $this->db->query($query);
    }

    function showBatches () {

        $query=sprintf("select count(*) as c,batch,reseller_id,date_batch
        from prepaid_cards
        where %s
        group by batch
        order by date_batch DESC",$this->whereResellerFilter);
        dprint($query);

        $this->db->query($query);
        print "<table class='table table-condesed table-striped table-bordered'>";

        print "
            <thead>
            <tr>
        <th>Reseller</th>
        <th>Cards</th>
        <th>Date</th>
        <th>Batch</th>
        <th colspan=3>Download</th>
        <th>Actions</th>
        </tr>
        </thead>
        ";

        while ($this->db->next_record()) {
            $date=$this->db->f('date_batch');
            $c=$this->db->f('c');
            $reseller_id=$this->db->f('reseller_id');
            $batch=$this->db->f('batch');
            $batch_enc=urlencode($batch);

            $query=sprintf("select count(*) as c from prepaid_cards
            where batch = '%s' and value = '0' and %s",addslashes($batch),$this->whereResellerFilter);
            dprint($query);

            $this->db1->query($query);
            $this->db1->next_record();
            $used=$this->db1->f('c');
    
            $query=sprintf("select count(*) as c from prepaid_cards
            where batch = '%s' and value <> '0' and %s",addslashes($batch),$this->whereResellerFilter);
            dprint($query);

            $this->db1->query($query);
            $this->db1->next_record();

            $unused=$this->db1->f('c');

            print "
            <tr>
            <td>$reseller_id</td>
            <td>$c</td>
            <td>$date</td>
            <td>$batch</td>
            <td><a href=$this->PHP_SELF?batch=$batch_enc&action=export target=_new>All cards</a></td>
            <td><a href=$this->PHP_SELF?batch=$batch_enc&action=export&available=yes target=_new>Available cards ($unused)</a></td>
            <td><a href=$this->PHP_SELF?batch=$batch_enc&action=export&available=no target=_new>Used cards ($used)</a></td>
            <td><a href=$this->PHP_SELF?batch=$batch_enc&action=delete>Delete</a></td>
            </tr>
            ";
        }
        print "</table>";
    }
}

?>
