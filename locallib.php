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
 * Local helper functions for the role theme plugin.
 *
 * @package   local_roletheme
 * @author    Pedro Luis Garcia Leiva
 * @copyright 2026 Pedro Lois {@link https://github.com/pedrolois}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Default role priority.
 *
 * @return string
 */
function local_roletheme_get_default_priority_csv(): string {
    return 'siteadmin,manager,editingteacher,teacher,student,guest';
}

/**
 * Default cohort priority.
 *
 * @return string
 */
function local_roletheme_get_default_cohort_priority_csv(): string {
    return '';
}

/**
 * Parse the configured role priority list.
 *
 * @param string|null $prioritycsv
 * @return array
 */
function local_roletheme_parse_priority_csv(?string $prioritycsv): array {
    if ($prioritycsv === false || $prioritycsv === null || trim($prioritycsv) === '') {
        $prioritycsv = local_roletheme_get_default_priority_csv();
    }

    return array_values(array_filter(array_map('trim', explode(',', $prioritycsv)), static function(string $value): bool {
        return $value !== '';
    }));
}

/**
 * Validate the configured role priority list.
 *
 * @param string $prioritycsv
 * @return string|null
 */
function local_roletheme_validate_priority_csv(string $prioritycsv): ?string {
    global $DB;

    $priority = local_roletheme_parse_priority_csv($prioritycsv);
    if ($priority === []) {
        return get_string('error_emptypriority', 'local_roletheme');
    }

    $duplicates = array_keys(array_filter(array_count_values($priority), static function(int $count): bool {
        return $count > 1;
    }));
    if ($duplicates !== []) {
        return get_string('error_duplicatepriority', 'local_roletheme', implode(', ', $duplicates));
    }

    $validroles = $DB->get_records_menu('role', [], '', 'shortname,shortname');
    $validroles['siteadmin'] = 'siteadmin';

    $invalidroles = array_values(array_filter($priority, static function(string $shortname) use ($validroles): bool {
        return !array_key_exists($shortname, $validroles);
    }));
    if ($invalidroles !== []) {
        return get_string('error_invalidpriorityroles', 'local_roletheme', implode(', ', $invalidroles));
    }

    return null;
}

/**
 * Parse the configured cohort priority list.
 *
 * @param string|null $prioritycsv
 * @return array
 */
function local_roletheme_parse_cohort_priority_csv(?string $prioritycsv): array {
    if ($prioritycsv === false || $prioritycsv === null) {
        $prioritycsv = local_roletheme_get_default_cohort_priority_csv();
    }

    return array_values(array_filter(array_map('trim', explode(',', $prioritycsv)), static function(string $value): bool {
        return $value !== '';
    }));
}

/**
 * Parse configured cohort to theme mappings.
 *
 * @param string|null $mappingtext
 * @return array
 */
function local_roletheme_parse_cohort_theme_map(?string $mappingtext): array {
    $mappingtext = trim((string) $mappingtext);
    if ($mappingtext === '') {
        return [];
    }

    $map = [];
    $lines = preg_split('/\r\n|\r|\n/', $mappingtext);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$idnumber, $theme] = array_pad(array_map('trim', explode('=', $line, 2)), 2, '');
        if ($idnumber === '') {
            continue;
        }

        $map[$idnumber] = $theme;
    }

    return $map;
}

/**
 * Validate configured cohort priority list.
 *
 * @param string $prioritycsv
 * @return string|null
 */
function local_roletheme_validate_cohort_priority_csv(string $prioritycsv): ?string {
    $priority = local_roletheme_parse_cohort_priority_csv($prioritycsv);
    $duplicates = array_keys(array_filter(array_count_values($priority), static function(int $count): bool {
        return $count > 1;
    }));

    if ($duplicates !== []) {
        return get_string('error_duplicatecohortpriority', 'local_roletheme', implode(', ', $duplicates));
    }

    return null;
}

/**
 * Validate configured cohort map.
 *
 * @param string $mappingtext
 * @return string|null
 */
function local_roletheme_validate_cohort_theme_map(string $mappingtext): ?string {
    $installedthemes = local_roletheme_get_installed_theme_names();
    $map = local_roletheme_parse_cohort_theme_map($mappingtext);
    foreach ($map as $idnumber => $theme) {
        if ($theme !== '' && !in_array($theme, $installedthemes, true)) {
            return get_string('status_missingmappedcohorttheme', 'local_roletheme', (object) [
                'cohort' => $idnumber,
                'theme' => $theme,
            ]);
        }
    }

    return null;
}

/**
 * Return the configured role to theme map.
 *
 * @param array $priority
 * @return array
 */
function local_roletheme_get_role_theme_map(array $priority): array {
    $rolemap = [];
    foreach ($priority as $shortname) {
        $value = get_config('local_roletheme', "rolemap_{$shortname}");
        if ($value !== false) {
            $rolemap[$shortname] = $value;
        }
    }

    return $rolemap;
}

/**
 * Return installed theme shortnames.
 *
 * @return array
 */
function local_roletheme_get_installed_theme_names(): array {
    return array_keys(\core_component::get_plugin_list('theme'));
}

/**
 * Return configuration issues worth showing to administrators.
 *
 * @return array
 */
function local_roletheme_get_configuration_issues(): array {
    global $CFG;

    $issues = [];
    if (empty($CFG->allowuserthemes)) {
        $issues[] = get_string('status_allowuserthemesdisabled', 'local_roletheme');
    }

    $prioritycsv = (string) get_config('local_roletheme', 'priority');
    if ($priorityerror = local_roletheme_validate_priority_csv($prioritycsv)) {
        $issues[] = $priorityerror;
    }

    $cohortprioritycsv = (string) get_config('local_roletheme', 'cohortpriority');
    if ($cohortpriorityerror = local_roletheme_validate_cohort_priority_csv($cohortprioritycsv)) {
        $issues[] = $cohortpriorityerror;
    }

    $cohortmaptext = (string) get_config('local_roletheme', 'cohortmap');
    if ($cohortmaperror = local_roletheme_validate_cohort_theme_map($cohortmaptext)) {
        $issues[] = $cohortmaperror;
    }

    $priority = local_roletheme_parse_priority_csv($prioritycsv);
    $rolemap = local_roletheme_get_role_theme_map($priority);
    $installedthemes = local_roletheme_get_installed_theme_names();
    foreach ($rolemap as $role => $theme) {
        if ($theme !== '' && !in_array($theme, $installedthemes, true)) {
            $issues[] = get_string('status_missingmappedtheme', 'local_roletheme', (object) [
                'role' => $role,
                'theme' => $theme,
            ]);
        }
    }

    $cohortmap = local_roletheme_parse_cohort_theme_map($cohortmaptext);
    foreach ($cohortmap as $cohortidnumber => $theme) {
        if ($theme !== '' && !in_array($theme, $installedthemes, true)) {
            $issues[] = get_string('status_missingmappedcohorttheme', 'local_roletheme', (object) [
                'cohort' => $cohortidnumber,
                'theme' => $theme,
            ]);
        }
    }

    return $issues;
}

/**
 * Return the user's effective role shortnames.
 *
 * @param int $userid
 * @param bool $systemonly
 * @return array
 */
function local_roletheme_get_user_role_shortnames(int $userid, bool $systemonly): array {
    global $DB;

    $usershort = [];

    if ($systemonly) {
        $sysctx = \context_system::instance();
        $roles = get_user_roles($sysctx, $userid, true);
        if ($roles) {
            $roleid2sn = $DB->get_records_menu('role', [], '', 'id,shortname');
            foreach ($roles as $r) {
                if (isset($roleid2sn[$r->roleid])) {
                    $usershort[$roleid2sn[$r->roleid]] = true;
                }
            }
        }
    } else {
        $shorts = $DB->get_fieldset_sql("
            SELECT DISTINCT r.shortname
              FROM {role_assignments} ra
              JOIN {role} r ON r.id = ra.roleid
             WHERE ra.userid = ?
        ", [$userid]);
        foreach ($shorts as $sn) {
            $usershort[$sn] = true;
        }
    }

    return array_keys($usershort);
}

/**
 * Return the user's cohort idnumbers.
 *
 * @param int $userid
 * @return array
 */
function local_roletheme_get_user_cohort_idnumbers(int $userid): array {
    global $DB;

    return $DB->get_fieldset_sql(
        "SELECT c.idnumber
           FROM {cohort_members} cm
           JOIN {cohort} c ON c.id = cm.cohortid
          WHERE cm.userid = ?
            AND c.idnumber <> ''",
        [$userid]
    );
}

/**
 * Explain how the theme was resolved for a given user.
 *
 * @param int $userid
 * @return array
 */
function local_roletheme_explain_theme_for_user(int $userid): array {
    $priority = local_roletheme_parse_priority_csv((string) get_config('local_roletheme', 'priority'));
    $cohortpriority = local_roletheme_parse_cohort_priority_csv((string) get_config('local_roletheme', 'cohortpriority'));
    $systemonly = (bool) get_config('local_roletheme', 'systemcontextonly');
    $cohortsenabled = (bool) get_config('local_roletheme', 'enablecohorts');
    $rolemap = local_roletheme_get_role_theme_map($priority);
    $cohortmap = local_roletheme_parse_cohort_theme_map((string) get_config('local_roletheme', 'cohortmap'));

    $explanation = [
        'priority' => $priority,
        'cohortpriority' => $cohortpriority,
        'systemonly' => $systemonly,
        'cohortsenabled' => $cohortsenabled,
        'rolemap' => $rolemap,
        'cohortmap' => $cohortmap,
        'userroles' => [],
        'usercohorts' => [],
        'matchedroles' => [],
        'matchedcohorts' => [],
        'winningrole' => null,
        'winningcohort' => null,
        'winningsource' => null,
        'themename' => null,
        'issiteadmin' => is_siteadmin($userid),
        'issues' => local_roletheme_get_configuration_issues(),
    ];

    if ($rolemap === [] && (!$cohortsenabled || $cohortmap === [])) {
        return $explanation;
    }

    if ($explanation['issiteadmin']) {
        $explanation['userroles'][] = 'siteadmin';
        if (array_key_exists('siteadmin', $rolemap)) {
            $explanation['matchedroles'][] = 'siteadmin';
        }
    }

    $userroles = local_roletheme_get_user_role_shortnames($userid, $systemonly);
    $explanation['userroles'] = array_values(array_unique(array_merge($explanation['userroles'], $userroles)));

    foreach ($priority as $sn) {
        if (!array_key_exists($sn, $rolemap)) {
            continue;
        }

        if (in_array($sn, $explanation['userroles'], true)) {
            $explanation['matchedroles'][] = $sn;
            if ($explanation['winningrole'] === null) {
                $explanation['winningrole'] = $sn;
                $explanation['winningsource'] = 'role';
                $explanation['themename'] = $rolemap[$sn];
            }
        }
    }

    if ($explanation['winningrole'] === null && $cohortsenabled && $cohortmap !== []) {
        $usercohorts = local_roletheme_get_user_cohort_idnumbers($userid);
        $explanation['usercohorts'] = $usercohorts;

        $orderedcohorts = $cohortpriority === [] ? array_keys($cohortmap) : $cohortpriority;
        foreach ($orderedcohorts as $cohortidnumber) {
            if (!array_key_exists($cohortidnumber, $cohortmap)) {
                continue;
            }

            if (in_array($cohortidnumber, $usercohorts, true)) {
                $explanation['matchedcohorts'][] = $cohortidnumber;
                if ($explanation['winningcohort'] === null) {
                    $explanation['winningcohort'] = $cohortidnumber;
                    $explanation['winningsource'] = 'cohort';
                    $explanation['themename'] = $cohortmap[$cohortidnumber];
                }
            }
        }

        foreach ($usercohorts as $cohortidnumber) {
            if (!in_array($cohortidnumber, $orderedcohorts, true) && array_key_exists($cohortidnumber, $cohortmap)) {
                $explanation['matchedcohorts'][] = $cohortidnumber;
                if ($explanation['winningcohort'] === null) {
                    $explanation['winningcohort'] = $cohortidnumber;
                    $explanation['winningsource'] = 'cohort';
                    $explanation['themename'] = $cohortmap[$cohortidnumber];
                }
            }
        }
    }

    return $explanation;
}

/**
 * Resolve the effective theme shortname for the user.
 *
 * @param int $userid
 * @param \context|null $pagecontext
 * @return string|null
 */
function local_roletheme_resolve_theme_for_user(int $userid, ?\context $pagecontext = null): ?string {
    $explanation = local_roletheme_explain_theme_for_user($userid);
    if ($explanation['themename'] === '') {
        return '';
    }

    return $explanation['themename'];
}

/**
 * Apply the resolved theme to the user preference field.
 *
 * @param int $userid
 * @return string|null
 */
function local_roletheme_apply_theme_for_user(int $userid): ?string {
    global $CFG, $SESSION, $USER;

    $themename = local_roletheme_resolve_theme_for_user($userid);
    $userupdate = (object) [
        'id' => $userid,
        'theme' => $themename ?: '', // The column is NOT NULL: empty means the site theme.
    ];
    if (method_exists(\core\user::class, 'update_user')) {
        // Moodle 5.3+: user_update_user() is deprecated (MDL-82650).
        \core\user::update_user($userupdate, false);
    } else {
        require_once($CFG->dirroot . '/user/lib.php');
        user_update_user($userupdate, false);
    }

    if (isset($USER) && (int) $USER->id === $userid) {
        $USER->theme = $themename ?: '';
    }

    unset_user_preference('theme', $userid);
    if (isset($SESSION->theme)) {
        unset($SESSION->theme);
    }

    return $themename;
}
