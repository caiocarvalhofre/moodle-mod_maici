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
 * Prints an instance of mod_maici.
 *
 * @package     mod_maici
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
$functions = array(
    'mod_maici_validate_user_tokens' => array(
        'classname' => 'mod_maici\external',
        'methodname' => 'validate_user_tokens',
        'classpath' => 'mod_maici\classes\external.php',
        'description' => 'Check user token usage',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
        'loginrequired' => true,
    ),
    );

