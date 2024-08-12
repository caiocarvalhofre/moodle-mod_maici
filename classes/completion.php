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
 * Base completion object class
 *
 * @package    mod_maici
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace mod_maici;
defined('MOODLE_INTERNAL') || die;

class completion {

    protected $apikey;
    protected $apiurl;
    protected $message;
    protected $history;
    protected $cmid;
    protected $maiciid;
    protected $conversation_logging;

    protected $assistantname;
    protected $username;
    protected $prompt;
    protected $sourceoftruth;
    protected $model;
    protected $temperature;
    protected $maxlength;
    protected $topp;
    protected $frequency;
    protected $presence;

    protected $assistant;
    protected $instructions;

    /**
     * Initialize all the class properties that we'll need regardless of model
     * @param string model: The name of the model we're using
     * @param string message: The most recent message sent by the user
     * @param array history: An array of objects containing the history of the conversation
     * @param string block_settings: An object containing the instance-level settings if applicable
     */
    public function __construct($model, $message, $history, $block_settings) {
        // Set default values
        $this->model = $model;

        // Then override with block settings if applicable
            foreach ($block_settings as $name => $value) {
                if ($value) {
                    $this->$name = $value;
                }
            }

        $this->message = $message;
        $this->history = $history;
        $this->sourceoftruth = $block_settings['sourceoftruth'];
        //$this->build_source_of_truth($block_settings['sourceoftruth']);
    }


    /**
     * Make the source of truth ready to add to the prompt by appending some extra information
     * @param string localsourceoftruth: The instance-level source of truth we got from the API call 
     */
    private function build_source_of_truth($localsourceoftruth) {
        $sourceoftruth = '';
    
        if ($localsourceoftruth) {
            $sourceoftruth = 
                get_string('sourceoftruthpreamble', 'mod_maici')
                . $localsourceoftruth . "\n\n";
            }
        $this->sourceoftruth = $sourceoftruth;
    }

    /**
     * @param $usage
     * @param $completion
     * @return bool|int
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function log_conversation($completion, $moduleinstance, $usage=0) {
        global $DB,$USER;
        $log = new \stdClass();
        $log->maiciid =  $this->maiciid;
        $log->cmid =  $this->cmid;
        $log->userid =  $USER->id;
        $log->prompt_tokens = $usage ? $usage->prompt_tokens : $usage;
        $log->completion_tokens =  $usage ? $usage->completion_tokens : $usage;
        $log->total_tokens =  $usage ? $usage->total_tokens : $usage;
        $log->message =  '';
        $log->completion =  '';
        $log->logging =  false;
        $log->apitype =  $moduleinstance->apitype;
        if($this->conversation_logging){
            $log->logging =  true;
            $log->message =  $this->message;
            $log->completion =  $completion;
        }
        $log->timecreated =  time();
        if($log->id = $DB->insert_record('maici_logs',$log)){

            if($this->check_user_completion($moduleinstance->completionaiexchanges, $log)){
                $course = get_course($moduleinstance->course);
                $completion = new \completion_info($course);
                $cm = get_coursemodule_from_instance('maici', $log->maiciid);
                if ($completion->is_enabled($cm)) {
                    $current = $completion->get_data($cm, false, $log->userid);
                    $current->completionstate = COMPLETION_COMPLETE;
                    $current->timemodified = time();
                    $completion->internal_set_data($cm, $current);
                }
            }
        }

        return $log->id;
    }

    private function check_user_completion($exchanges, $loginstance) {
        global $DB;

        $params = [];
        $params['cmid'] = $loginstance->cmid;
        $params['userid'] = $loginstance->userid;

        $select = "SELECT COUNT(chl.id) as records  ";
        $fields = " ";
        $from = " FROM {maici_logs} chl ";
        $join = "  ";
        $where = " WHERE chl.cmid=:cmid AND userid=:userid ";
        $groupby = "  ";

        $sql = $select . $fields . $from . $join . $where . $groupby;

        if($records = $DB->get_record_sql($sql,$params)){
            if($records->records >= $exchanges){
                return true;
            }
        }
        return false;
    }
}