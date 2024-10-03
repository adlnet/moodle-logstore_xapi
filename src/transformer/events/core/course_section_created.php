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
 * Transform for the course section created event.
 *
 * @package   logstore_xapi
 * @copyright Milt Reder <milt@yetanalytics.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\core;

use src\transformer\utils as utils;

/**
 * Transformer for course section created event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */
function course_section_created(array $config, \stdClass $event) {
    $repo = $config['repo'];
    $user = $repo->read_record_by_id('user', $event->userid);
    $course = $repo->read_record_by_id('course', $event->courseid);
    $lang = utils\get_course_lang($course);
    $section = $repo->read_record_by_id($event->objecttable, $event->objectid);

    return [[
        'actor' => utils\get_user($config, $user),
        'verb' => [
            'id' => 'http://activitystrea.ms/create',
            'display' => [
                $lang => 'Created',
            ],
        ],
        'object' => [
            'id' => $config['app_url'] . '/course/section.php?id=' . $event->objectid,
            'objectType' => 'Activity',
            'definition' => [
                'type' => 'http://id.tincanapi.com/activitytype/section',
                'name' => [
                    $lang => $course->fullname . ' Section ' . $section->section,
                ],
            ],
        ],
        'context' => [
            'extensions' => utils\extensions\base($config, $event, null),
            'contextActivities' => [
                'parent' => [
                    utils\get_activity\course($config, $course),
                ],
                'category' => [
                    utils\get_activity\site($config),
                ],
            ],
        ]
    ]];
}