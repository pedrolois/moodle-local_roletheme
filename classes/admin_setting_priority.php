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

require_once($CFG->dirroot . '/lib/adminlib.php');
require_once($CFG->dirroot . '/local/roletheme/locallib.php');

/**
 * Custom admin setting for validating role priority configuration.
 *
 * @package   local_roletheme
 * @author    Pedro Luis Garcia Leiva
 * @copyright 2026 Pedro Lois {@link https://github.com/pedrolois}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_priority extends \admin_setting_configtext {
    /**
     * Validate setting data.
     *
     * @param string $data
     * @return bool|string
     */
    public function validate($data) {
        $parentvalidation = parent::validate($data);
        if ($parentvalidation !== true) {
            return $parentvalidation;
        }

        $validationerror = local_roletheme_validate_priority_csv((string) $data);
        if ($validationerror !== null) {
            return $validationerror;
        }

        return true;
    }
}
