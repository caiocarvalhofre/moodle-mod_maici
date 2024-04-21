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
 *
 * @package     mod_maici
 * @category    admin
* @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace mod_maici\table;

use lang_string;
use moodle_exception;
use moodle_url;
use core_user\output\status_field;
use stdClass;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Class for the displaying the table.
 *
 * @package     report_logging
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_logging extends \table_sql {

    public $search;
    private $cm;
    private $context;
    private $maici;
    public $extrafields;
    private \core_user\fields $foridentity;
    /**
     * @var string[]
     */
    private array $identityfields;
    private stdClass $userfields;

    /**
     * Sets up the table.
     *
     * @param string|array $search The search string(s)
     */
    public function init_table($cm, $maici, $search = null) {
        global $CFG;
        $this->context = \context_module::instance($cm->id);
        $this->cm = $cm;
        $this->maici = $maici;
        $this->search = $search;
        $this->foridentity = \core_user\fields::for_identity(\context_system::instance())->with_userpic();
        $this->identityfields = \core_user\fields::for_identity(\context_system::instance())->get_required_fields();
        $this->userfields= $this->foridentity->get_sql('u',true);
    }

    /**
     * Render the table.
     *
     * @param int $pagesize Size of page for paginated displayed table.
     * @param bool $useinitialsbar Whether to use the initials bar which will only be used if there is a fullname column defined.
     * @param string $downloadhelpbutton
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton = '') {
        global $DB;
        $extrafields = [];
        $this->downloadable = true;
        $this->set_attribute('class', 'table-bordered');
        $columns = [];
        $headers = [];
        $columns[] = 'fullname';
        $headers[] = get_string('fullname');
        $columns[] = 'email';
        $headers[] = get_string('email');
        $columns[] = 'message';
        $headers[] = get_string('message');
        $columns[] = 'completion';
        $headers[] = get_string('completion', 'mod_maici');
        $columns[] = 'apitype';
        $headers[] = get_string('apitype', 'mod_maici');
        $columns[] = 'tokens';
        $headers[] = get_string('tokens', 'mod_maici');
        $columns[] = 'timecreated';
        $headers[] = get_string('timecreated');

        $this->define_columns($columns);
        $this->define_headers($headers);

        // Make this table sorted by last name by default.
        $this->sortable(true, 'starttime');
        $this->extrafields = $extrafields;

        parent::out($pagesize, $useinitialsbar, $downloadhelpbutton);
    }

    /**
     * @param $data
     * @return lang_string|string
     * @throws \dml_exception
     * @throws moodle_exception
     */
    public function col_fullname($data) {
        global $DB,$OUTPUT;
        $user=$DB->get_record('user',['id'=>$data->userid]);
        $userurl = new moodle_url('/user/view.php', array('id' => $data->userid));
        if ($this->is_downloading()) {
            return fullname($user);
        }else{
            return \html_writer::link($userurl->out(),  $OUTPUT->user_picture($user, array('size' => 35, 'includefullname' => true, 'link' => false)));
        }
        return $data->firstname . ' ' . $data->lastname;
    }

    /**
     * Generate column
     *
     * @param $data
     * @return lang_string|string
     * @throws \coding_exception
     */
    public function col_email($data) {
        return $data->email;
    }

    /**
     * Generate column
     *
     * @param $data
     * @return lang_string|string
     * @throws \coding_exception
     */
    public function col_tokens($data) {
        return $data->total_tokens==0?'-':$data->total_tokens;
    }

    /**
     * Generate column
     *
     * @param $data
     * @return lang_string|string
     * @throws \coding_exception
     */
    public function col_apitype($data) {
        return $data->apitype;
    }

    /**
     * Generate column
     *
     * @param $data
     * @return lang_string|string
     * @throws \coding_exception
     */
    public function col_message($data) {
        return \html_writer::div($data->message,'overflow-hidden');
    }

    /**
     * Generate column
     *
     * @param $data
     * @return lang_string|string
     * @throws \coding_exception
     */
    public function col_completion($data) {
        return \html_writer::div($data->completion,'overflow-hidden');
    }

    /**
     * Generate slot sloteventd events column
     *
     * @param $data
     * @return lang_string|string
     * @throws \coding_exception
     */
    public function col_timecreated($data) {
        return userdate($data->timecreated, '%Y-%m-%d %H:%M:%S');

    }

    /**
     * This function is used for the extra user fields.
     *
     * These are being dynamically added to the table so there are no functions 'col_<userfieldname>' as
     * the list has the potential to increase in the future and we don't want to have to remember to add
     * a new method to this class. We also don't want to pollute this class with unnecessary methods.
     *
     * @param $colname
     * @param $data
     * @return lang_string|string|null
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function other_cols($colname, $data) {
        global $DB, $OUTPUT;
        // Do not process if it is not a part of the extra fields.
        if (!in_array($colname, $this->extrafields)) {
            return '';
        }

        return $data->{$colname};
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        //Count all users.
        $total = $this->count_data();
            $this->pagesize($pagesize, $total);

        //Get users data.
        $rawdata = $this->get_data($this->get_sql_sort(), $this->get_page_start(), $this->get_page_size());

        $this->rawdata = [];
        foreach ($rawdata as $data) {
            $this->rawdata[$data->id] = $data;
        }

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars(true);
        }
    }

    /**
     * Override the table show_hide_link to not show for select column.
     *
     * @param string $column the column name, index into various names.
     * @param int $index numerical index of the column.
     * @return string HTML fragment.
     */
    protected function show_hide_link($column, $index) {
        return '';
    }

    /**
     * Guess the base url for the participants table.
     */
    public function guess_base_url(): void {
        $this->baseurl = new moodle_url('/mod/maici/view.php', ['id'=>$this->cm->id]);
    }

    /**
     * Query users for table.
     */
    public function count_data() {
        global $DB;
        $params= [];
        $params['maiciid'] = $this->maici->id;
        $select = "SELECT COUNT(ml.id) ";
        $fields = " ";

        $from = " FROM {maici_logs} ml ";
        $join = " LEFT JOIN {maici} m ON m.id=ml.maiciid ";
        $join .= "LEFT JOIN {user} u ON u.id = ml.userid ";
        $where = "WHERE ml.maiciid=:maiciid AND ml.message <> '' ";

        $this->get_where_conditions($where,$params,$join);

        $groupby = "";
        $orderby = "";
        $sql = $select . $fields . $from . $join . $where . $groupby . $orderby;
        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Query users for table.
     */
    public function get_data($sort, $start, $size) {
        global $DB;
        $params= [];
        $params['maiciid'] = $this->maici->id;
        $select = "SELECT ";
        $fields = " ml.*,
                    u.firstname,
                    u.lastname,
                     u.email ";

        $from = " FROM {maici_logs} ml ";
        $join = " LEFT JOIN {maici} m ON m.id=ml.maiciid ";
        $join .= "LEFT JOIN {user} u ON u.id = ml.userid ";
        $where = "WHERE ml.maiciid=:maiciid AND ml.message <> '' ";
        $groupby = "";

        if ($sort) {
            $orderby = "ORDER BY {$sort} ";
        } else {
            $orderby = "ORDER BY ml.timecreated ";
        }

        $this->get_where_conditions($where,$params,$join);

        $sql = $select . $fields . $from . $join . $where . $groupby . $orderby;
        return $DB->get_records_sql($sql, $params, $start, $size);
    }

    /**
     * Where condition funciton for implementation of filters or search
     *
     * @param $where
     * @param $params
     * @return void
     */
    private function get_where_conditions(&$where,&$params, &$join){
        global $DB;

        if(property_exists($this->search,'useridentity')&& !empty($this->search->useridentity) && strlen($this->search->useridentity)>=3){
            $where .= "AND (";
            $where .= $DB->sql_like('u.firstname', ':firstname',false)." OR ";
            $params['firstname'] = "%{$this->search->useridentity}%";
            $where .= $DB->sql_like('u.lastname', ':lastname',false)." OR ";
            $params['lastname'] = "%{$this->search->useridentity}%";


            foreach ($this->identityfields as $identityfield) {
                if (strpos($identityfield, 'profile_field_') === 0) {
                    $fieldname = str_replace('profile_field_', '', $identityfield);
                    $partialsql="(SELECT dd.data 
            FROM {user_info_data} dd 
            LEFT JOIN {user_info_field} ff ON dd.fieldid=ff.id 
            WHERE dd.userid = u.id AND ff.shortname LIKE '{$fieldname}')";
                    $where .= $DB->sql_like($partialsql, ':'.$fieldname,false)." OR ";
                    $params[''.$fieldname] = "%{$this->search->useridentity}%";
                } else {
                    $where .= $DB->sql_like('u.'.$identityfield, ':'.$identityfield,false)." OR ";
                    $params[$identityfield] = "%{$this->search->useridentity}%";
                }
            }
            $where= rtrim($where,'OR ');
            $where .= ") ";
        }

        if(property_exists($this->search,'dateenabled')&& $this->search->dateenabled==1){
            if($this->search->datefrom==$this->search->dateto){
                $this->search->dateto+=86400;
            }
            $where .= " AND ml.timecreated BETWEEN :datefrom AND :dateto ";
            $params['datefrom'] = $this->search->datefrom;
            $params['dateto'] = $this->search->dateto;


        }

    }
}
