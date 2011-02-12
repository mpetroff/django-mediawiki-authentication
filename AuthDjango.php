<?php
// Django authentication plugin.
$wgAutoloadClasses['AuthDjango'] = dirname(__FILE__) . '/AuthDjango.body.php';

# Schema updates for update.php
$wgHooks['LoadExtensionSchemaUpdates'][] = 'addAuthDjangoTables';
function addAuthDjangoTables() {
    global $wgExtNewTables;
    $wgExtNewTables[] = array(
        'djangouser',
        dirname( __FILE__ ) . '/tables-authdjango.sql' );
    return true;
}


$wgAuthDjangoConfig = array();

$wgAuthDjangoConfig['DjangoHost']   = 'localhost';  // Django MySQL Host Name.
$wgAuthDjangoConfig['DjangoUser']   = 'root';       // Django MySQL Username.
$wgAuthDjangoConfig['DjangoPass']   = '';           // Django MySQL Password.
$wgAuthDjangoConfig['DjangoDBName'] = 'django';     // phpBB MySQL Database Name.

$wgAuthDjangoConfig['AuthDjangoTable']      = 'authdjango';
$wgAuthDjangoConfig['UserTable']            = 'auth_user';
$wgAuthDjangoConfig['SessionTable']         = 'django_session';
$wgAuthDjangoConfig['SessionprofileTable']  = 'sessionprofile_sessionprofile';

$wgAuthDjangoConfig['LinkToSite']            = 'http://localhost/';
$wgAuthDjangoConfig['LinkToSiteLogin']       = '/accounts/login/';
$wgAuthDjangoConfig['LinkToSiteLogout']      = '/accounts/logout/';
$wgAuthDjangoConfig['LinkToSiteRegister']    = '/accounts/register/';
$wgAuthDjangoConfig['LinkToWiki']            = '/wiki/';

$wgAuth = new AuthDjango();     // Initiate Auth Plugin
?>
