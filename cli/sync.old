<?php


// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Sync LDAP user roles with custom profile fields.
 *
 * This script is meant to be called from a cronjob to sync LDAP groups with ELIST Groups
 * registered in LDAP groups where the CAS/LDAP backend acts as 'master'.
 *
 */


define('CLI_SCRIPT', true);

require (dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

require (dirname(dirname(__FILE__)) . '/locallib.php');
require_once ($CFG->dirroot . '/auth/ldap/auth.php');


// Ensure errors are well explained
$CFG->debug = DEBUG_NORMAL;

if ( !is_enabled_auth('ldap')) {
    error_log('[AUTH CAS] ' . get_string('pluginnotenabled', 'auth_ldap'));
    die;
}
$starttime = microtime();
$plugin = new auth_plugin_groups();
$ldap_groups = $plugin->ldap_get_grouplist();

$difftime = microtime_diff($starttime, microtime());
print("Execution took ".$difftime." seconds".PHP_EOL);
