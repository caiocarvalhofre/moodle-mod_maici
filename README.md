# Moodle AI Chat Interface #

Moodle AI Chat Interface, abbreviated as "MAICI" and pronounced "MAY-see" is a chat-based learning activity providing students with natural language conversation with an an AI Agent. 

Using this plugin, an instructor or course designer can create one or several activities of this kind within the same course. This plugin supports an "initializing prompt" which is sent to the agent as well as any documents you wish to be uploaded at the beginning of the conversation thread.

The plugin works with both standard "ChatGPT" and you can access OpenAI "Assistants" which you have aready created in your account.

The amount of bandwidth back-and-forth with Open AI can be capped according to an approximate number of tokens.

Additionally, our plugin supports two types of completion criteria which can be used, or not, in any configuration:
1. Student must view activity
2. Student must complete a number of exchanges

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/maici

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##



This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
