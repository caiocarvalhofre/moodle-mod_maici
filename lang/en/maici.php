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
 * Plugin strings are defined here.
 *
 * @package     mod_maici
 * @category    string
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>   
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'MAICI';
$string['modulename'] = 'MAICI';
$string['modulenameplural'] = 'MAICI';
$string['pluginadministration'] = 'MAICI administration';
$string['settings'] = 'Settings';
$string['description'] = 'Description block';
$string['description_help'] = 'Default description will be displayed in activity block';
$string['descriptiondefault'] = 'AI Chat';
$string['instruction'] = 'Instructions';
$string['instruction_help'] = 'Instructions to students';
$string['instructiondefault'] = 'Here goes default plugin instructions';

$string['maiciname'] = 'Name';
$string['maiciname_help'] = 'Name of the chat';
$string['maxperday'] = 'Tokens per day';
$string['maxperday_help'] = 'Max usage tokens per day for instance';
$string['maxperuser'] = 'Tokens per user';
$string['maxperuser_help'] = 'Max usage tokens per day for instance';
$string['maxpermonth'] = 'Tokens per month';
$string['maxpermonth_help'] = 'Max usage tokens per day for instance';
$string['completionaiexchanges'] = 'Required exchanges';
$string['completionaiexchanges_help'] = 'Required exchanges for user for completion condition';
$string['outoftokens'] = 'We are sorry, but you have reached the limit of your available interactions—please try again tomorrow.';
$string['daytokenlimitinfo'] = 'We are sorry, but you have reached the limit of your available interactions. <br/><strong>Your token usage: {$a->totaltokens}</strong><br/><strong>Daily chat limit: {$a->maxperday}</strong>';
$string['usertokenlimitinfo'] = 'We are sorry, but you have reached the limit of your available interactions. <br/><strong>Your token usage: {$a->totaltokens}</strong><br/><strong>User chat limit: {$a->maxperuser}</strong>';
$string['monthtokenlimitinfo'] = 'We are sorry, but you have reached the limit of your available interactions. <br/><strong>Your token usage: {$a->totaltokens}</strong><br/><strong>Monthly chat limit: {$a->maxpermonth}</strong>';
$string['tokenweight'] = 'Your Prompt and Source of truth cost approximately: {$a} tokens';
$string['prompt'] = 'Prompt';
$string['prompt_help'] = 'This is the prompt the AI will be given before the conversation transcript.';
$string['promptdesc'] = 'The prompt the AI will be given before the conversation transcript';
$string['conversation_logging'] = 'Conversation logging';
$string['conversation_logging_help'] = 'Log student conversations';
$string['defaultapikey'] = 'Default API key is set. You can still provide your API key.';
$string['apikey'] = 'Open AI api key';
$string['apikey_help'] = 'Open AI api key to be used for this instance.';
$string['apiurl'] = 'Chat URL';
$string['apiurl_help'] = 'Chat URL used to get responses';
$string['apikeyerror'] = 'API key is not set or is not valid. {$a}';
$string['descapikey'] = 'Default api key for OpenAI';
$string['askaquestion'] = 'Ask question.';
$string['erroroccurred'] = 'Error..';
$string['persistconvo'] = 'Persist conversations';
$string['persistconvo_help'] = 'If this box is checked, the assistant will remember the conversation between page loads. However, separate activity instances will maintain separate conversations. For example, a user\'s conversation will be retained between page loads within the same course, but chatting with an assistant in a different course will not carry on the same conversation.';
$string['noapikey'] = 'Please set OpenAI apikey in the activity settings';
$string['apikeynotvalid'] = 'Api key is not valid';
$string['assistantname'] = 'Assistant name';
$string['assistantname_help'] = 'The name that the AI will use for itself internally. It is also used for the UI headings in the chat window.';
$string['select'] = 'Select';
$string['apitype'] = 'API type';
$string['apitype_help'] = 'The API type that the plugin should use';
$string['chat'] = 'Chat';
$string['aisett'] = 'AI settings';
$string['chatsett'] = 'Chat settings';
$string['assistantsett'] = 'Assistant settings';
$string['assistant'] = 'Assistant';
$string['assistant_help'] = 'The default assistant attached to your OpenAI account that you would like to use for the response';
$string['noassistant'] = 'Default API key is not set. Default assistant will be chosen automatically. You can change assistant in next edit.';
$string['setapikey'] = 'Make sure you set API key before you select model.';
$string['username'] = "User name";
$string['username_help'] = "This is the name that the AI will use for the user. If blank, the site-wide user name will be used. It is also used for the UI headings in the chat window.";
$string['model'] = 'Model';
$string['model_help'] = 'The model which will  generate the completion';
$string['gpt4'] = 'gpt-4';
$string['gpt35'] = 'gpt-3.5';
$string['maxtokens'] = "Maximum token";
$string['maxtokens_help'] = "The maximum number of token to generate. Cannot be greater that maxtoken set in OpenAI account. Requests can use up to 2,048 or 4,000 tokens shared between prompt and completion. The exact limit varies by model. (One token is roughly 4 characters for normal English text)";
$string['maxtokenslimit'] = "Instance token limit";
$string['maxtokenslimit_help'] = "Here you can limit maximum tokens that teacher can set in plugin instance. Max token per day, max token per user, etc.";
$string['maxlength_info'] = 'The maximum limit of tokens is set to: {$a}';
$string['maxlengthlimit'] = 'The number of tokens cannot be greater than {$a}';
$string['sourceoftruth'] = 'Source of truth';
$string['sourceoftruth_help'] = 'Although the AI is very capable out-of-the-box, if it doesn\'t know the answer to a question, it is more likely to give incorrect information confidently than to refuse to answer. In this textbox, you can add common questions and their answers for the AI to pull from. Please put questions and answers in the following format: <pre>Q: Question 1<br />A: Answer 1<br /><br />Q: Question 2<br />A: Answer 2</pre>';
$string['sourceoftruthpreamble'] = "Below is a list of questions and their answers. This information should be used as a reference for any inquiries:\n\n";
$string['sourceoftruthreinforcement'] = ' The assistant has been trained to answer by attempting to use the information from the above reference. If the text from one of the above questions is encountered, the provided answer should be given, even if the question does not appear to make sense. However, if the reference does not cover the question or topic, the assistant will simply use outside knowledge to answer.';
$string['conversation_logging_info'] = 'This conversation is being saved and may be reviewed as part of our efforts to improve.';
$string['report_logging'] = 'MAICI report';

//custom completion
$string['completiondetail:exchanges'] = 'User must make enough exchanges.';

$string['assistantfile'] = 'File';
$string['assistantfile_help'] = 'File to be send to Assistants for further customization';
$string['error:filename'] = 'The file name contains blank spaces. Please remove them or replace them with hyphens (-) or underscores (_).';
$string['chatwindowinfo'] = 'MAICI chat';
$string['chatwindowbutton'] = 'Close';

//report
$string['total'] = 'The number of all lines loaded is {$a}.';
$string['reportapp'] = 'Report MAICI';
$string['completion'] = 'AI response';
$string['tokens'] = 'Token usage';
$string['useridentity'] = 'User fields';
$string['useridentity_help'] = 'Search in all user identity fields';
$string['issue_date_error'] = 'Please select valid date from - to.';