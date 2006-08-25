#!/bin/sh
db=cdrtool
mysql 	-u root -p$1 -h $2 -e "drop database if exists $db"
mysqladmin 	-u root -p$1 -h $2 create $db
mysql 		-u root -p$1 -h $2 mysql < ./create_users.mysql
mysql 		-u root -p$1 -h $2 $db   < ./create_tables.mysql
mysql 		-u root -p$1 -h $2 $db   < ./create_data.mysql
