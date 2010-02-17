<?php
// Django authentication plugin.
require_once './extensions/ad.php';
 
$wgAuthDjangoConfig = array();
 
$wgAuthDjangoConfig['DjangoHost']   = 'localhost';  // Django MySQL Host Name.
$wgAuthDjangoConfig['DjangoUser']   = 'root';       // Django MySQL Username.
$wgAuthDjangoConfig['DjangoPass']   = '';           // Django MySQL Password.
$wgAuthDjangoConfig['DjangoDBName'] = 'django';     // phpBB MySQL Database Name.

$wgAuthDjangoConfig['UserTable']            = 'auth_user';
$wgAuthDjangoConfig['SessionTable']         = 'django_session';
$wgAuthDjangoConfig['SessionprofileTable']  = 'sessionprofile_sessionprofile';

$wgAuthDjangoConfig['LinkToSite']            = 'http://localhost/';
$wgAuthDjangoConfig['LinkToSiteLogin']       = $wgAuthDjangoConfig['LinkToSite'] . 'accounts/login/';
$wgAuthDjangoConfig['LinkToSiteLogout']      = $wgAuthDjangoConfig['LinkToSite'] . 'accounts/logout/';
$wgAuthDjangoConfig['LinkToSiteRegister']    = $wgAuthDjangoConfig['LinkToSite'] . 'accounts/register/';
$wgAuthDjangoConfig['LinkToWiki']            = $wgAuthDjangoConfig['LinkToSite'] . '/wiki/index.php/';
 
$wgAuth = new AuthDjango();     // Auth_django Plugin.