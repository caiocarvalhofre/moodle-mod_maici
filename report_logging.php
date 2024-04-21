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
 * The printit activity module for printing documents and confirmations.
 *
 * @package     mod_maici
* @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/maici/lib.php');
require_once($CFG->dirroot . '/mod/maici/locallib.php');
require_once($CFG->dirroot . '/lib/tablelib.php');

$id = required_param('id', PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT); // Which page to show.
$pagesize = optional_param('perpage', 30, PARAM_INT); // How many per page.
$search = new stdClass();

list($course, $cm) = get_course_and_cm_from_cmid($id);
require_login($course, true, $cm);

$maici = $DB->get_record('maici', array('id' => $cm->instance), '*', MUST_EXIST);

$url = new moodle_url('/mod/maici/report_logging.php', ['id' => $cm->id]);

$context = context_module::instance($cm->id);

$reporturl = new moodle_url('/mod/maici/report_logging.php', ['id' => $cm->id]);
$backurl = new moodle_url('/mod/maici/view.php', array('id' => $cm->id));
$mform = new \mod_maici\form\search($reporturl);


$search=new stdClass();
/** @var cache_session $cache */
$cache = cache::make_from_params(cache_store::MODE_SESSION, 'mod_maici', 'data');
if ($cachedata = $cache->get('data')) {
    $mform->set_data($cachedata);
}

// Check if we have a form submission, or a cached submission.
$data = ($mform->is_submitted() ? $mform->get_data() : fullclone($cachedata));

if ($data instanceof stdClass) {
    unset($data->submitbutton);
    $search = $data;
    $cache->set('data', $data);

}

$PAGE->set_url($url);
$PAGE->set_pagelayout('incourse');

$table = new \mod_maici\table\report_logging('report_logging');
$table->define_baseurl($url);
$filename = $course->shortname;
maici_sanitize_filename($filename);
$filename .= "-". userdate(time(),'%d_%m_%Y');

$table->is_downloading($download, $filename, 'Message logging');
$PAGE->activityheader->disable();
if(!$table->is_downloading()){
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('reportapp', 'mod_maici'), 2, null);
    echo $OUTPUT->spacer();
    echo $OUTPUT->action_link($backurl,get_string('back'),null,['class'=>'btn btn-secondary btn-sm']);
    echo html_writer::div('','activity-information');

    $mform->display();
}

$table->init_table($cm,$maici,$search);


ob_start();
$table->out($pagesize, false);
$tablehtml = ob_get_contents();
ob_end_clean();
if (!$table->is_downloading()) {
    echo html_writer::tag(
            'p',
            get_string('total', 'mod_maici', $table->count_data()),
            [
                    'data-region' => 'total-count',
            ]
    );
    echo $tablehtml;
}


if (!$table->is_downloading()) {
    echo $OUTPUT->footer($course);
}

