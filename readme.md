# Django MediaWiki Authentication

This is a simple authentication plugin for MediaWiki that uses the Django
authentication app as a backend.

It requires Django 1.5.3 or later with JSON session serializer, which has been
the default since 1.6.

## Installation

* Copy `django-media-authentication/` to `extensions/` and rename to `AuthDjango/`.
* Change the configuration variables in `AuthDjango.php` to suit your needs.
  * `LinkToSiteLogin` should be Django's login page URL.
  * `LinkToWiki` should be the subdirectory that the wiki is in, including the trailing `/`.
* Give the following permissions for your MediaWiki database user:
``GRANT SELECT ON `django_db_name`.`auth_user` TO 'wikiuser'@'localhost'``
``GRANT SELECT, DELETE ON `django_db_name`.`django_session` TO 'wikiuser'@'localhost'``
* Add the following line to your `LocalSettings.php`:
`require_once('extensions/AuthDjango`/AuthDjango.php');
* Run `update.php` to update the database schema:
`cd ./maintenance/ && php update.php`

Note that while new accounts and previous logins are linked together using
Django's user id, the first time a Django user goes to the wiki where they have
an existing account the two will be linked by username. So either ensure that
all existing MediaWiki users have the same usernames as their Django accounts
or go through and manually add entries for their accounts into the
newly-created `authdjango` table in the MediaWiki database.
