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

namespace local_roletheme\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Simulation form for the role theme resolver.
 *
 * @package   local_roletheme
 * @author    Pedro Luis Garcia Leiva
 * @copyright 2026 Pedro Lois {@link https://github.com/pedrolois}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class simulate_form extends \moodleform {
    /**
     * Define the form.
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('text', 'userid', get_string('simulateform_userid', 'local_roletheme'));
        $mform->addHelpButton('userid', 'simulateform_userid', 'local_roletheme');
        $mform->setType('userid', PARAM_INT);
        $mform->addRule('userid', null, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('simulateform_submit', 'local_roletheme'));
    }
}
