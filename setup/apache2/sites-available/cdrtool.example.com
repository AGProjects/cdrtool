<VirtualHost cdr.example.com:443>

   ServerName          cdr.example.com
   DocumentRoot        /var/www/
   CustomLog           /var/log/apache2/cdrtool-access.log combined
   ErrorLog            /var/log/apache2/cdrtool-errors.log
   SetEnvIf User-Agent ".*MSIE.*"      nokeepalive ssl-unclean-shutdown

   # To enable SSL:
   # a2enmode ssl
   # add Listen 443 to ports.conf file
   # generate site certificates

   SSLEngine           On
   SSLCertificateFile    /etc/apache2/ssl/cdr.example.com.crt
   SSLCertificateKeyFile /etc/apache2/ssl/cdr.example.com.key

   # RewriteEngine is required for Multimedia Service Platform
   #
   # a2enmode rewrite proxy
   # RewriteEngine       On
   # RewriteRule         ^/ngnpro/voicemail/(.*) http://10.0.0.1:9200/$1 [L,P]
   # RewriteRule         ^/ngnpro/(.*) http://10.0.0.2:9200/$1 [L,P]
   # ProxyVia            On

   # <Proxy *>
   #      Order Allow,Deny
   #      Allow from all
   # </Proxy>

        <Directory /var/www/CDRTool>
                Options FollowSymLinks
                AllowOverride All
        </Directory>
   	
</VirtualHost>
