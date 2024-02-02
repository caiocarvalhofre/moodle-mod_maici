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
 * Define mod_maici completion classes
 *
 * @package     mod_maici
 * @category    completion
 * @author       <>
 * 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_maici\completion;

defined('MOODLE_INTERNAL') || die();

use core_completion\activity_custom_completion;


/**
 * Activity custom completion subclass for the scorm activity.
 *
 * Contains the class for defining mod_scorm's custom completion rules
 * and fetching a scorm instance's completion statuses for a user.
 *
 * @package mod_scorm
 * @copyright Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion extends activity_custom_completion {

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    public function get_state(string $rule): int {
        global $DB;
        $this->validate_rule($rule);
        switch ($rule) {
            case 'completionai':
                $query = "SELECT ml.id, m.completionaiexchanges FROM {maici_logs} ml 
                          LEFT JOIN {maici} m ON m.id=ml.maiciid WHERE ml.userid=:userid 
                                                                   AND ml.cmid=:cmid";


                if($records = $DB->get_records_sql($query, array('userid' => $this->userid, 'cmid' => $this->cm->id))){
                    $data = array_values($records)[0];
                    $status = count($records) >= $data->completionaiexchanges ? true : false;
                }else{
                    $status = false;
                }

                break;
            default:
                $status = false;
                break;
        }

        return $status ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [
                'completionai',

        ];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        return [
                'completionai' =>
                        get_string('completiondetail:exchanges', 'mod_maici'),

        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
                'completionview',
                'completionai',
        ];
    }
}

