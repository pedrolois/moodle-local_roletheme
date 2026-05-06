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

defined('MOODLE_INTERNAL') || die();

/**
 * Hook callbacks for local_roletheme.
 *
 * @package   local_roletheme
 * @copyright 2026 Pedro Lois
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Inject the JavaScript module that keeps the preferred theme field read-only.
     *
     * @param \core\hook\output\before_http_headers $hook
     * @return void
     */
    public static function before_http_headers(\core\hook\output\before_http_headers $hook): void {
        global $PAGE, $SCRIPT;

        if ($SCRIPT !== '/user/edit.php' && $SCRIPT !== '/user/editadvanced.php') {
            return;
        }

        $PAGE->requires->js_call_amd('local_roletheme/locktheme', 'init', [[
            'lockNote' => get_string('lockthemenote', 'local_roletheme'),
        ]]);
    }
}
