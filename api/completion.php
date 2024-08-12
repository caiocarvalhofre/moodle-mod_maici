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
 * API endpoint for retrieving GPT completion
 *
 * @package    mod_maici
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \mod_maici\completion;

require_once('../../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/mod/maici/lib.php');
require_once($CFG->dirroot . '/mod/maici/locallib.php');

global $DB, $PAGE;

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $CFG->wwwroot");
    die();
}

$body = json_decode(file_get_contents('php://input'), true);
$message = clean_param($body['message'], PARAM_NOTAGS);
$history = clean_param_array($body['history'], PARAM_NOTAGS, true);
$cm_id = clean_param($body['blockId'], PARAM_INT, true);
$thread_id = clean_param($body['threadId'], PARAM_NOTAGS, true);

// So that we're not leaking info to the client like API key, the block makes an API request including its ID
// Then we can look up that specific block to pull out its config data
$cm = get_coursemodule_from_id('maici', $cm_id, 0, false, MUST_EXIST);
$moduleinstance = $DB->get_record('maici', ['id' => $cm->instance]);

$context = context_module::instance($cm->id);
$PAGE->set_context($context);

$block_settings = [];
$block_settings ['sourceoftruth'] = $moduleinstance->sourceoftruth;
$block_settings ['prompt'] = $moduleinstance->prompt;
$block_settings ['temperature'] = 0.5;
$block_settings ['topp'] = 1;
$block_settings ['frequency'] = 1;
$block_settings ['presence'] = 1;
$block_settings ['username'] = $moduleinstance->username;
$block_settings ['assistantname'] = $moduleinstance->assistantname;
$block_settings ['assistant'] = $moduleinstance->assistant;
$block_settings ['model'] = $moduleinstance->model;
$block_settings ['apikey'] = $moduleinstance->apikey ?:get_config('mod_maici','apikey');
$block_settings ['apiurl'] = $moduleinstance->apiurl;
$block_settings ['maxlength'] = get_config('mod_maici', 'maxtokens');
$block_settings ['cmid'] = $cm_id;
$block_settings ['maiciid'] = $moduleinstance->id;
$block_settings ['conversation_logging'] = $moduleinstance->conversation_logging;

$engine_class='\mod_maici\completion\\'.$moduleinstance->apitype;

$completion = new $engine_class(...[$moduleinstance->model, $message, $history, $block_settings, $thread_id]);
$response = $completion->create_completion($PAGE->context);

if(is_object($response) && property_exists($response,'error')){
    // Return error response
    http_response_code(400); // Bad request
    echo json_encode(array("error" => $response->error));
    exit;
}

if($moduleinstance->apitype=='chat'){
    $message_response = [
        "id" => $response->id,
        "message" => $response->choices[0]->message->content
    ];

    // Format the markdown of each completion message into HTML.
    $message_response["message"] = format_text($message_response["message"], FORMAT_MARKDOWN, ['context' => $context]);
    $completion_message = $message_response["message"];
    $message_response = json_encode($message_response);

    $usage = maici_get_chat_token_usage($response->usage,$moduleinstance->instructiontokens);
    $completion->log_conversation($completion_message, $moduleinstance, $usage);

    echo $message_response;
}else{
    // Format the markdown of each completion message into HTML.
    $response["message"] = format_text($response["message"], FORMAT_MARKDOWN, ['context' => $context]);
    $completion_message = $response["message"];
    $usage = maici_get_assistant_token_usage($message,$completion_message);
    $response = json_encode($response);

    $completion->log_conversation($completion_message, $moduleinstance, $usage);

    echo $response;
}
