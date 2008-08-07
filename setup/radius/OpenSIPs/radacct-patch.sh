#!/bin/sh

if [ $# -ge 1 ]; then
   radacct=$1
else
   radacct='radacct'
fi

echo -n "Please type in MySQL root password (^C to abort): "

read rootpass

echo "Patching table $radacct..."

mysql -u root -p$rootpass radius << EOF

# Fields required for SIP support 
alter table $radacct add column SipMethod varchar(50) not null;
alter table $radacct add column SipResponseCode smallint unsigned not null;
alter table $radacct add column SipToTag varchar(128) not null;
alter table $radacct add column SipFromTag varchar(128) not null;
alter table $radacct add column SipTranslatedRequestURI varchar(255) NOT NULL;
alter table $radacct add column SipUserAgents varchar(128) NOT NULL;
alter table $radacct add column SipApplicationType varchar(64) NOT NULL;
alter table $radacct add column SipCodecs varchar(255) NOT NULL;
alter table $radacct add column SipRPID varchar(25) not null;
alter table $radacct add column SipRPIDHeader varchar(25) not null;
alter table $radacct add column SourceIP varchar(64) not null;
alter table $radacct add column SourcePort varchar(5) not null;
alter table $radacct add column CanonicalURI varchar(255) not null;
alter table $radacct add column DelayTime varchar(5) not null;

# Fields required for normalization
alter table $radacct add column Timestamp bigint(20) unsigned not null;
alter table $radacct add column DestinationId varchar(255) not null;
alter table $radacct add column Rate text not null;
alter table $radacct add column Price double(20,4)  not null;
alter table $radacct add Normalized enum('0','1') default '0';
alter table $radacct add BillingId varchar(255) NOT NULL default '';
alter table $radacct add column MediaInfo varchar(32) default NULL;
alter table $radacct add column RTPStatistics text not null;
alter table $radacct add column FromHeader varchar(128) not null; 
alter table $radacct add column UserAgent varchar(128) not null; 
alter table $radacct add column Contact varchar(128) not null;

# Enlarge some small columns
alter table $radacct change column AcctSessionId AcctSessionId varchar(255) not null;
alter table $radacct change column NASPortType NASPortType varchar(255) not null;
alter table $radacct change column NASPortId NASPortId varchar(50) not null;
alter table $radacct change column AcctUniqueId AcctUniqueId varchar(255) not null;
alter table $radacct change column FramedProtocol ENUMtld varchar(64) not null;

alter table $radacct add unique sess_id(AcctSessionId(128),SipFromTag,SipToTag) ;

alter table $radacct add index caller_idx (CallingStationId);
alter table $radacct add index called_idx (CalledStationId);
alter table $radacct add index canon_idx  (CanonicalURI);
alter table $radacct add index normalize_idx(Normalized);
alter table $radacct add index source_ip_idx (SourceIP);
alter table $radacct add index billing_id_idx (BillingId);
alter table $radacct add index dest_id_idx (DestinationId);
alter table $radacct add index MediaInfo_idx(MediaInfo);
alter table $radacct add index Realm_idx(Realm);

EOF

echo "done"
