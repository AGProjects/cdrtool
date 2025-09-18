<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998,1999 SH Online Dienst GmbH
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: db_mysql.inc,v 1.4 2006-05-12 11:38:39 adigeo Exp $
 *
 */

class DB_Sql
{

    /* public: connection parameters */
    public $Host     = "";
    public $Database = "";
    public $User     = "";
    public $Password = "";

    /* public: configuration parameters */
    public $Auto_Free     = 0;     ## Set to 1 for automatic mysql_free_result()
    public $Debug         = 0;     ## Set to 1 for debugging messages.
    public $Halt_On_Error = "no"; ## "report" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
    public $Seq_Table     = "db_sequence";

    /* public: result array and current row number */
    public $Record   = array();
    public $Row;

    /* public: current error number and error text */
    public $Errno    = 0;
    public $Error    = "";

    /* public: this is an api revision, not a CVS revision. */
    public $type     = "mysqli";
    public $revision = "1.2";

    /* private: link and query handles */
    private $Link_ID  = 0;
    private $Query_ID = 0;
    private $Stmt_ID = null;


    public $type_mappings = array(
        "int" => array(
            "int",
            "tinyint",
            "smallint",
            "mediumint",
            "bigint"
        ),
        "string" => array(
            "char",
            "varchar",
            "enum"
        ),
        "blob" => array(
            "longblob",
            "mediumblob",
            "text"
        ),
        "real" => array(
            "decimal",
            "double"
        )
    );

    public $flag_mappings = array(
        "not_null" => array(
            "NO",
        ),
        "primary_key" => array(
            "PRI",
        ),
        "multiple_key" => array(
            "MUL"
        ),
        "unique_key" => array(
            "UNI"
        ),
        "binary" => array(
            "timestamp",
            "date",
            "datetime"
        ),
        "blob binary" => array(
            "longblob",
            "mediumblob",
            "blob"
        ),
        "blob" => array(
            "text"
        ),
        "unsigned zerofill" => array(
            "0000-00-00 00:00:00",
            "CURRENT_TIMESTAMP"
        ),
        "enum" => array(
            "enum"
        )
    );

    public $length_mappings = array(
        16777215 => array("mediumblob"),
        65535    => array("text","blob"),
        22       => array("double"),
        19       => array("timestamp","datetime"),
        10       => array("date")
    );

    public $extra_mappings = array(
        "unsigned zerofill" => array("on update CURRENT_TIMESTAMP")
    );

    private function remap($arr)
    {
        $ret = array();
        foreach ($arr as $key => $value) {
            foreach ($value as $name) {
                $ret[$name] = $key;
            }
        }
        return $ret;
    }

    /* public: constructor */
    public function __construct($query = "")
    {
        $this->query($query);
    }

    /* public: some trivial reporting */
    public function link_id()
    {
        return is_int($this->Link_ID) ? 0 : mysqli_thread_id($this->Link_ID);
    }

    public function query_id()
    {
        return $this->Query_ID;
    }

    /* public: connection management */
    public function connect($Database = "", $Host = "", $User = "", $Password = "")
    {
        /* Handle defaults */
        if ("" == $Database) {
            $Database = $this->Database;
        }
        if ("" == $Host) {
            $Host     = $this->Host;
        }
        if ("" == $User) {
            $User     = $this->User;
        }
        if ("" == $Password) {
            $Password = $this->Password;
        }

        /* establish connection, select database */
        if (0 == $this->link_id()) {
            mysqli_report(MYSQLI_REPORT_OFF);
            $link = mysqli_init();
            if (!$link) {
                die('mysqli_init failed');
            }

            if (!mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 2)) {
                die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
            }

            if (!mysqli_real_connect($link, "p:".$Host, $User, $Password)) {
                syslog(LOG_NOTICE, "Error in connect(): connect as $User to $Host failed");

                $this->halt("connect as $User to $Host failed.");
                return 0;
            }
            $this->Link_ID = $link;
            debug("Connected to mysql with $User to $Host ".$this->link_id());
            // print "Connected to mysql with $User to $Host ".$this->link_id();

            if (!((bool)mysqli_query($this->Link_ID, "USE " . $Database))) {
                syslog(LOG_NOTICE, "Error in connect(): cannot use database");
                $this->halt("cannot use database ".$this->Database);
                return 0;
            }
        }
        return $this->Link_ID;
    }

    /* public: discard the query result */
    public function free()
    {
        if (isset($this->Query_ID) && is_resource($this->Query_ID)) { 
            @mysqli_free_result($this->Query_ID);
            $this->Query_ID = 0;
        }
    }

    /* public: perform a query */
    public function query($Query_String, $result_mode=MYSQLI_STORE_RESULT)
    {
        /* No empty queries, please, since PHP4 chokes on them. */
        if ($Query_String == "") {
            /* The empty query string is passed on from the constructor,
             * when calling the class without a query, e.g. in situations
             * like these: '$db = new DB_Sql_Subclass;'
             */
            return 0;
        }

        if (!$this->connect()) {
            return 0; /* we already complained in connect() about that. */
        };

        # New query, discard previous result.
        if ($this->Query_ID) {
            $this->free();
        }

        try {
            $this->Query_ID = @mysqli_query($this->Link_ID, $Query_String, $result_mode);
            $this->Row   = 0;
            $this->Errno = mysqli_errno($this->Link_ID);
            $this->Error = mysqli_error($this->Link_ID);
            if ($this->Errno) {
                throw new Exception($this->Errno);
            }
        } catch (Exception $e) {
            /*
                2006= CR_SERVER_GONE_ERROR
                2013= CR_SERVER_LOST
            */
            $dbError = $e->getMessage();
            if ($dbError=='2006' || $dbError=='2013') {
                warningAndPrint(sprintf("Warning: mysql connection %s lost with code %s", $this->link_id(), $dbError));

                if ($this->Query_ID) {
                    $this->free();
                }

                mysqli_close($this->Link_ID);

                $this->Link_ID = 0;

                if (!$this->connect()) {
                    error("Error: failed to re-connect to the MySQL server");
                    return 0;
                }

                noticeAndPrint(sprintf("Mysql connection re-created with %s\n", $this->link_id()));

                $this->Query_ID = @mysqli_query($this->Link_ID, $Query_String);
                $this->Row   = 0;
                $this->Errno = mysqli_errno($this->Link_ID);
                $this->Error = mysqli_error($this->Link_ID);
            } else {
                $this->halt("Invalid SQL: ".$Query_String);
            }
        }
        # Will return nada if it fails. That's fine.
        return $this->Query_ID;
    }

    /* public: walk result set */
    public function next_record()
    {
        if (!$this->Query_ID) {
            $this->halt("next_record called with no query pending.");
            return 0;
        }

        $this->Record = @mysqli_fetch_assoc($this->Query_ID);
        $this->Errno  = mysqli_errno($this->Link_ID);
        $this->Error  = mysqli_error($this->Link_ID);
        $this->Row   += 1;
        $stat = is_array($this->Record);
        if (!$stat && $this->Auto_Free) {
            $this->free();
        }
        return $stat;
    }

    /* public: position in result set */
    public function seek($pos = 0)
    {
        $status = @mysqli_data_seek($this->Query_ID, $pos);
        if ($status) {
            $this->Row = $pos;
        } else {
            $this->halt("seek($pos) failed: result has ".$this->num_rows()." rows");

            /* half assed attempt to save the day,
             * but do not consider this documented or even
             * desireable behaviour.
             */
            @mysqli_data_seek($this->Query_ID, $this->num_rows());
            $this->Row = $this->num_rows;
            return 0;
        }

        return 1;
    }

    public function prepare($query)
    {
        if (!$this->Link_ID) {
            $this->connect();  // Ensure connection is established
        }

        $this->Stmt_ID = mysqli_prepare($this->Link_ID, $query);

        if (!$this->Stmt_ID) {
            $this->halt("Prepare failed: " . mysqli_error($this->Link_ID));
            return false;
        }

        return true;
    }

    public function execute($params = array())
    {
        if (!$this->Stmt_ID) {
            $this->halt("Execute called without a prepared statement.");
            return false;
        }

        if (!empty($params)) {
            // Build type string and array of references
            $types = '';
            $refs = [];

            foreach ($params as $key => $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } elseif (is_string($value)) {
                    $types .= 's';
                } else {
                    $types .= 'b'; // Default to blob
                }

                $refs[$key] = &$params[$key]; // Create reference
            }

            array_unshift($refs, $types);
            call_user_func_array(array($this->Stmt_ID, 'bind_param'), $refs);
        }

        if (!mysqli_stmt_execute($this->Stmt_ID)) {
            $this->halt("Execute failed: " . mysqli_stmt_error($this->Stmt_ID));
            return false;
        }

        // Save result if it's a SELECT
        $result = mysqli_stmt_get_result($this->Stmt_ID);
        if ($result !== false) {
            $this->Query_ID = $result;
        }

        return true;
    }

    public function closeStmt()
    {
        if ($this->Stmt_ID) {
            mysqli_stmt_close($this->Stmt_ID);
            $this->Stmt_ID = null;
        }
    }

    /* public: table locking */
    public function lock($table, $mode = "write")
    {
        $this->connect();

        $query="lock tables ";
        if (is_array($table)) {
            while (list($key, $value) = each($table)) {
                if ($key=="read" && $key!=0) {
                    $query.="$value read, ";
                } else {
                    $query.="$value $mode, ";
                }
            }
            $query=substr($query, 0, -2);
        } else {
            $query.="$table $mode";
        }
        $res = @mysqli_query($this->Link_ID, $query);
        if (!$res) {
            $this->halt("lock($table, $mode) failed.");
            return 0;
        }
        return $res;
    }

    public function unlock()
    {
        $this->connect();

        $res = @mysqli_query($this->Link_ID, "unlock tables");
        if (!$res) {
            $this->halt("unlock() failed.");
            return 0;
        }
        return $res;
    }


    /* public: evaluate the result (size, width) */
    public function affected_rows()
    {
        return @mysqli_affected_rows($this->Link_ID);
    }

    public function num_rows()
    {
        if (isset($this->Query_ID)) {
            return @mysqli_num_rows($this->Query_ID);
        }
        return 0;
    }

    public function num_fields()
    {
        return @mysqli_num_fields($this->Query_ID);
    }

    /* public: shorthand notations */
    public function nf()
    {
        return $this->num_rows();
    }

    public function np()
    {
        print $this->num_rows();
    }

    public function f($Name)
    {
        return $this->Record[$Name];
    }

    public function p($Name)
    {
        print $this->Record[$Name];
    }

    /* public: sequence numbers */
    public function nextid($seq_name)
    {
        $this->connect();

        if ($this->lock($this->Seq_Table)) {
            /* get sequence number (locked) and increment */
            $q  = sprintf(
                "select nextid from %s where seq_name = '%s'",
                $this->Seq_Table,
                $seq_name
            );
            $id  = @mysqli_query($this->Link_ID, $q);
            $res = @mysqli_fetch_array($id);

            /* No current value, make one */
            if (!is_array($res)) {
                $currentid = 0;
                $q = sprintf(
                    "insert into %s values('%s', %s)",
                    $this->Seq_Table,
                    $seq_name,
                    $currentid
                );
                $id = @mysqli_query($this->Link_ID, $q);
            } else {
                $currentid = $res["nextid"];
            }
            $nextid = $currentid + 1;
            $q = sprintf(
                "update %s set nextid = '%s' where seq_name = '%s'",
                $this->Seq_Table,
                $nextid,
                $seq_name
            );
            $id = @mysqli_query($this->Link_ID, $q);
            $this->unlock();
        } else {
            $this->halt("cannot lock ".$this->Seq_Table." - has it been created?");
            return 0;
        }
        return $nextid;
    }

    /* public: return table metadata */
    public function metadata($table = '', $full = false)
    {
        $count = 0;
        $id    = 0;
        $res   = array();

        $remapped = $this->remap($this->type_mappings);
        $remapped_flag = $this->remap($this->flag_mappings);
        $rlengths = $this->remap($this->length_mappings);
        $remapped_extra = $this->remap($this->extra_mappings);
        /*
         * Due to compatibility problems with Table we changed the behavior
         * of metadata();
         * depending on $full, metadata returns the following values:
         *
         * - full is false (default):
         * $result[]:
         *   [0]["table"]  table name
         *   [0]["name"]   field name
         *   [0]["type"]   field type
         *   [0]["len"]    field length
         *   [0]["flags"]  field flags
         *
         * - full is true
         * $result[]:
         *   ["num_fields"] number of metadata records
         *   [0]["table"]  table name
         *   [0]["name"]   field name
         *   [0]["type"]   field type
         *   [0]["len"]    field length
         *   [0]["flags"]  field flags
         *   ["meta"][field name]  index of field named "field name"
         *   The last one is used, if you have a field name, but no index.
         *   Test:  if (isset($result['meta']['myfield'])) { ...
         */

        // if no $table specified, assume that we are working with a query
        // result
        if ($table) {
            $this->connect();
            //maybe we need escaping
            $q = sprintf("SHOW COLUMNS FROM %s.%s", $this->Database, $table);
            $id = (($___mysqli_tmp = mysqli_query($this->Link_ID, $q)) ? $___mysqli_tmp : false);
            if (!$id) {
                $this->halt("Metadata query failed.");
            }
        } else {
            $id = $this->Query_ID;
            if (!$id) {
                $this->halt("No query specified.");
            }
        }

        $i = 0;
        $meta = array();
        while ($row = mysqli_fetch_object($id)) {
            list($type, $len, $ignore) = sscanf($row->Type, "%[^[(](%d) %[^[]]");

            $res[$i]["table"] = $table;
            $res[$i]["name"] = $row->Field;
            $res[$i]["type"] = isset($remapped[$type]) ? $remapped[$type] : $type;
            $res[$i]['len'] = isset($len) ? $len : (isset($rlengths[$type]) ? $rlengths[$type] : -1);

            if ($type == 'enum') {
                preg_match("/^enum\(\'(.*)\'\)$/", $row->Type, $matches);
                $enum = explode("','", $matches[1]);
                $max = max(array_map('strlen', $enum));
                $res[$i]['len'] = $max;
            }

            if (preg_match("/^decimal\((.*)\)$/", $row->Type, $matches)) {
                $len = explode("','", $matches[1]);
                $res[$i]['len'] = $len[0]+2;
            }


            $flags = array();
            isset($remapped_flag[$row->Null])? $flags[]=$remapped_flag[$row->Null]: '' ;
            isset($remapped_flag[$row->Key])? $flags[]=$remapped_flag[$row->Key]: '' ;
            $ignore ? $flags[] = $ignore: '';
            isset($remapped_extra[$row->Extra])? $flags[]=$remapped_extra[$row->Extra]: ($row->Extra ? $flags[] = $row->Extra: '');
            isset($remapped_flag[$row->Default])? $flags[]=$remapped_flag[$row->Default]: '' ;
            isset($remapped_flag[$type])? $flags[]=$remapped_flag[$type]: '' ;
            if ($row->Extra == 'on update CURRENT_TIMESTAMP' || $row->Default == 'CURRENT_TIMESTAMP') {
                $flags[] = "timestamp";
            }


            $res[$i]['flags'] = trim(
                sprintf(
                    "%s",
                    join(' ', array_unique($flags))
                )
            );
            $meta[$row->Field] = $i;
            $i++;
        }
        // made this IF due to performance (one if is faster than $count if's)
        if ($full) {
            $res["num_fields"] = $i;
            $res["meta"]= $meta;
        }

        // free the result only if we were called on a table
        if ($table) {
            @mysqli_free_result($id);
        }
        return $res;
    }

    public function table_names()
    {
        $this->query("SHOW TABLES");
        $i = 0;
        while ($info=mysqli_fetch_row($this->Query_ID)) {
            $return[$i]["table_name"]= $info[0];
            $return[$i]["tablespace_name"]=$this->Database;
            $return[$i]["database"]=$this->Database;
            $i++;
        }
        return $return;
    }

    /* private: error handling */
    public function halt($msg)
    {
        if (!mysqli_connect_errno()) {
            $this->Error = @mysqli_error($this->Link_ID);
            $this->Errno = @mysqli_errno($this->Link_ID);
        }
        if ($this->Halt_On_Error == "no") {
            return;
        }

        $this->haltmsg($msg);

        if ($this->Halt_On_Error != "report") {
            die("Session halted.\n");
        }
    }

    private function haltmsg($msg)
    {
        $log=sprintf("Database error: %s\n", $msg);
        syslog(LOG_NOTICE, $log);

        if ($this->Errno) {
            $log=printf("MySQL error: %s (%s)\n", $this->Errno, $this->Error);
            syslog(LOG_NOTICE, $log);
        }
    }
}
?>
