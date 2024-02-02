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


if (true) {
    $settings = new admin_settingpage('mod_maici_settings', new lang_string('pluginname', 'mod_maici'));

    if ($ADMIN->fulltree) {
        //Default Description block
        $name = new lang_string('description', 'mod_maici');
        $description = new lang_string('description_help', 'mod_maici');
        $default = get_string('descriptiondefault', 'mod_maici');
        $setting = new admin_setting_configtextarea('mod_maici/descriptionblock',
            $name,
            $description,
            $default);
        $setting->set_force_ltr(false);
        $settings->add($setting);

        //Default Instruction block
        $name = new lang_string('instruction', 'mod_maici');
        $description = new lang_string('instruction_help', 'mod_maici');
        $default = get_string('instructiondefault', 'mod_maici');
        $setting = new admin_setting_configtextarea('mod_maici/instructionblock',
            $name,
            $description,
            $default);
        $setting->set_force_ltr(false);
        $settings->add($setting);

        // Less non-HTML characters than this is short
        $settings->add(new admin_setting_configtext('mod_maici/maxlength', get_string('maxlength', 'mod_maici'),
            get_string('maxlength_help', 'maici'), 300, PARAM_INT));

        $settings->add(new admin_setting_configtext('mod_maici/apikey', get_string('apikey','mod_maici'),
            get_string('descapikey', 'maici'), '',PARAM_TEXT));

    }
}
