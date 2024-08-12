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
 * The main mod_maici configuration form.
 *
 * @package     mod_maici
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>   
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_maici
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>   
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_maici_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('maiciname', 'mod_maici'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'maiciname', 'mod_maici');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Instruction text
        $filemanager_options = array('subdirs' => 1, 'accepted_types' => array('.jpeg', '.jpg'), 'maxbytes' => $CFG->maxbytes,
            'maxfiles' => -1, 'changeformat' => 1, 'context' => $this->context, 'noclean' => 1, 'trusttext' => 0);

        $mform->addElement('editor', 'instructions', get_string('instruction', 'mod_maici'), null, $filemanager_options);
        $mform->setType('instructions', PARAM_RAW);
        $mform->addHelpButton('instructions', 'instruction', 'mod_maici');

        //OpenAI settings
        $defaultapikey = get_config('mod_maici','apikey');
        if($defaulassistants = maici_fetch_assistants_array($defaultapikey)){
            $mform->addElement('hidden', 'defaultapikey',$defaultapikey);
            $mform->setType('defaultapikey', PARAM_TEXT);

            $mform->addElement('html', get_string('defaultapikey','mod_maici').'</br></br>');
        }

        $mform->addElement('password', 'apikey', get_string('apikey', 'mod_maici'),array('size'=>45));
        $mform->setType('apikey', PARAM_TEXT);
        $mform->addHelpButton('apikey', 'apikey', 'mod_maici');
        if(!$defaulassistants){
            $mform->addRule('apikey', null, 'required', null, 'client');
        }

        $mform->addElement('text', 'apiurl', get_string('apiurl', 'mod_maici'),array('size'=>45));
        $mform->setType('apiurl', PARAM_TEXT);
        $mform->addHelpButton('apiurl', 'apiurl', 'mod_maici');

        $mform->addElement('text', 'assistantname', get_string('assistantname','mod_maici'),array('size'=>40));
        $mform->setType('assistantname', PARAM_TEXT);
        $mform->addRule('assistantname', null, 'required', null, 'client');
        $mform->setDefault('assistantname', 'Assistant');
        $mform->addHelpButton('assistantname', 'assistantname', 'mod_maici');

        $mform->addElement('text', 'username', get_string('username','mod_maici'),array('size'=>40));
        $mform->setType('username', PARAM_TEXT);
        $mform->addRule('username', null, 'required', null, 'client');
        $mform->setDefault('username', 'User');
        $mform->addHelpButton('username', 'username', 'mod_maici');

        $mform->addElement('checkbox', 'conversation_logging', get_string('conversation_logging', 'mod_maici'));
        $mform->addHelpButton('conversation_logging', 'conversation_logging', 'mod_maici');

        $mform->addElement('html', get_string('maxlength_info','mod_maici',get_config('mod_maici', 'maxtokenslimit')).'</br></br>');

        $mform->addElement('text', 'maxperday', get_string('maxperday','mod_maici'));
        $mform->setType('maxperday', PARAM_INT);
        $mform->addHelpButton('maxperday', 'maxperday', 'mod_maici');

        $mform->addElement('text', 'maxperuser', get_string('maxperuser','mod_maici'));
        $mform->setType('maxperuser', PARAM_INT);
        $mform->addHelpButton('maxperuser', 'maxperuser', 'mod_maici');

        $mform->addElement('text', 'maxpermonth', get_string('maxpermonth','mod_maici'));
        $mform->setType('maxpermonth', PARAM_INT);
        $mform->addHelpButton('maxpermonth', 'maxpermonth', 'mod_maici');

        $mform->addElement('select', 'apitype', get_string('apitype', 'mod_maici'), ['chat' => 'chat', 'assistant' => 'assistant']);

        $mform->addElement('header', 'aisettings', get_string('aisett','mod_maici'));

        /////------------CHAT
        $models = maici_get_models()['models'];
        $mform->addElement('select', 'model', get_string('model', 'mod_maici'),$models);
        $mform->addHelpButton('model', 'model', 'mod_maici');

        $mform->addElement('textarea', 'prompt', get_string('prompt', 'mod_maici'),
            array('rows' => 8, 'cols' => 41));
        $mform->setType('prompt', PARAM_TEXT);
        $mform->addHelpButton('prompt', 'prompt', 'mod_maici');

        $mform->addElement('textarea', 'sourceoftruth', get_string('sourceoftruth', 'mod_maici'),
            array('rows' => 8, 'cols' => 41));
        $mform->setType('sourceoftruth', PARAM_TEXT);
        $mform->addHelpButton('sourceoftruth', 'sourceoftruth', 'mod_maici');

        if(isset($this->current->id) && !empty($this->current->id)){
            global $DB;
            $instance = $DB->get_record('maici',['id'=>$this->current->id]);
            $apikey = $instance->apikey;
            if($tokens = maici_count_tokens($instance->sourceoftruth) + maici_count_tokens($instance->prompt)){
                $mform->addElement('html', get_string('tokenweight','mod_maici',$tokens).'</br></br>');
            }
        }else{
            $apikey = null;
        }

        $mform->hideIf('model','apitype','nq','chat');
        $mform->hideIf('prompt','apitype','nq','chat');
        $mform->hideIf('sourceoftruth','apitype','nq','chat');

        /////------------Assistant
        if(($assistants = maici_fetch_assistants_array($apikey)) || $defaulassistants){
            $mform->addElement('select', 'assistant', get_string('assistant', 'mod_maici'),$assistants?:$defaulassistants);
            $mform->addHelpButton('assistant', 'assistant', 'mod_maici');
        }else{
            $mform->addElement('html', get_string('noassistant','mod_maici').'</br></br>');
        }

        $mform->addElement('checkbox', 'persistconvo', get_string('persistconvo', 'mod_maici'));
        $mform->addHelpButton('persistconvo', 'persistconvo', 'mod_maici');

        $filemanager_options = array();
        $filemanager_options['accepted_types'] = ['.txt','.pdf','.csv'];
        $filemanager_options['maxbytes'] = 0;
        $filemanager_options['subdirs'] = 0;
        $filemanager_options['maxfiles'] = 1;
        $filemanager_options['mainfile'] = false;

        $mform->addElement('filemanager', 'assistantfile',get_string('assistantfile', 'mod_maici'), null, $filemanager_options);
        $mform->addHelpButton('assistantfile', 'assistantfile', 'mod_maici');

        $mform->hideIf('assistant','apitype','eq','chat');
        $mform->hideIf('assistantfile','apitype','eq','chat');
        $mform->hideIf('persistconvo','apitype','eq','chat');

        $mform->setExpanded('aisettings');
        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    public function add_completion_rules() {
        $mform = & $this->_form;
        $group = [];

        $group[] = $mform->createElement('checkbox', 'completionai', '', get_string('completiondetail:exchanges', 'mod_maici'));
        $mform->setType('completionai', PARAM_INT);

        $group[] = $mform->createElement('text', 'completionaiexchanges', get_string('completionaiexchanges','mod_maici'));
        $mform->setType('completionaiexchanges', PARAM_INT);

        $mform->addGroup(
            $group,
            'completionaidgroup',
            '',
            [' '],
            false
        );
        $mform->hideIf('completionaiexchanges', 'completionai', 'notchecked');
        $mform->setDefault('completionaiexchanges', 1);
        $mform->setType('completionaiexchanges', PARAM_INT);
        /* This ensures the elements are disabled unless completion rules are enabled */
        return ['completionaidgroup'];
    }

    function completion_rule_enabled($data) {
        return  !empty($data['completionai']);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $maxtokens = get_config('mod_maici', 'maxtokenslimit');

        if (isset($data['maxperday']) && $data['maxperday'] > $maxtokens) {
            $errors['maxperday'] = get_string('maxlengthlimit', 'mod_maici',$maxtokens);
        }

        if (isset($data['maxperuser']) && $data['maxperuser'] > $maxtokens) {
            $errors['maxperuser'] = get_string('maxlengthlimit', 'mod_maici',$maxtokens);
        }

        if (isset($data['maxpermonth']) && $data['maxpermonth'] > $maxtokens) {
            $errors['maxpermonth'] = get_string('maxlengthlimit', 'mod_maici',$maxtokens);
        }

        if ((!isset($data['defaultapikey']) && empty($data['apikey']))
            || (!empty($data['apikey']) && ($errormessage = maici_validate_apikey($data['apikey'])) !== true)) {
            $errors['apikey'] = get_string('apikeyerror', 'mod_maici', $errormessage);
        }

        $fs = get_file_storage();
        if (isset($data['assistantfile']) && $files = $fs->get_area_files($this->context->id, 'mod_maici', 'draft',$data['assistantfile'], 'sortorder, id', false)) {
            foreach ($files as $file) {
                if(preg_match('/\s/',$file->get_filename())){
                    $errors['assistantfile'] = get_string('error:filename', 'mod_maici');
                }
            }
        }

        return $errors;
    }

    /**
     * Enforce defaults here.
     *
     * @param array $defaultvalues Form defaults
     * @return void
     * */
    public function data_preprocessing(&$defaultvalues) {

        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('instructions_submit');
            $defaultvalues['instructions']['text'] =
                file_prepare_draft_area($draftitemid, $this->context->id,
                    'mod_maici', 'instructions_submit', false,
                    maici_get_editor_options($this->context),
                    $defaultvalues['instructions_submit']);

            $defaultvalues['instructions']['format'] = $defaultvalues['instructionsformat'];
            $defaultvalues['instructions']['itemid'] = $draftitemid;

            //assistant file
            $defaultvalues = (object) $defaultvalues;

            $draftitemid = file_get_submitted_draft_itemid('assistantfile');
            file_prepare_draft_area($draftitemid,  $this->context->id, 'mod_maici', 'assistantfile', $this->current->instance, array('subdirs'=>false));
            $defaultvalues->assistantfile = $draftitemid;
        } else {
            $draftitemid = file_get_submitted_draft_itemid('instructions');

            file_prepare_draft_area($draftitemid, null, 'mod_maici', 'instructions_submit', false);
            $defaultvalues['instructions']['text'] = '';
            $defaultvalues['instructions']['format'] = editors_get_preferred_format();
            $defaultvalues['instructions']['itemid'] = $draftitemid;
        }
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        error_log('DATA URL '.$data->apiurl);
        parent::data_postprocessing($data);
        if (isset($data->instructions)) {
            $data->instructionsformat = $data->instructions['format'];
            $data->instructions_submit = $data->instructions['text'];
        }

        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;

            if (!$autocompletion) {
                $data->completionai = 0;
            }

            if(!property_exists($data,'completionai')){
                $data->completionai = 0;
            }

        }

        if (!empty($data->apikey) && !isset($data->assistant)) {
            $assistant = maici_fetch_assistants_array($data->apikey);
            $data->assistant = count($assistant) ? key($assistant) : null;
        }

        if (empty($data->apikey) && isset($data->defaultapikey)) {
            $assistant = maici_fetch_assistants_array($data->defaultapikey);
            $data->assistant = count($assistant) ? key($assistant) : null;
        }
    }
}
