<?php
defined('MOODLE_INTERNAL') || die();

$observers = array(
    array (
        'eventname' => '\core\event\course_module_created',
        'callback'  => '\mod_maici\mod_maici_observer::resource_created_handler',
        'internal'  => false,
        'priority'  => 1000,
    ),
);