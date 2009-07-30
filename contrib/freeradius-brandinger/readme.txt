Patch for 
- Added support for acct type Failed (15)
- Added MySQL stored procedures support
- Removed Cisco hack that causes errors in radius.log if acctsessiontime= 0

Update July 22, 2009 by Norm Brandinger norm@goes.com

    Reworked patch to apply cleanly with the current CVS FreeRADIUS sources.  To apply:

    Download the latest FreeRADIUS sources, for example:

        cvs -d :pserver:anoncvs@cvs.freeradius.org:/source login
        cvs -d :pserver:anoncvs@cvs.freeradius.org:/source checkout radiusd

    Change to the FreeRADIUS sources, for example:

        cd radiusd

    Apply the patch:

        patch -p0 < ../CDRTool/contrib/freeradius-brandinger/freeradius-20090722.patch

    Configure and Make as usual, for example:

    ./configure
    make

Updated Feb 23, 2009 by Norm Brandinger norm@goes.com
	
	Reworked patch to apply cleanly with the current CVS FreeRADIUS sources.  To apply:

	The procedures listed below should still continue to work.  
	The patch was generated with the "cvs diff" command so to replace the patch from within the radius subdirectory use the following:

	patch -p0 < ../freeradius-20090223.patch

	If you're using the apt-get method to obtain the sources, the commands to apply the patch would be similar to the following.
	This patch was generated with a "diff -r -u" command.

	cd freeradius-2.0.4+dfsg
	patch -p1 < ../freeradius-2.0.4+dfsg.patch


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
