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
 * Theme resolution simulator page.
 *
 * @package   local_roletheme
 * @copyright 2026 Pedro Lois
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/roletheme/locallib.php');
require_once($CFG->dirroot . '/local/roletheme/classes/form/simulate_form.php');

require_login();
admin_externalpage_setup('local_roletheme_simulate');

$PAGE->set_url(new \moodle_url('/local/roletheme/simulate.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('simulatepage', 'local_roletheme'));
$PAGE->set_heading(get_string('simulatepage', 'local_roletheme'));

$form = new \local_roletheme\form\simulate_form();
$renderer = $PAGE->get_renderer('local_roletheme');
$simulation = null;
$user = null;

if ($data = $form->get_data()) {
    $user = $DB->get_record('user', ['id' => $data->userid, 'deleted' => 0], 'id, firstname, lastname, username, email, theme');
    if ($user) {
        $simulation = local_roletheme_explain_theme_for_user((int) $user->id);
    } else {
        \core\notification::error(get_string('simulationunknownuser', 'local_roletheme', $data->userid));
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('simulatepage', 'local_roletheme'));

$issues = local_roletheme_get_configuration_issues();
$formhtml = $form->render();
$page = new \local_roletheme\output\simulation_page($formhtml, $issues, $user, $simulation);
echo $renderer->render($page);

echo $OUTPUT->footer();
