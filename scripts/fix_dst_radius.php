#!/usr/bin/env php
<?php

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'rating.php';

function logger()
{
    global $options, $use_syslog;
    $argv = func_get_args();
    if (count($argv) != 1) {
        $format = array_shift($argv);
        $msg = vsprintf($format, $argv);
    } else {
        $msg = array_shift($argv);
    }
    $uncolored_msg = preg_replace('#\\033\[\d\d?;?\d?m#', '', $msg);
    if (array_key_exists('no-color', $options)) {
        print($uncolored_msg);
    } else {
        print($msg);
    }

    if ($use_syslog) {
        syslog(LOG_NOTICE, $uncolored_msg);
    }
}

function sql_logger()
{
    global $log_dir, $log_file;
    if (!file_exists($log_dir)) {
        return;
    }

    $argv = func_get_args();
    if (count($argv) != 1) {
        $format = array_shift($argv);
        $msg = vsprintf($format, $argv);
    } else {
        $msg = array_shift($argv);
    }

    $uncolored_msg = preg_replace('#\\033\[\d\d?;?\d?m#', '', $msg);
    $uncolored_msg = str_replace(array("\r", "\n"), '', $msg);
    $uncolored_msg = preg_replace('/\s+/', ' ', $msg);
    file_put_contents($log_file, $uncolored_msg.";\n", FILE_APPEND);
}

$filename = basename(__FILE__);
$log_dir = "/var/spool/cdrtool/dst-updater";
$log_file = $log_dir.'/undo-dst_updater'.date('dmYHis').'.sql';

$use_syslog = false;
$verbose = 0;

$shortopts  = "";
$shortopts .= "h";

$longopts  = array(
    "help",
    "year:",
    "time:",
    "dry-run",
    "no-color"
);

$options = getopt($shortopts, $longopts);

$help_msg = <<< EOF

\033[1m\033[4m\033[33mUSAGE:\033[0m

    $filename \033[32m<OPTIONS>\033[0m

    This will fix call duration for Summer/Winter time changes in the given year 
        
\033[1m\033[4m\033[33mOPTIONS:\033[0m
    
    \033[32m-h, --help\033[0m      Get the help
    \033[32m--year\033[0m          The year to fix
    \033[32m--time\033[0m          The time to change, can be summer or winter
    \033[32m--dry-run\033[0m       Only print changed, don't modify radius table
    \033[32m--no-color\033[0m      Disable color output


EOF;

if (array_key_exists('h', $options) || !$options['year'] || !$options['time']) {
    logger($help_msg);
    exit(0);
}

//$dt1 = new DateTime('2016-10-29 10:00:00');
//$dt2 = new DateTime('2016-10-31 09:00:00');

//print_r($dt1->diff($dt2));
//exit(0);
$dry_run = array_key_exists('dry-run', $options);

if ($dry_run) {
    logger("\n\033[32m%80s\033[0m\n\n", str_pad(" DRY RUN, radius table will not be modified ", 80, "=", STR_PAD_BOTH));
} else {
    logger("\n\033[31m%80s\033[0m\n\n", str_pad(" NORMAL RUN, radius table will be modified ", 80, "=", STR_PAD_BOTH));
}

if (!is_dir($log_dir)) {
    if (!mkdir($log_dir)) {
        logger("\n\033[31;1mUndo directory cannot be created: %s\033[0m\n", $log_dir);
    }
    chmod($log_dir, 0775);
}

$d1 = new DateTime(sprintf("%s-01-01 01:00", $options['year']));
$year = $d1->format('U');

$timezone = new DateTimeZone(date_default_timezone_get());
$transitions = $timezone->getTransitions($year);

$transition = new DateTime();
$transition->setTimestamp($transitions[1]['ts']);
$transition_datetime = $transition->format('Y-m-d H:i:s');
$transition_offset = $transitions[1]['offset'] - $transitions[0]['offset'];

if ($options['time'] == 'winter') {
    $transition_offset = $transitions[2]['offset'] - $transitions[1]['offset'];
    $transition->setTimestamp($transitions[2]['ts']);
    $transition_datetime = $transition->format('Y-m-d H:i:s');
    logger("\033[1mPlease note that calls with a negative duration and calls that are 1 hour too long can/will be fixed.\nCalls that are too short can't be fixed.\033[0m\n\n");
}

logger("DST transition that will be fixed: %s\n", $transition_datetime);


foreach ($DATASOURCES as $key => $value) {
    if (strlen($value['normalizedField'] and !$value['skipNormalize'])) {
        $start_time = time();
        $class_name = $value["class"];

        unset($CDRS);
        $CDRS = new $class_name($key);

        if (is_array($CDRS->db_class)) {
            $db_class = $CDRS->db_class[0];
        } else {
            $db_class = $CDRS->db_class;
        }

        $CDRS->table = 'radacct'.$transition->format('Ym');

        logger(
            "\nFixing DST on datasource %s, database %s, table %s, offset %s\n",
            $key,
            $db_class,
            $CDRS->table,
            $transition_offset
        );

        $query_excess_duration = sprintf(
            "
            SELECT
                *
            FROM
                %s
            WHERE
                    %s > %s
                AND %s < '%s'
                AND %s > '%s'
            ",
            addslashes($CDRS->table),
            addslashes($CDRS->stopTimeField),
            addslashes($CDRS->startTimeField),
            addslashes($CDRS->startTimeField),
            $transition_datetime,
            addslashes($CDRS->stopTimeField),
            $transition_datetime
        );

        $extra_query_excess_duration = sprintf(
            "
                SELECT
                   *
                FROM (
                    SELECT
                        *,
                        TIME_TO_SEC(TIMEDIFF(%s, %s)) AS diff
                    FROM
                        %s
                    WHERE
                        %s > '%s'
                        AND %s < '%s'
                    ORDER BY
                        %s
            ) AS innerTable
                WHERE
                    %s > diff
            ",
            addslashes($CDRS->stopTimeField),
            addslashes($CDRS->startTimeField),
            addslashes($CDRS->table),
            addslashes($CDRS->stopTimeField),
            $transition_datetime,
            addslashes($CDRS->startTimeField),
            $transition_datetime,
            addslashes($CDRS->startTimeField),
            addslashes($CDRS->durationField)
        );

        $query_negative_duration = sprintf(
            "
            SELECT
                *,
                ''
            FROM
                %s
            WHERE
                    %s > %s
                AND %s > '%s'
                AND %s > '%s'
                AND %s < '0'
            UNION
                %s
            ",
            addslashes($CDRS->table),
            addslashes($CDRS->startTimeField),
            addslashes($CDRS->stopTimeField),
            addslashes($CDRS->startTimeField),
            $transition_datetime,
            addslashes($CDRS->stopTimeField),
            $transition_datetime,
            addslashes($CDRS->durationField),
            $extra_query_excess_duration
        );

        $query = $query_negative_duration;
        if ($transition_offset > 0) {
            $query = $query_excess_duration;
        }
        dprint_sql($query);

        $update_rows = array();
        if ($CDRS->CDRdb->query($query)) {
            if ($CDRS->CDRdb->affected_rows()) {
                while ($CDRS->CDRdb->next_record()) {
                    $id = $CDRS->CDRdb->f($CDRS->idField);
                    $call_id = $CDRS->CDRdb->f($CDRS->callIdField);
                    $start_time = $CDRS->CDRdb->f($CDRS->startTimeField);
                    $stop_time = $CDRS->CDRdb->f($CDRS->stopTimeField);
                    $fixed_duration = $CDRS->CDRdb->f($CDRS->durationField) - $transition_offset;
                    $duration = $CDRS->CDRdb->f($CDRS->durationField);
                    if ($duration > 0) {
                        $fixed_duration = $CDRS->CDRdb->f($CDRS->durationField) + $transition_offset;
                    }
                    if ($transition_offset > 0) {
                        $stop_datetime = new DateTime($stop_time);
                        $start_datetime = new DateTime($start_time);
                        // $fixed_duration_test = $stop_datetime->getTimestamp() - $start_datetime->getTimestamp();
                        // if ($fixed_duration_test == $duration) {
                        //     logger("Durations are the same");
                        //     continue;
                        // }

                        $fixed_stop = new DateTime();
                        $fixed_stop_duration = $start_datetime->getTimestamp() + $duration;
                        $fixed_stop = $fixed_stop->setTimestamp($fixed_stop_duration);

                        if ($fixed_stop == $stop_datetime) {
                            logger(
                                "\033[032m  [%-50s] SKIP - same end dates (%s <> %s)\033[0m\n",
                                $call_id,
                                $stop_datetime->format("Y-m-d H:i:s"),
                                $fixed_stop->format("Y-m-d H:i:s")
                            );
                            continue;
                        }
                        $fixed_duration = $CDRS->CDRdb->f($CDRS->durationField);
                    }
                    $row = [
                        'id' => $id,
                        'call_id' => $call_id,
                        'start_time' => $start_time,
                        'stop_time' => $stop_time,
                        'fixed_duration' => $fixed_duration,
                        'fixed_stop_time' => $stop_time,
                        'duration' => $duration
                    ];

                    if ($fixed_stop) {
                        $row['fixed_stop_time'] = $fixed_stop->format("Y-m-d H:i:s");
                    }
                    $update_rows[] = $row;
                }
            }
        }
        if (count($update_rows) == 0) {
            logger("\nNo calls found to fix\n");
            exit;
        }
        logger("\nCalls found to fix \033[1m%s:\033[0m\n", count($update_rows));
        foreach ($update_rows as $data) {
            if ($data['fixed_duration'] != $data['duration']) {
                logger(
                    "\n  [\033[34m%-50s\033[0m]\t%s - %s\t%ss=>\033[1m\033[33m%ss\033[0m",
                    $data['call_id'],
                    $data['start_time'],
                    $data['stop_time'],
                    $data['duration'],
                    $data['fixed_duration']
                );
            } elseif ($data['stop_time'] != $data['fixed_stop_time']) {
                logger(
                    "\n  [\033[34m%-50s\033[0m]\t%s - %s=>\033[1m\033[33m%s\033[0m\t%ss",
                    $data['call_id'],
                    $data['start_time'],
                    $data['stop_time'],
                    $data['fixed_stop_time'],
                    $data['duration'],
                    $data['fixed_duration']
                );
            }
        }
        print("\n");

        if (!$dry_run) {
            logger("\nUndo file: %s", $log_file);
            foreach ($update_rows as $data) {
                $update_fields = array();
                if ($data['fixed_duration'] != $data['duration']) {
                    $update_fields = array(
                        $CDRS->table,
                        addslashes($CDRS->durationField),
                        $data['fixed_duration'],
                        addslashes($CDRS->normalizedField),
                        addslashes($CDRS->idField),
                        $data['id']
                    );
                    $undo_fields = $update_fields;
                    $undo_fields[2] = $data['duration'];
                } elseif ($data['stop_time'] != $data['fixed_stop_time']) {
                    $update_fields = array(
                        $CDRS->table,
                        addslashes($CDRS->stopTimeField),
                        $data['fixed_stop_time'],
                        addslashes($CDRS->normalizedField),
                        addslashes($CDRS->idField),
                        $data['id']
                    );
                    $undo_fields = $update_fields;
                    $undo_fields[2] = $data['stop_time'];
                }

                $update_query = vsprintf(
                    "
                    UPDATE
                        %s
                    SET
                        %s='%s', %s='0'
                    WHERE
                        %s=%s
                    ",
                    $update_fields
                );

                $undo_query = vsprintf(
                    "
                    UPDATE
                        %s
                    SET
                        %s='%s', %s='1'
                    WHERE
                        %s=%s
                    ",
                    $undo_fields
                );
                dprint_sql($update_query);
                sql_logger($undo_query);

                if (!$CDRS->CDRdb->query($update_query)) {
                    logger("Query failed: %s", $update_query);
                }
            }
            if (count($update_rows) > 0) {
                $now = new DateTime();
                if ($transition->format('m') == $now->format('m')) {
                    logger("Fix is for the current month, reset quota usage\n");
                    include 'quotaReset.php';
                }
                logger("\nRunning normalize:\n");
                $command = "/var/www/CDRTool/scripts/normalize.php ".$transition->format('Ym');
                $output = `$command`;
                echo $output;
            }
        }
        $end_time = time();
    }
}
?>
