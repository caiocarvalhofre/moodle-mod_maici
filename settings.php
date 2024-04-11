<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     mod_maici
 * @category    admin
 * @copyright   
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings = new admin_settingpage('mod_maici_settings', new lang_string('pluginname', 'mod_maici'));
    $settings->add(new admin_setting_configtext('mod_maici/maxtokenslimit', get_string('maxtokenslimit', 'mod_maici'),
        get_string('maxtokenslimit_help', 'maici'), 3000, PARAM_INT));

    $settings->add(new admin_setting_configtext('mod_maici/maxtokens', get_string('maxtokens', 'mod_maici'),
        get_string('maxtokens_help', 'maici'), 3000, PARAM_INT));

    $settings->add(new admin_setting_configtext('mod_maici/apikey', get_string('apikey','mod_maici'),
        get_string('descapikey', 'maici'), '',PARAM_TEXT));

}
