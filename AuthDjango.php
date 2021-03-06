<?php
// Django authentication plugin.

// Configuration variables
$wgAuthDjangoConfig = array();

$wgAuthDjangoConfig['DjangoHost']   = $wgDBserver;   // Django MySQL Host Name.
$wgAuthDjangoConfig['DjangoUser']   = $wgDBuser;     // Django MySQL Username.
$wgAuthDjangoConfig['DjangoPass']   = $wgDBpassword; // Django MySQL Password.
$wgAuthDjangoConfig['DjangoDBName'] = 'django';      // Django MySQL Database Name.

$wgAuthDjangoConfig['AuthDjangoTable']      = 'authdjango';
$wgAuthDjangoConfig['UserTable']            = 'auth_user';
$wgAuthDjangoConfig['SessionTable']         = 'django_session';

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
    global $wgAuth;
    $wgAuth = new AuthDjango();     // Initiate Auth Plugin
}
