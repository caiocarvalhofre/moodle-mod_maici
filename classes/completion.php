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
 * @copyright
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace mod_maici;
defined('MOODLE_INTERNAL') || die;

class completion {

    protected $apikey;
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

        $this->build_source_of_truth($block_settings['sourceoftruth']);
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
}