Patch for 
- Added support for acct type Failed (15)
- Added MySQL stored procedures support
- Removed Cisco hack that causes errors in radius.log if acctsessiontime= 0

Updated Jan 3, 2008 by Norm Brandinger norm@goes.com
    
    Reworked patch to apply cleanly with the current CVS FreeRADIUS sources. To apply:

    Download the latest FreeRADIUS sources, for example:

        cvs -d :pserver:anoncvs@cvs.freeradius.org:/source login
        cvs -d :pserver:anoncvs@cvs.freeradius.org:/source checkout radiusd

    CD to the FreeRADIUS sources, for example: 

	cd /usr/local/src/radiusd
    
    Patch the sources, for example:

	patch -p1 < /usr/local/src/CDRTool/setup/radius/freeradius/freeradius_20080103.patch

    Configure, compile and install as usual, for example:

        ./configure
        make
        install

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
