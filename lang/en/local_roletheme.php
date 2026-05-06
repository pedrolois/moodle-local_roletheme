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
 * English language pack for local_roletheme.
 *
 * @package   local_roletheme
 * @category  string
 * @copyright 2026 Pedro Lois
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['componentname'] = 'Role and Cohort Theme Switcher';
$string['defaultoption'] = 'Default site theme';
$string['error_duplicatecohortpriority'] = 'The cohort priority contains duplicate entries: {$a}.';
$string['error_duplicatepriority'] = 'The role priority contains duplicate entries: {$a}.';
$string['error_emptypriority'] = 'The role priority must contain at least one role shortname.';
$string['error_invalidpriorityroles'] = 'The role priority contains unknown role shortnames: {$a}.';
$string['lockthemenote'] = 'Preferred theme is managed automatically by the Role and Cohort Theme Switcher plugin.';
$string['pluginname'] = 'Role and Cohort Theme Switcher';
$string['privacy:metadata'] = 'The Role and Cohort Theme Switcher plugin does not store any personal data of its own.';
$string['setting_cohortheading'] = 'Cohort Theme Mappings';
$string['setting_cohortheading_desc'] = 'Configure cohort-based fallback rules. Role mappings are checked first, and cohort mappings are used only when no role rule matches.';
$string['setting_cohortmap'] = 'Cohort to theme mapping';
$string['setting_cohortmap_desc'] = 'Enter one mapping per line in the format cohortidnumber=theme. Leave the theme empty after = to clear the user preferred theme and fall back to the site theme.';
$string['setting_cohortpriority'] = 'Cohort priority';
$string['setting_cohortpriority_desc'] = 'Comma-separated cohort idnumbers. If a user belongs to several mapped cohorts, the first matching cohort in this list wins. Any mapped cohorts not listed here are checked afterwards.';
$string['setting_enablecohorts'] = 'Enable cohort mappings';
$string['setting_enablecohorts_desc'] = 'If enabled, the plugin can resolve a theme from ordered cohort mappings when no role mapping matches.';
$string['setting_priority'] = 'Role priority';
$string['setting_priority_desc'] = 'Comma-separated role shortnames. If a user has several mapped roles, the first matching role in this list wins.';
$string['setting_roleheading'] = 'Role Theme Mappings';
$string['setting_roleheading_desc'] = 'Configure role-based theme rules and the order used to resolve users who match several roles.';
$string['setting_rolemap'] = 'Theme for role: {$a}';
$string['setting_rolemap_desc'] = 'Choose the theme to apply when the user has the "{$a}" role. Select Default site theme to clear the preferred theme value.';
$string['setting_systemcontextonly'] = 'Check roles only at system context';
$string['setting_systemcontextonly_desc'] = 'If enabled, theme resolution uses only system-context role assignments. Disable this to include course and category role assignments too.';
$string['simulateform_submit'] = 'Run simulation';
$string['simulateform_userid'] = 'User ID';
$string['simulateform_userid_help'] = 'Enter the Moodle user ID to inspect how the plugin resolves the theme for that user.';
$string['simulatepage'] = 'Theme Resolution Simulator';
$string['simulatepage_desc'] = 'Inspect how the plugin resolves the final theme for a specific user.';
$string['simulationcohort'] = 'Cohort';
$string['simulationcohortpriority'] = 'Configured cohort priority';
$string['simulationissues'] = 'Configuration issues';
$string['simulationmappedcohorts'] = 'Matched cohorts';
$string['simulationmappedroles'] = 'Matched roles';
$string['simulationno'] = 'No';
$string['simulationnoconfigurationissues'] = 'No configuration issues detected.';
$string['simulationnothemeset'] = 'Default site theme';
$string['simulationnouser'] = 'No user selected yet.';
$string['simulationpriority'] = 'Configured role priority';
$string['simulationresult'] = 'Simulation result';
$string['simulationrole'] = 'Role';
$string['simulationsystemscope'] = 'System context only';
$string['simulationtheme'] = 'Resolved theme';
$string['simulationunknownuser'] = 'User with ID {$a} was not found.';
$string['simulationuser'] = 'User';
$string['simulationusercohorts'] = 'Detected cohorts';
$string['simulationuserroles'] = 'Detected roles';
$string['simulationwinningrole'] = 'Winning role';
$string['simulationwinningsource'] = 'Winning source';
$string['simulationyes'] = 'Yes';
$string['status_allowuserthemesdisabled'] = 'User themes are disabled. Add $CFG->allowuserthemes = true; to config.php.';
$string['status_missingmappedcohorttheme'] = 'Cohort "{$a->cohort}" is mapped to missing theme "{$a->theme}".';
$string['status_missingmappedtheme'] = 'Role "{$a->role}" is mapped to missing theme "{$a->theme}".';
$string['task_syncuserthemes'] = 'Synchronise role and cohort theme assignments';
$string['taskusersprocessed'] = 'Processed {$a} users.';
$string['taskuserupdated'] = 'Updated user {$a->userid} => {$a->theme}';
