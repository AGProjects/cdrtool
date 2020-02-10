#!/usr/bin/env sh

if [ $# -ge 1 ]; then
   radacct=$1
else
   radacct='radacct'
fi

echo -n "Please type in MySQL root password (^C to abort): "

read rootpass

echo "Patching table $radacct..."

mysql -u root -p$rootpass radius << EOF

# Fields required for H323 Vendor Attributes
alter table $radacct add column H323GWID VARCHAR(32) DEFAULT '' NOT NULL;
alter table $radacct add column H323CallOrigin VARCHAR(10) DEFAULT '' NOT NULL;
alter table $radacct add column H323CallType VARCHAR(64) DEFAULT '' NOT NULL;
alter table $radacct add column H323SetupTime timestamp NOT NULL;
alter table $radacct add column H323ConnectTime timestamp NOT NULL;
alter table $radacct add column H323DisconnectTime timestamp NOT NULL;
alter table $radacct add column H323DisconnectCause varchar(2) DEFAULT '' NOT NULL;
alter table $radacct add column H323RemoteAddress varchar(255) NOT NULL;
alter table $radacct add column H323VoiceQuality NUMERIC(2);
alter table $radacct add column H323ConfID VARCHAR(255) DEFAULT '' NOT NULL;
alter table $radacct add column CiscoNASPort varchar(255) not null after NASIPAddress;

# Fields required for normalization
alter table $radacct add column Timestamp bigint(20) unsigned not null;
alter table $radacct add column DestinationId varchar(255) not null;
alter table $radacct add column Price double(20, 4);
alter table $radacct add column Rate text not null;
alter table $radacct add Normalized enum('0','1') default '0';

# Enlarge some small columns
alter table $radacct change column AcctSessionId AcctSessionId varchar(255) not null;
alter table $radacct change column NASPortType NASPortType varchar(255) not null;
alter table $radacct change column NASPortId NASPortId varchar(50) not null;
alter table $radacct change column AcctUniqueId AcctUniqueId varchar(255) not null;
alter table $radacct change column H323DisconnectCause H323DisconnectCause varchar(255) not null;
alter table $radacct change column H323CallOrigin H323CallOrigin varchar(128) not null;
alter table $radacct change column H323ConfID H323ConfID varchar(128) not null;

alter table radacct add index caller_idx (CallingStationId);
alter table radacct add index called_idx (CalledStationId);
alter table radacct add index Normalized_idx (Normalized);

EOF

echo "done"
