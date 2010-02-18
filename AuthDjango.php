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
$wgAuthDjangoConfig['LinkToSiteLogin']       = '/accounts/login/';
$wgAuthDjangoConfig['LinkToSiteLogout']      = '/accounts/logout/';
$wgAuthDjangoConfig['LinkToSiteRegister']    = '/accounts/register/';
$wgAuthDjangoConfig['LinkToWiki']            = '/wiki/';
 
$wgAuth = new AuthDjango();     // Auth_django Plugin.