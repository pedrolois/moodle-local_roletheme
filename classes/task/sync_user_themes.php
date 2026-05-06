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

namespace local_roletheme\task;

use core\task\scheduled_task;

/**
 * Recompute stored user themes in bulk.
 *
 * @package   local_roletheme
 * @copyright 2026 Pedro Lois
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_user_themes extends scheduled_task {
    /**
     * Return the task name.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('task_syncuserthemes', 'local_roletheme');
    }

    /**
     * Execute the task.
     */
    public function execute(): void {
        global $CFG, $DB;

        if (empty($CFG->allowuserthemes)) {
            mtrace(get_string('status_allowuserthemesdisabled', 'local_roletheme'));
            return;
        }

        require_once($CFG->dirroot . '/local/roletheme/locallib.php');

        $users = $DB->get_recordset_select('user', 'deleted = 0 AND suspended = 0', [], '', 'id');
        $count = 0;
        foreach ($users as $user) {
            $theme = \local_roletheme_apply_theme_for_user((int) $user->id);
            $displaytheme = $theme === null || $theme === ''
                ? get_string('simulationnothemeset', 'local_roletheme')
                : $theme;
            mtrace(get_string('taskuserupdated', 'local_roletheme', (object) [
                'userid' => $user->id,
                'theme' => $displaytheme,
            ]));
            $count++;
        }
        $users->close();

        mtrace(get_string('taskusersprocessed', 'local_roletheme', $count));
    }
}
