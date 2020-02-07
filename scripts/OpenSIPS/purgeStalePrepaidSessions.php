#!/usr/bin/env php
<?php

require '/etc/cdrtool/global.inc';

class PrepaidSessions
{
    public function PrepaidSessions()
    {
        $this->db = new DB_cdrtool;
        $this->db1 = new DB_cdrtool;
    }

    public function removeStaleSessions()
    {
        $query = sprintf("select * from prepaid where active_sessions <> ''");

        if (!$this->db->query($query)) {
            $log = sprintf(
                "Database error for query '%s': %s (%s), link_id =%s, query_id =%s",
                $query,
                $this->db->Error,
                $this->db->Errno,
                $this->db->Link_ID,
                $this->db->Query_ID
            );
            syslog(LOG_NOTICE, $log);
            return 0;
        }

        $j=0;
        $expired=0;
        $removed=0;

        while ($this->db->next_record()) {
            $account_expired=0;
            $active_sessions = json_decode($this->db->f('active_sessions'), true);

            if (count($active_sessions)) {
                $active_sessions_new=array();

                foreach (array_keys($active_sessions) as $_session) {
                    $expired_since=time() - $active_sessions[$_session]['timestamp'] - $active_sessions[$_session]['MaxSessionTime'];
                    if ($expired_since > 120) {
                        $account_expired++;
                        $log = sprintf(
                            "Session %s for %s has expired since %d seconds\n",
                            $_session,
                            $active_sessions[$_session]['BillingPartyId'],
                            $expired_since
                        );
                        print $log;
                        $expired++;
                    } else {
                        $active_sessions_new[$_session]=$active_sessions[$_session];
                    }
                }

                if ($account_expired) {
                    $query = sprintf(
                        "update prepaid set active_sessions = '%s', session_counter  = '%s' where account  = '%s'",
                        addslashes(json_encode($active_sessions_new)),
                        count($active_sessions_new),
                        addslashes($this->db->f('account'))
                    );

                    if (!$this->db1->query($query)) {
                        $log = sprintf("Database error for %s: %s (%s)", $query, $this->db1->Error, $this->db1->Errno);
                        print $log;
                    } else {
                        $removed++;
                    }
                }
            }
            $j++;
        }

        printf(
            "%d prepaid accounts parsed, %d sessions were stale and %d account were updated\n",
            $j,
            $expired,
            $removed
        );
    }
}

$PrepaidSessions = new PrepaidSessions();
$PrepaidSessions->removeStaleSessions();
