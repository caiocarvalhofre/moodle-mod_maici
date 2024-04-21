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
 * @package   mod_maici
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/maici/lib.php');

/**
 * Get real user token usage
 *
 * @param $usage
 * @param $instructiontokens
 * @return mixed
 */
function maici_get_chat_token_usage($usage, $instructiontokens) {

    if($usage->prompt_tokens < $instructiontokens){
        $usage->prompt_tokens = 0;
    }else{
        $usage->prompt_tokens -= $instructiontokens;
    }

    if($usage->total_tokens < $instructiontokens){
        $usage->total_tokens = 0;
    }else{
        $usage->total_tokens -= $instructiontokens;
    }

    return $usage;
}

/**
 * Get user token usage for assistant
 * Assistants doesn't provide info about token usage yet.
 *
 * @param $message
 * @param $completion_message
 * @return stdClass
 */
function maici_get_assistant_token_usage($message,$completion_message) {
    $usage = new stdClass();
    $usage->prompt_tokens = maici_count_tokens($message);
    $usage->completion_tokens = maici_count_tokens($completion_message);
    $usage->total_tokens = $usage->prompt_tokens + $usage->completion_tokens;
    return $usage;
}

/**
 * Sanitize string for use as filename
 *
 * @param $string
 * @return void
 */
function maici_sanitize_filename(&$string) {
    $unwanted_array =
        array( "/" => "_",
            'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O',
            'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U','Ť'=>'T',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a',
            'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o',
            'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u','ü' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', " " => "_",
            'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A');
    $string = strtr($string, $unwanted_array);
}

/**
 * Formats activity intro text
 *
 * @param string $module name of module
 * @param object $activity instance of activity
 * @param int $cmid course module id
 * @param bool $filter filter resulting html text
 * @return string
 */
function maici_format_instructions($moduleinstance, $cmid, $filter=true) {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");
    $context = context_module::instance($cmid);
    $options = array('noclean' => true, 'para' => false, 'filter' => $filter, 'context' => $context, 'overflowdiv' => true);
    $instructions = file_rewrite_pluginfile_urls($moduleinstance->instructions_submit, 'pluginfile.php', $context->id, 'mod_maici', 'intro', null);
    return trim(format_text($instructions, $moduleinstance->instructionsformat, $options, null));
}