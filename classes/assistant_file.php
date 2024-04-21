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

class assistant_file {

    protected $apikey;
    protected $moduleinstance;
    protected $contextid;

    /**
     * Initialize all the class properties that we'll need regardless of model
     * @param string model: The name of the model we're using
     * @param string message: The most recent message sent by the user
     * @param array history: An array of objects containing the history of the conversation
     * @param string block_settings: An object containing the instance-level settings if applicable
     */
    public function __construct($moduleinstance, $contextid) {

        $this->apikey = $moduleinstance->apikey ?:get_config('mod_maici','apikey');
        $this->moduleinstance = $moduleinstance;
        $this->contextid = $contextid;

    }


    /**
     * @param $filearea
     * @param $idemid
     * @return moodle_url|string
     * @throws coding_exception
     * @throws dml_exception
     */
    private function get_storage_file($filearea, $itemid){
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->contextid, 'mod_maici', $filearea, $itemid);
        if ($files) {
            foreach ($files as $file) {
                $requestdir = make_request_directory();
                $url = "{$requestdir}/testfile.txt";
                $url = !$file->copy_content_to($url) ? false : $url;
            }
            return $url;
        } else {
            return false;
        }
    }

    public function openai_assistantfiles_request()
    {
        if($file_path = $this->get_storage_file('assistantfile',$this->moduleinstance->id)){

            //check if file is already there
            $api_key = $this->moduleinstance->apikey ?:get_config('mod_maici','apikey');
            if(isset($this->moduleinstance->assistantfileid)
                && $this->is_assistantfile_listed($this->moduleinstance->assistantfileid,$api_key)){
                $this->delete_assistantfile($this->moduleinstance->assistantfileid);
            }

            $purpose = "assistants";

            $curl = curl_init();

            // Set the cURL options
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.openai.com/v1/files",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array(
                    'file' => new \CURLFile($file_path),
                    'purpose' => $purpose,
                ),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $api_key",
                ),
            ));

            // Execute the cURL request and get the response
            $response = curl_exec($curl);

            // Close cURL session
            curl_close($curl);

            // Decode the response JSON
            $result = json_decode($response, true);

            // Check if there is an error in the response
            if (isset($result['error'])) {
                throw new \Exception("OpenAI API error: " . $result['error']['message']);
            }

            return $result['id'];
        }
    }

    private function delete_assistantfile($api_key, $file_id) {
        $curl = curl_init();

        // Set the cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.openai.com/v1/files/$file_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $api_key",
            ),
        ));

        // Execute the cURL request and get the response
        $response = curl_exec($curl);

        // Close cURL session
        curl_close($curl);

        // Decode the response JSON
        $result = json_decode($response, true);

        // Check if there is an error in the response
        if (isset($result['error'])) {
            throw new \Exception("OpenAI API error: " . $result['error']['message']);
        }

        return $result;
    }

    private function is_assistantfile_listed($assistantfileid, $api_key) {

        $curl = curl_init();

        // Set the cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.openai.com/v1/files",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $api_key",
            ),
        ));

        // Execute the cURL request and get the response
        $response = curl_exec($curl);

        // Close cURL session
        curl_close($curl);

        if($result = json_decode($response, true)){
            foreach ($result['data'] as $file){
                if($file->id == $assistantfileid){
                    return true;
                }
            }
            return false;
        }
    }

}