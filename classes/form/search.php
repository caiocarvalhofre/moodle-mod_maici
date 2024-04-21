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
 * activity module search form definition.
 *
 * @package     mod_maici
 * @category    admin
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_maici\form;

use MoodleQuickForm;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Report search form class.
 *
 * @package     mod_maici
* @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search extends \moodleform {

    /**
     * Form definition
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'heading', get_string('search'));
        $rangegroup=array();
        $rangegroup[] =& $mform->createElement('date_selector', 'datefrom', get_string('from'));
        $rangegroup[] =& $mform->createElement('date_selector', 'dateto', get_string('to'));
        $rangegroup[] =& $mform->createElement('checkbox', 'dateenabled', '', get_string('enable'));
        $mform->addGroup($rangegroup, 'date', get_string('date'), ' ', false);
        $mform->disabledIf('date', 'dateenabled');

        // User identyt fields search
        $mform->addElement('text', 'useridentity', get_string('useridentity', 'mod_maici'));
        $mform->addHelpButton('useridentity','useridentity','mod_maici');
        $mform->setType('useridentity',PARAM_TEXT);

        $this->add_action_buttons(true, get_string('search'));

    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['datefrom']>$data['dateto']) {
            $errors['date'] = get_string("issue_date_error", "mod_maici");
        }

        return $errors;
    }

}
