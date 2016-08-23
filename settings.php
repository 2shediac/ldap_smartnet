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

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_ldap_smartnet', get_string('pluginname', 'local_ldap_smartnet'));
    $name = 'debug_ldap_groupes';
    $title = get_string($name,'local_ldap_smartnet');
    $description = get_string($name.'_desc','local_ldap_smartnet');
    $setting = new admin_setting_configcheckbox('local_ldap_smartnet/'.$name, $title, $description, false);
    $settings ->add($setting);

    $name = 'ldap_group';
    $title = get_string($name,'local_ldap_smartnet');
    $description = get_string($name.'_desc','local_ldap_smartnet');
    $setting = new admin_setting_configtext('local_ldap_smartnet/'.$name, $title, $description, 'applicant');
    $settings ->add($setting);
    $ADMIN->add('localplugins', $settings);
}
