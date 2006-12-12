Patch for 
- Acct type Failed

Todo
- Stored procedures
- Remove Cisco hack

Install freeradius by compiling from source:

   Run the following commands to get the freeradius source, apply the patch,
   then compile and install the resulted debian packages:
   
     apt-get build-dep freeradius
     apt-get source freeradius
     cd freeradius-1.0.2
     patch -p0 -s < freeradius-1.0.x-failed-accounting.diff
     debuild
     cd ../
     dpkg -i freeradius_1.0.2-3_i386.deb freeradius-mysql_1.0.2-3_i386.deb

