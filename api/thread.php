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
 * API endpoint for retrieving thread history
 *
 * @package    mod_maici
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/mod/maici/lib.php');

global $DB, $PAGE;

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $CFG->wwwroot");
    die();
}

$body = json_decode(file_get_contents('php://input'), true);
$cm_id = clean_param($body['blockId'], PARAM_INT, true);
$thread_id = clean_param($body['threadId'], PARAM_NOTAGS, true);

$cm = get_coursemodule_from_id('maici', $cm_id, 0, false, MUST_EXIST);
$moduleinstance = $DB->get_record('maici', ['id' => $cm->instance]);
$apikey = $moduleinstance->apikey ?:get_config('mod_maici','apikey');

$curl = new \curl();
$curl->setopt(array(
    'CURLOPT_HTTPHEADER' => array(
        'Authorization: Bearer ' . $apikey,
        'Content-Type: application/json',
        'OpenAI-Beta: assistants=v2'
    ),
));

$response = $curl->get("https://api.openai.com/v1/threads/$thread_id/messages");
$response = json_decode($response);

if (property_exists($response, 'error')) {
    throw new \Exception($response->error->message);
}

$api_response = [];
$message_list = array_reverse($response->data);

foreach ($message_list as $message) {
    array_push($api_response, [
        "id" => $message->id,
        "role" => $message->role,
        "message" => $message->content[0]->text->value
    ]);
}

$api_response = json_encode($api_response);
echo $api_response;
