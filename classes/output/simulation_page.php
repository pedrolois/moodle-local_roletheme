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

namespace local_roletheme\output;

use renderer_base;
use renderable;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();

/**
 * Data for the simulation page output.
 *
 * @package   local_roletheme
 * @author    Pedro Luis Garcia Leiva
 * @copyright 2026 Pedro Lois {@link https://github.com/pedrolois}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class simulation_page implements renderable, templatable {
    /** @var string */
    private $formhtml;

    /** @var array */
    private $issues;

    /** @var stdClass|null */
    private $user;

    /** @var array|null */
    private $simulation;

    /**
     * Constructor.
     *
     * @param string $formhtml Rendered form HTML.
     * @param array $issues Configuration issues.
     * @param stdClass|null $user Selected user.
     * @param array|null $simulation Simulation data.
     */
    public function __construct(string $formhtml, array $issues, ?stdClass $user, ?array $simulation) {
        $this->formhtml = $formhtml;
        $this->issues = $issues;
        $this->user = $user;
        $this->simulation = $simulation;
    }

    /**
     * Export the page data for a Mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'formhtml' => $this->formhtml,
            'hasissues' => !empty($this->issues),
            'issues' => array_map(static function(string $issue): array {
                return ['message' => $issue];
            }, $this->issues),
            'showokmessage' => empty($this->issues),
            'okmessage' => get_string('simulationnoconfigurationissues', 'local_roletheme'),
            'hasresult' => $this->simulation !== null && $this->user !== null,
            'showempty' => $this->user === null,
            'emptymessage' => get_string('simulationnouser', 'local_roletheme'),
            'resulttitle' => get_string('simulationresult', 'local_roletheme'),
            'headers' => [
                'setting' => get_string('setting', 'admin'),
                'value' => get_string('value', 'admin'),
            ],
            'rows' => [],
        ];

        if ($data['hasresult']) {
            $data['rows'] = $this->build_rows();
        }

        return $data;
    }

    /**
     * Build the table rows for the simulation result.
     *
     * @return array
     */
    private function build_rows(): array {
        $simulation = $this->simulation;
        $user = $this->user;

        $resolvedtheme = $simulation['themename'] === '' || $simulation['themename'] === null
            ? get_string('simulationnothemeset', 'local_roletheme')
            : $simulation['themename'];
        $winningsource = '-';
        if ($simulation['winningsource'] === 'cohort') {
            $winningsource = get_string('simulationcohort', 'local_roletheme');
        } else if ($simulation['winningsource'] === 'role') {
            $winningsource = get_string('simulationrole', 'local_roletheme');
        }

        return [
            $this->build_row(
                get_string('simulationuser', 'local_roletheme'),
                fullname($user) . ' (' . $user->username . ')'
            ),
            $this->build_row(get_string('simulationtheme', 'local_roletheme'), $resolvedtheme),
            $this->build_row(get_string('simulationwinningsource', 'local_roletheme'), $winningsource),
            $this->build_row(get_string('simulationwinningrole', 'local_roletheme'), $simulation['winningrole'] ?? '-'),
            $this->build_row(get_string('simulationpriority', 'local_roletheme'), implode(', ', $simulation['priority'])),
            $this->build_row(
                get_string('simulationcohortpriority', 'local_roletheme'),
                $simulation['cohortpriority'] ? implode(', ', $simulation['cohortpriority']) : '-'
            ),
            $this->build_row(
                get_string('simulationuserroles', 'local_roletheme'),
                $simulation['userroles'] ? implode(', ', $simulation['userroles']) : '-'
            ),
            $this->build_row(
                get_string('simulationusercohorts', 'local_roletheme'),
                $simulation['usercohorts'] ? implode(', ', $simulation['usercohorts']) : '-'
            ),
            $this->build_row(
                get_string('simulationmappedroles', 'local_roletheme'),
                $simulation['matchedroles'] ? implode(', ', $simulation['matchedroles']) : '-'
            ),
            $this->build_row(
                get_string('simulationmappedcohorts', 'local_roletheme'),
                $simulation['matchedcohorts'] ? implode(', ', $simulation['matchedcohorts']) : '-'
            ),
            $this->build_row(
                get_string('simulationsystemscope', 'local_roletheme'),
                $simulation['systemonly']
                    ? get_string('simulationyes', 'local_roletheme')
                    : get_string('simulationno', 'local_roletheme')
            ),
        ];
    }

    /**
     * Build a template row.
     *
     * @param string $label
     * @param string $value
     * @return array
     */
    private function build_row(string $label, string $value): array {
        return [
            'label' => $label,
            'value' => $value,
        ];
    }
}
