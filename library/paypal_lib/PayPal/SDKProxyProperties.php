<?php
/****************************************************
SDKProxyProperties.php

This is the configuration file for the Proxy setup. This file 
defines the parameters needed to make an API call through Proxy.

Called by HTTP.php.
****************************************************/
/**
USE_PROXY: Set this variable to TRUE to route all the API requests through proxy.
like define('USE_PROXY',TRUE);
*/
define('USE_PROXY',FALSE);
/**
PROXY_HOST: Set the host name or the IP address of proxy server.
PROXY_PORT: Set proxy port.
PROXY_USER: Set proxy user id (optional)
PROXY_PASSWORD: Set proxy password (optional)
PROXY_HOST,PROXY_PORT,PROXY_USER and PROXY_PASSWORD will be read only if USE_PROXY is set to TRUE
*/
define('PROXY_HOST', '127.0.0.1');
define('PROXY_PORT', '808');

define('PROXY_USER', 'test');
define('PROXY_PASSWORD', 'test');

?>