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
 * External Class
 *
 * @package
 * @author Tay Moss <imc@tucc.ca>
 * @copyright 2024 CHURCHx at TUCC <https://churchx.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_maici; // Replace with your actual plugin namespace

defined('MOODLE_INTERNAL') || die();

class mod_maici_observer {
    public static function resource_created_handler(\core\event\course_module_created $event) {
        global $CFG;

        $fileinfo = $event->get_record_snapshot('course_modules', $event->objectid);
        $filecontext = \context_module::instance($fileinfo->id);

        // You might need to adjust this to get the actual file
        $fs = get_file_storage();
        $files = $fs->get_area_files($filecontext->id, 'mod_resource', 'content', false, 'timemodified', false);

        foreach ($files as $file) {
            if (!$file->is_directory()) {
                // Get file content
                $filecontent = $file->get_content();

                // Send the file content to the remote server
                $ch = curl_init();

                // Get file content
                $filecontent = $file->get_content();
                
                // Prepare the file for upload
                $tempfile = tempnam(sys_get_temp_dir(), 'moodle_upload_');
                file_put_contents($tempfile, $filecontent);

                // Send the file content to the remote server
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://prujuai:6500/uploadfile/");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'file' => new \CURLFile($tempfile, $file->get_mimetype(), $file->get_filename())
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                // Clean up temporary file
                unlink($tempfile);

            }
        }
    }
}

