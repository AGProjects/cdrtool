Patch for 
- Added support for acct type Failed (15)
- Added MySQL stored procedures support
- Removed Cisco hack that causes errors in radius.log if acctsessiontime= 0

Install freeradius by compiling from source:

   Run the following commands to get the freeradius source, apply the patch,
   then compile and install the resulted debian packages:
   
     apt-get build-dep freeradius
     apt-get source freeradius
     cd freeradius-1.1.3
     patch -p1 -s < freeradius.patch
     debuild
     cd ../
     dpkg -i freeradius*deb
