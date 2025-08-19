<?php

class PrepaidHistory
{
    public function __construct()
    {
        $this->db = new DB_cdrtool;
    }

    function purge($days = 7)
    {
        $beforeDate=Date("Y-m-d", time()-$days*3600*24);
        $query = sprintf(
            "delete from prepaid_history where date < '%s' and action like 'Debit balance%s'",
            addslashes($beforeDate),
            '%'
        );

        if (!$this->db->query($query)) {
            $log = sprintf(
                "Database error for query %s: %s (%s)\n",
                $query,
                $this->db->Error,
                $this->db->Errno
            );
            print $log;
            syslog(LOG_NOTICE, $log);
        } else {
            $log = sprintf(
                "Purged %d records from prepaid history before %s\n",
                $this->db->affected_rows(),
                $beforeDate
            );
            print $log;
            syslog(LOG_NOTICE, $log);
        }
    }
}
