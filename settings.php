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
 * Admin settings for local_roletheme.
 *
 * @package   local_roletheme
 * @author    Pedro Luis Garcia Leiva
 * @copyright 2026 Pedro Lois {@link https://github.com/pedrolois}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/roletheme/classes/admin_setting_priority.php');
require_once($CFG->dirroot . '/local/roletheme/classes/admin_setting_cohortmap.php');

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_roletheme', get_string('pluginname', 'local_roletheme'));

    $settings->add(new admin_setting_heading(
        'local_roletheme/rolesettings',
        get_string('setting_roleheading', 'local_roletheme'),
        get_string('setting_roleheading_desc', 'local_roletheme')
    ));

    // Build theme options from installed themes.
    $themes = [];
    foreach (\core_component::get_plugin_list('theme') as $name => $path) {
        $themes[$name] = $name;
    }
    // First option clears mdl_user.theme.
    $themeoptions = ['' => get_string('defaultoption', 'local_roletheme')] + $themes;

    // Roles to map.
    $roleshortnames = ['siteadmin', 'manager', 'editingteacher', 'teacher', 'student', 'guest'];

    foreach ($roleshortnames as $r) {
        $settings->add(new admin_setting_configselect(
            "local_roletheme/rolemap_{$r}",
            get_string('setting_rolemap', 'local_roletheme', $r),
            get_string('setting_rolemap_desc', 'local_roletheme', $r),
            '',
            $themeoptions
        ));
    }

    // Priority order: first match wins.
    $settings->add(new \local_roletheme\admin_setting_priority(
        'local_roletheme/priority',
        get_string('setting_priority', 'local_roletheme'),
        get_string('setting_priority_desc', 'local_roletheme'),
        local_roletheme_get_default_priority_csv()
    ));

    // Optionally restrict role resolution to system context.
    $settings->add(new admin_setting_configcheckbox(
        'local_roletheme/systemcontextonly',
        get_string('setting_systemcontextonly', 'local_roletheme'),
        get_string('setting_systemcontextonly_desc', 'local_roletheme'),
        1
    ));

    $settings->add(new admin_setting_heading(
        'local_roletheme/cohortsettings',
        get_string('setting_cohortheading', 'local_roletheme'),
        get_string('setting_cohortheading_desc', 'local_roletheme')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_roletheme/enablecohorts',
        get_string('setting_enablecohorts', 'local_roletheme'),
        get_string('setting_enablecohorts_desc', 'local_roletheme'),
        0
    ));

    $settings->add(new admin_setting_configtext(
        'local_roletheme/cohortpriority',
        get_string('setting_cohortpriority', 'local_roletheme'),
        get_string('setting_cohortpriority_desc', 'local_roletheme'),
        local_roletheme_get_default_cohort_priority_csv()
    ));

    $settings->add(new \local_roletheme\admin_setting_cohortmap(
        'local_roletheme/cohortmap',
        get_string('setting_cohortmap', 'local_roletheme'),
        get_string('setting_cohortmap_desc', 'local_roletheme'),
        '',
        PARAM_RAW,
        4,
        40
    ));

    $ADMIN->add('localplugins', $settings);

    $ADMIN->add('localplugins', new admin_externalpage(
        'local_roletheme_simulate',
        get_string('simulatepage', 'local_roletheme'),
        new \moodle_url('/local/roletheme/simulate.php'),
        'moodle/site:config'
    ));
}
