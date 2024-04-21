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
 * External Class
 *
 * @package
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_maici\external;
defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/webservice/lib.php');

require_once("../../config.php");

use coding_exception;
use context_system;
use core_user;
use dml_exception;
use external_api;
use external_description;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use required_capability_exception;
use restricted_context_exception;

class external extends external_api {


    /**
     * @return external_function_parameters
     */
    public static function validate_user_tokens_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED)
        ]);
    }

    /**
     * @param $cmid
     * @return array
     * @throws \core_external\restricted_context_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function validate_user_tokens($cmid) {
        global $DB;
        $params = external_api::validate_parameters(self::validate_user_tokens_parameters(), [
            'cmid' => $cmid
        ]);
        $cmid = $params['cmid'];
        $data = [];

        // Validate context.
        $context = context_module::instance($cmid);
        self::validate_context($context);

        $moduleinstance = $DB->get_record('maici',['id'=>$context->instance]);
        $aichat = new \mod_maici\output\aichat($moduleinstance,$cmid,$moduleinstance->intro);
        $data ['usertokenvalidation'] = $aichat->maici_validate_user();

        return $data;
    }

    /**
     * @return external_single_structure
     */
    public static function validate_user_tokens_returns() {
        return new external_single_structure(
            [
                'usertokenvalidation' => new external_value(PARAM_BOOL, 'validation of user max token usage')
            ]
        );
    }

}
