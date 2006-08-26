<VirtualHost cdrtool.example.com:80>

   ServerName          cdrtool.example.com
   DocumentRoot        /var/www/
   CustomLog           /var/log/apache2/cdrtool-access.log combined
   ErrorLog            /var/log/apache2/cdrtool-errors.log
   #SSLEngine           On
   #SSLCertificateFile    /etc/apache/ssl.crt/snakeoil-rsa.crt
   #SSLCertificateKeyFile /etc/apache/ssl.key/snakeoil-rsa.key
   SetEnvIf User-Agent ".*MSIE.*"      nokeepalive ssl-unclean-shutdown

</VirtualHost>
