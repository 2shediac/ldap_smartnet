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
 * Code for handling synching Smartnet LDAP user roles with Moodle
 *

 *
 * @package local
 * @subpackage ldap_smartnet
 */


defined('MOODLE_INTERNAL') || die();

require_once ($CFG->dirroot . '/auth/ldap/auth.php');

class auth_plugin_groups extends auth_plugin_ldap {


    function ldap_get_grouplist($filter = "*") {
        /// returns all groups from ldap servers

        global $CFG, $DB;

        print_string('connectingldap', 'auth_ldap');
        $ldapconnection = $this->ldap_connect();
        $filter='';
        
        $ldaptree = explode(';', $this->config->contexts);
        $groups = get_config('local_ldap_smartnet','ldap_group');
        $grouplist = explode(',', $groups);
        
        foreach ($grouplist as $gp) {
            $gp = trim($gp);
            if (empty ($gp)) {
                continue;
            }
            $gp = '(cn='.$gp.')';
            $filter = $filter .$gp;
        }
        $filter= '(| '.$filter.')';
        $ldap_result = ldap_search($ldapconnection,$ldaptree,$filter);
        $memberlist = ldap_get_entries($ldapconnection, $ldap_result);
        $gc = count($memberlist);

        for ($i = 0; $i < count($memberlist) - 1; $i++) {
            $memberdetail = $memberlist[$i]["cn"][0];
            $memberdetail_info = trim(json_encode($memberdetail));
            $memberdetail_info = str_replace('"', "", $memberdetail_info);
            echo "\t ".print_string('ldap_role', 'local_ldap_smartnet').$memberdetail_info."\n";
            /**
             * Find the record in the profile field
             */
            $memberdetail_info = str_replace('_', "", $memberdetail_info);
            $select = "shortname = '".$memberdetail_info."'";
            $profile_field = $DB->get_record_select('user_info_field',$select);
            if (!empty($profile_field)) {
                $profile_id = $profile_field->id;
                if (isset ($memberlist[$i]["memberuid"])) {
                    $members = $memberlist[$i]["memberuid"];
                    $mcount = count($members) - 1;
                    for ($j = 0;$j < $mcount; $j++) {
                        $glist = trim($members[$j]);
                        $glist = addslashes($glist);
                        echo "\t ".print_string('user_process','local_ldap_smartnet').$glist."\n";
                        $selectuser = "username='".$glist."'";
                        $userinfo = $DB->get_record_select('user',$selectuser);
                        if (!empty($userinfo)) {
                            $userid = $userinfo->id;
                            /* 
                             * see if record exists in user info data
                            */
                            $selectinfo = "userid = '".$userid."' and fieldid='".$profile_id."'";
                            $userdata_found = $DB->record_exists_select('user_info_data',$selectinfo);
                            if ($userdata_found ){
                                $data_details = $DB->get_record_select('user_info_data',$selectinfo);
                                $dataval = trim($data_details->data);
                                if ($dataval == '0') {
                                    $data_details->data='1';
                                    $DB->update_record('user_info_data',$data_details);
                                }
                            } else {
                                $record = new stdClass();
                                $record->userid = $userid;
                                $record->fieldid = $profile_id;
                                $record->data = '1';
                                $record->dataformat = '0';
                                $DB->insert_record('user_info_data',$record);
                            }
                        }
                    }
                 }
             }
        }
        $this->ldap_close();
        return ;
    }
}