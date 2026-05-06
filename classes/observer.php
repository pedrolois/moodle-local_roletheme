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

namespace local_roletheme;

/**
 * Event observers for the role theme plugin.
 *
 * @package   local_roletheme
 * @copyright 2026 Pedro Lois
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * On login, compute the theme and persist it to mdl_user.theme.
     */
    public static function user_loggedin(\core\event\user_loggedin $event): void {
        global $CFG;
        if (empty($CFG->allowuserthemes)) {
            return;
        }

        require_once($CFG->dirroot . '/local/roletheme/locallib.php');

        $userid = $event->relateduserid ?? $event->userid;
        \local_roletheme_apply_theme_for_user($userid);
    }

    /**
     * When roles change, recompute and persist the user's theme.
     */
    public static function role_changed(\core\event\base $event): void {
        global $CFG;
        if (empty($CFG->allowuserthemes)) {
            return;
        }

        require_once($CFG->dirroot . '/local/roletheme/locallib.php');

        $userid = $event->relateduserid ?? $event->userid ?? null;
        if (!$userid) {
            return;
        }

        \local_roletheme_apply_theme_for_user((int) $userid);
    }
}
