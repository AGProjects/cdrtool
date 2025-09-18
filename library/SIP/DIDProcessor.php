<?php

class DIDProcessor
{
    private $db;

    public function __construct()
    {

        /*
        http://www.didww.com/support/
        API help page: http://open.didww.com
        */

        $this->db = new DB_CDRTool();

        require('didww_soap_library.php');
        include("/etc/cdrtool/enrollment/config.ini");

        if (!$enrollment['did_username'] || !$enrollment['did_key']) {
            print '<p>Error: Missing DID engine credentials';
            return false;
        }

        if ($enrollment['did_environment'] == 'production') {
            $this->did_engine = new WebService_DID_World_Wide__DID_World_Wide_Port();
            $this->auth_string = sha1($enrollment['did_username'].$enrollment['did_key']);
            $this->environment='production';
        } else {
            print "<h2>Testing DID environment</h2>";
            flush();
            $this->did_engine = new WebService_DID_World_Wide__DID_World_Wide_Port_Testing();
            $this->auth_string = sha1($enrollment['did_username'].$enrollment['did_key'].'sandbox');
            $this->environment='testing';
        }

        $this->did_engine->_options['timeout'] = 30;
    }

    function getPrefixesFromRemote()
    {

        if (!$this->auth_string) return false;

        $result = $this->did_engine->didww_getdidwwregions($this->auth_string, $country);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf("<p><font color=red>Error: %s (%s): %s</font>", $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            return false;
        } else {
            foreach ($result as $_country) {
                foreach ($_country->cities as $_city) {
                	$prefix = $_country->country_prefix.$_city->city_prefix;
                    if (!$_city->isavailable) continue;
                    $prefixes[$prefix]=array('country_prefix' => trim($_country->country_prefix),
                    	                     'country_name'   => trim($_country->country_name),
                                             'country_iso'    => trim($_country->country_iso),
                                             'city_name'      => trim($_city->city_name),
                                             'city_prefix'    => trim($_city->city_prefix),
                                             'setup'          => $_city->setup,
                                             'monthly'        => $_city->monthly
                                              );
                }
            }
        }

        return $prefixes;
    }

    function getPrefixes()
    {

        $query=sprintf("select * from ddi_cache where environment = '%s' and DATE_ADD(date, INTERVAL +1 day) > NOW()", addslashes($this->environment));

        if (!$this->db->query($query)) return false;

        if ($this->db->num_rows()) {
            $this->db->next_record();
            $prefixes = json_decode($this->db->f('cache'), true);
            if (!is_array($prefixes)) {
                $prefixes = $this->cachePrefixes();
            }
        } else {
            $prefixes=$this->cachePrefixes();
        }

        return $prefixes;
    }

    function cachePrefixes()
    {
        if ($prefixes = $this->getPrefixesFromRemote()) {
            $query=sprintf("delete from ddi_cache where environment = '%s'", addslashes($this->environment));
            $this->db->query($query);

            $query=sprintf("insert into ddi_cache (cache, date, environment) values ('%s', NOW(),'%s')", addslashes(json_encode($prefixes)), addslashes($this->environment));
            $this->db->query($query);
            return $prefixes;
        } else {
            return false;
        }
    }

    function getResellerInfo()
    {

        if (!$this->auth_string) return false;

        $result = $this->did_engine->didww_getdidwwapidetails($this->auth_string);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf("<p><font color=red>Error: %s (%s): %s</font>", $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            return false;
        } else {
            print "<pre>";
            print_r($result);
            print "</pre>";
        }
    }

    function createOrder($data)
    {
        if (!$this->auth_string) return false;

        print "<pre>";
        print_r($data);
        print "</pre>";

        $result = $this->did_engine->didww_ordercreate(
            $this->auth_string,
            $data['customer_id'],
            $data['country_iso'],
            $data['city_prefix'],
            $data['period'],
            $data['map_data'],
            $data['prepaid_funds'],
            $data['uniq_hash']
        );

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf("<p><font color=red>Error: %s (%s): %s</font>", $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            return false;
        } else {
            $query = sprintf(
                "insert into ddi_numbers (
                `customer_id`,
                `country_name`,
                `city_name`,
                `did_number`,
                `did_status`,
                `did_timeleft`,
                `did_expire_date_gmt`,
                `order_id`,
                `order_status`,
                `sip_address`,
                `did_setup`,
                `did_monthly`,
                `did_period`,
                `prepaid_balance`,
                `environment`
                )
                values
                (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
                )
                ",
                addslashes($data['customer_id']),
                addslashes($result->country_name),
                addslashes($result->city_name),
                addslashes($result->did_number),
                addslashes($result->did_status),
                addslashes($result->did_timeleft),
                addslashes($result->did_expire_date_gmt),
                addslashes($result->order_id),
                addslashes($result->order_status),
                addslashes($data['map_data']['map_detail']),
                addslashes($result->did_setup),
                addslashes($result->did_monthly),
                addslashes($result->did_period),
                addslashes($result->prepaid_balance),
                addslashes($this->environment)
            );

            if (!$this->db->query($query)) {
                $log=sprintf("Database error for DID createOrder: %s (%s)", $this->db->Error, $this->db->Errno);
                print $log;
        		syslog(LOG_NOTICE, $log);
            }
        }
    }

    function renewOrder($data)
    {
        if (!$this->auth_string) return false;

        print "<pre>";
        print_r($data);
        print "</pre>";

        $result = $this->did_engine->didww_orderautorenew(
            $this->auth_string,
            $data['customer_id'],
            $data['number'],
            $data['period'],
            $data['uniq_hash']
        );

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf("<p><font color=red>Error: %s (%s): %s</font>", $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            return false;
        } else {
            $query = sprintf(
                "update ddi_numbers set did_timeleft = '%s' and did_expire_date_gmt = '%s' where did_number = '%s'",
                addslashes($result->did_timeleft),
                addslashes($result->did_expire_date_gmt),
                addslashes($result->did_number)
            );

            if (!$this->db->query($query)) {
                $log=sprintf("Database error for DID renewOrder: %s (%s)", $this->db->Error, $this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
            }

            print $query;
        }
    }

    function cancelOrder($data)
    {
        if (!$this->auth_string) return false;

        print "<pre>";
        print_r($data);
        print "</pre>";

        $result = $this->did_engine->didww_ordercancel(
            $this->auth_string,
            $data['customer_id'],
            $data['number']
        );

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf("<p><font color=red>Error: %s (%s): %s</font>", $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
            return false;
        } else {
            $query=sprintf("delete from ddi_numbers where did_number = '%s'", addslashes($result->did_number));

            if (!$this->db->query($query)) {
                $log=sprintf("Database error for DID cancelOrder: %s (%s)", $this->db->Error, $this->db->Errno);
                print $log;
                syslog(LOG_NOTICE, $log);
            }

            print $query;
        }
    }

    function getOrders($sip_address)
    {
        $orders=array();

        $query=sprintf("select * from ddi_numbers where sip_address = '%s' and environment = '%s'", addslashes($sip_address), addslashes($this->environment));

        if (!$this->db->query($query)) {
            $log=sprintf("Database error for DID createOrder: %s (%s)", $this->db->Error, $this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
        } else {
            while ($this->db->next_record()) {
                $orders[$this->db->f('did_number')] = array(
                    'country_name' => $this->db->f('country_name'),
                    'city_name' => $this->db->f('city_name'),
                    'did_status' => $this->db->f('did_status'),
                    'did_timeleft' => $this->db->f('did_timeleft'),
                    'did_expire_date_gmt' => $this->db->f('did_expire_date_gmt'),
                    'order_id' => $this->db->f('order_id'),
                    'order_status' => $this->db->f('order_status'),
                    'sip_address' => $this->db->f('sip_address'),
                    'did_setup' => $this->db->f('did_setup'),
                    'did_monthly' => $this->db->f('did_monthly')
                );
            }
        }

        return $orders;
    }
}
