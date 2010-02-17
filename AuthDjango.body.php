<?php
    /* AuthDjango.php */
    error_reporting(E_ALL);
    /**
     * This plugin allows you to use the django auth system with mediawiki.
     *
     * Copyright 2009-2010 Thomas Lilley <mail@tomlilley.co.uk> (tomlilley.co.uk)
     *
     * This program is free software; you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation; either version 2 of the License, or
     * any later version.
     *
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     *
     * http://www.gnu.org/copyleft/gpl.html
     */

    require_once './includes/AuthPlugin.php';
    
    /**
     * Not actually used for authentication, UserLoadFromSession does that.
     *
     */
    class AuthDjango extends AuthPlugin {
        /**
         * Mysqli database object
         *
         * @var object
         */
        private $db;
        
        /**
         * Users table name
         *
         * @var string
         */
        private $user_table;
        
        /**
         * Sesion table name
         *
         * @var string
         */
        private $session_table;
        
        /**
         * Session profile table name
         *
         * @var string
         */
        private $session_profile_table;
        
        /**
         * Connects to the database and inititaes some variables.
         *
         */
        public function __construct() {
            // Disable mediawiki account creation
            $GLOBALS['wgGroupPermissions']['*']['createaccount'] = false;
            
            // Set table names
            $this->user_table               = $GLOBALS['wgAuthDjangoConfig']['UserTable'];
            $this->session_table            = $GLOBALS['wgAuthDjangoConfig']['SessionTable'];
            $this->session_profile_table    = $GLOBALS['wgAuthDjangoConfig']['SessionprofileTable'];
            
            // start database connection
            $this->db = new mysqli($GLOBALS['wgAuthDjangoConfig']['DjangoHost'], $GLOBALS['wgAuthDjangoConfig']['DjangoUser'], $GLOBALS['wgAuthDjangoConfig']['DjangoPass'], $GLOBALS['wgAuthDjangoConfig']['DjangoDBName']);
            
            if ($this->db->connect_error) {
                die('Connect Error (' . mysqli_connect_errno() . ') ' . $this->db->connect_error);
            }
            
            // Set hooks functions
            $GLOBALS['wgHooks']['UserLogout'][]            = $this;
            $GLOBALS['wgHooks']['UserLoadFromSession'][]   = $this;
            $GLOBALS['wgHooks']['UserLoginForm'][]         = $this;
        }
        
        /**
         * Check whether there exists a user account with the given name.
         * The name will be normalized to MediaWiki's requirements, so
         * you might need to munge it (for instance, for lowercase initial
         * letters).
         *
         * @param $username String: username.
         * @return bool
         */
        public function userExists($username) {
            // replace space with underscore (site login doesn't allow spaces in usernames)
            $username = str_replace(' ', '_', $username);
            
            $query = sprintf('SELECT username FROM %s WHERE username = "%s" LIMIT 0,1', $this->user_table, $this->db->real_escape_string($username));

            if ($result = $this->db->query($query)) {
                // single row so no looping
                $row = $result->fetch_assoc();
                $result->close();
                
                // make both usernames lowercase, then compare
                if (strtolower($row['username']) == strtolower($username)) {
                    return true;
                }
            }
            
            return false;
        }
        
        /**
         * Modify options in the login template.
         *
         * @param $template UserLoginTemplate object.
         */
        public function modifyUITemplate( &$template ) {
            # Override this!
            $template->set('usedomain', false);
            $template->set('create', false);
            $template->set('useemail', false);
        }

        /**
         * Return true if the wiki should create a new local account automatically
         * when asked to login a user who doesn't exist locally but does in the
         * external auth database.
         *
         * If you don't automatically create accounts, you must still create
         * accounts in some way. It's not possible to authenticate without
         * a local account.
         *
         * This is just a question, and shouldn't perform any actions.
         *
         * @return bool
         */
        public function autoCreate() {
            return true;
        }

        /**
         * Can users change their passwords?
         *
         * @return bool
         */
        public function allowPasswordChange() {
            return false;
        }

        /**
         * Set the given password in the authentication database.
         * As a special case, the password may be set to null to request
         * locking the password to an unusable value, with the expectation
         * that it will be set later through a mail reset or other method.
         *
         * Return true if successful.
         *
         * @param $user User object.
         * @param $password String: password.
         * @return bool
         */
        public function setPassword( $user, $password ) {
            return false;
        }
        
        /**
         * Return true to prevent logins that don't authenticate here from being
         * checked against the local database's password fields.
         *
         * This is just a question, and shouldn't perform any actions.
         *
         * @return bool
         */
        public function strict() {
            return true;
        }

        /**
         * Check if a user should authenticate locally if the global authentication fails.
         * If either this or strict() returns true, local authentication is not used.
         *
         * @param $username String: username.
         * @return bool
         */
        public function strictUserAuth( $username ) {
            return true;
        }
        
        /**
         * Login in to mediawiki from an existing django session.
         * User must be logged in to django for this to work.
         *
         * @param object $user
         * @param bool $result
         * @return bool
         */
        public function onUserLoadFromSession($user, &$result) {
            if (array_key_exists('sessionid', $_COOKIE)) {
                $django_session = $_COOKIE['sessionid'];
                
                // find if there is a user connected to this session
                $query = sprintf("SELECT auth_user.username as username, auth_user.email as email FROM %s, %s sp" .
                    " WHERE sp.session_id = '%s' AND auth_user.id = sp.user_id", $this->user_table, $this->session_profile_table, $this->db->real_escape_string($django_session));
                $qresult = $this->db->query($query);
                $row = $qresult->fetch_array(MYSQLI_BOTH);
                
                // replace space with underscore (site login doesn't allow spaces in usernames)
                $username = str_replace(' ', '_', $row['username']);
                if ($row) {
                    $u = User::newFromName($username);
                    // create a new user if one does not exist
                    if ($u->getID() == 0) {
                        if (Auth_django::autoCreate() && Auth_django::userExists($username)) {
                            $u->addToDatabase();
                            $u->setToken();
                        }
                    }
                    
                    $local_id = User::idFromName($username);
                    
                    if (!$local_id) {
                        return true;
                    }
                    
                    $user->setID($local_id);
                    $user->loadFromId();
                    $result = true;
                    $user->setCookies();
                    wfSetupSession();
                } else {
                    // if we're not logged in on the site make sure we're logged out of the database.
                    setcookie('wikidb_session', '', time()-3600);
                    unset($_COOKIE['wikidb_session']);
                    session_destroy();
                }
            }
            
            return true;
        }
        
        /**
         * Logs user out of django
         *
         * @param object $user
         * @return bool
         */
        public function onUserLogout(&$user) {
            if (array_key_exists('sessionid', $_COOKIE)) {
                // Delete session from session table and session profile table
                $query = sprintf("DELETE FROM %s WHERE session_key = '%s' LIMIT 1", $this->session_table, $this->db->real_escape_string($_COOKIE['sessionid']));
                $result = $this->db->query($query);
                $query = sprintf("DELETE FROM %s WHERE session_id = '%s' LIMIT 1", $this->session_profile_table, $this->db->real_escape_string($_COOKIE['sessionid']));
                $this->db->query($query);
            }
            
            // Clear cookies and session data
            setcookie('sessionid', '', time()-3600);
            unset($_COOKIE['sessionid']);
            session_destroy();
            return true;
        }
        
        /**
         * Changes the form action to login to django and sets username to blank
         * so mediawiki doesn't try to format it wrong (spaces/undescore issue)
         *
         * @param object $template
         * @return bool
         */
        public function onUserLoginForm($template) {
            $template->data['name'] = '';
            $template->data['action'] = $GLOBALS['wgAuthDjangoConfig']['LinkToSiteLogin'] . '?next=' . $GLOBALS['wgAuthDjangoConfig']['LinkToWiki'] . $_GET['returnto'];
            return true;
        }
    }
    