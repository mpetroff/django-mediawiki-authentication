<?php
// Django authentication plugin.

// Configuration variables
$wgAuthDjangoConfig = array();

$wgAuthDjangoConfig['DjangoHost']   = 'localhost';  // Django MySQL Host Name.
$wgAuthDjangoConfig['DjangoUser']   = 'root';       // Django MySQL Username.
$wgAuthDjangoConfig['DjangoPass']   = '';           // Django MySQL Password.
$wgAuthDjangoConfig['DjangoDBName'] = 'django';     // Django MySQL Database Name.

$wgAuthDjangoConfig['AuthDjangoTable']      = 'authdjango';
$wgAuthDjangoConfig['UserTable']            = 'auth_user';
$wgAuthDjangoConfig['SessionTable']         = 'django_session';
$wgAuthDjangoConfig['SessionprofileTable']  = 'sessionprofile_sessionprofile';

$wgAuthDjangoConfig['LinkToSiteLogin']       = '/accounts/login/';
$wgAuthDjangoConfig['LinkToWiki']            = '/wiki/';

// Load classes
$wgAutoloadClasses['AuthPlugin'] = dirname('./include') . '/AuthPlugin.php';
$wgAutoloadClasses['AuthDjango'] = dirname(__FILE__) . '/AuthDjango.body.php';

// Schema updates for update.php
$wgHooks['LoadExtensionSchemaUpdates'][] = 'addAuthDjangoTables';
function addAuthDjangoTables() {
    global $wgExtNewTables;
    $wgExtNewTables[] = array(
        'authdjango',
        dirname( __FILE__ ) . '/tables-authdjango.sql' );
    return true;
}

$wgExtensionFunctions[] = "initAuthDjango";
function initAuthDjango() {
    $wgAuth = new AuthDjango();     // Initiate Auth Plugin
}
