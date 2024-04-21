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
 * Plugin upgrade steps are defined here.
 *
 * @package     mod_maici
 * @category    upgrade
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>   
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute mod_maici upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_maici_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024022000) {
        $table = new xmldb_table('maici');
        if ($dbman->field_exists($table, 'prompt')) {
            $field = new xmldb_field('prompt');
            $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, false, null, null);
            $dbman->change_field_type($table, $field);
        }

        if ($dbman->field_exists($table, 'sourceoftruth')) {
            $field = new xmldb_field('sourceoftruth');
            $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, false, null, null);
            $dbman->change_field_type($table, $field);
        }

        // maici savepoint reached.
        upgrade_mod_savepoint(true, 2024022000, 'maici');
    }

    if ($oldversion < 2024030500) {
        $table = new xmldb_table('maici');
        $field = new xmldb_field('instructiontokens', XMLDB_TYPE_INTEGER, '10', null, null, null, '0','sourceoftruth');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // maici savepoint reached.
        upgrade_mod_savepoint(true, 2024030500, 'maici');
    }

    return true;
}
