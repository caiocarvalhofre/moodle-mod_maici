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
 * Renderable that initialises the grading "app".
 *
 * @package     mod_maici
 * @category    output
 * @author       <>
 * 
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_maici\output;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use renderable;
use templatable;
use stdClass;

require_once($CFG->dirroot . '/mod/maici/locallib.php');

/**
 * Grading app renderable.
 *
 * @package    mod_maici
 * @since      Moodle 3.1
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aichat implements templatable, renderable {

    public $moduleinstance;

    public $cmid;

    public $intro;
    /**
     * @var \lang_string|string
     */
    public $tokenlimitinfo;

    public function __construct($moduleinstance,$cmid,$intro=null) {
        $this->moduleinstance = $moduleinstance;
        $this->cmid = $cmid;
        $this->intro = $intro;
    }

    /**
     * Export this class data as a flat list for rendering in a templates.
     *
     * @param renderer_base $output The current page renderer.
     * @return stdClass - Flat list of exported data.
     */
    public function export_for_template($output) {
        global $PAGE,$OUTPUT;
        $data = new stdClass();
        $apikey = $this->moduleinstance->apikey ?:get_config('mod_maici','apikey');

        if(empty($apikey)) {
            $data->displayactivity = false;
            $data->info = get_string('modulenotset','mod_maici');
        }else{
            if($this->maici_validate_user()){
                $PAGE->requires->js_call_amd('mod_maici/lib', 'init', [[
                    'blockId' => $this->cmid,
                    'api_type' => $this->moduleinstance->apitype,
                    'persistConvo' => $this->moduleinstance->persistconvo,
                    'usertokenvalidation' => true
                ]]);

                $data->displayactivity = true;
                $data->instructions = maici_format_instructions($this->moduleinstance,$this->cmid);
                $data->intro = $this->intro;
                $data->username = $this->moduleinstance->username;
                $data->assistantname = $this->moduleinstance->assistantname;
                $data->conversation_logging = $this->moduleinstance->conversation_logging ? $OUTPUT->container(get_string('conversation_logging_info','mod_maici'), 'alert alert-info') : '';
            }else{
                $data->displayactivity = false;
                $data->info = $OUTPUT->container($this->tokenlimitinfo, 'alert alert-warning');
            }
        }

        return $data;
    }

    public function maici_validate_user() {
        global $DB,$USER;

        $date = new \DateTime();
        $date->setTimestamp(time());
        $date->setTime(0, 0, 0);
        $startOfDayTimestamp = $date->getTimestamp();

        $date->setTime(23, 59, 59);
        $endOfDayTimestamp = $date->getTimestamp();

        $params = [];
        $params['cmid'] = $this->cmid;
        $params['startoftheday'] = $startOfDayTimestamp;
        $params['endoftheday'] = $endOfDayTimestamp;

        $maxtokens = get_config('mod_maici', 'maxtokenslimit');
        $maxperday = $this->moduleinstance->maxperday ?: $maxtokens;
        $maxperuser = $this->moduleinstance->maxperuser ?: $maxtokens;
        $maxpermonth = $this->moduleinstance->maxpermonth ?: $maxtokens;

        // Instance limit for a day
        if($maxperday){
            $select = "SELECT SUM(chl.total_tokens) as totaltokens";
            $fields = " ";
            $from = " FROM {maici_logs} chl ";
            $join = "  ";
            $where = " WHERE chl.cmid=:cmid AND chl.timecreated < :endoftheday AND chl.timecreated > :startoftheday ";
            $groupby = "  ";

            $sql = $select . $fields . $from . $join . $where . $groupby;

            if(($total_tokens = $DB->get_record_sql($sql,$params)->totaltokens) && $total_tokens >= $maxperday){
                $a = (object)['maxperday' => $maxperday, 'totaltokens' => $total_tokens];
                $this->tokenlimitinfo = get_string('daytokenlimitinfo','maici',$a);
                return false;
            }
        }

        //instance limit for user for a day
        if($maxperuser){
            $params['userid'] = $USER->id;
            $where .= " AND userid=:userid ";
            $sql = $select . $fields . $from . $join . $where . $groupby;

            if(($total_tokens = $DB->get_record_sql($sql,$params)->totaltokens) && $total_tokens >= $maxperuser){
                $a = (object)['maxperuser' => $maxperuser, 'totaltokens' => $total_tokens];
                $this->tokenlimitinfo = get_string('usertokenlimitinfo','maici',$a);
                return false;
            }
        }

        //instance limit for month
        if($maxpermonth){
            $date = new \DateTime();
            $date->setTimestamp(time());
            $date->modify('first day of this month');
            $firstDayOfMonth = $date->getTimestamp();

            $date->modify('last day of this month');
            $lastDayOfMonth = $date->getTimestamp();

            $params = [];
            $params['cmid'] = $this->cmid;
            $params['firstdayofmonth'] = $firstDayOfMonth;
            $params['lastdayofmonth'] = $lastDayOfMonth;

            $select = "SELECT SUM(chl.total_tokens) as totaltokens";
            $fields = " ";
            $from = " FROM {maici_logs} chl ";
            $join = "  ";
            $where = " WHERE chl.cmid=:cmid AND chl.timecreated < :lastdayofmonth AND chl.timecreated > :firstdayofmonth ";
            $groupby = "  ";

            $sql = $select . $fields . $from . $join . $where . $groupby;

            if(($total_tokens = $DB->get_record_sql($sql,$params)->totaltokens) && $total_tokens >= $maxpermonth){
                $a = (object)['maxpermonth' => $maxpermonth, 'totaltokens' => $total_tokens];
                $this->tokenlimitinfo = get_string('monthtokenlimitinfo','maici',$a);
                return false;
            }
        }

        return true;
    }
}
