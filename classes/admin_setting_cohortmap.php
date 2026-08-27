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

/**
 * Compact textarea setting for cohort map configuration.
 *
 * @package   local_roletheme
 * @author    Pedro Luis Garcia Leiva
 * @copyright 2026 Pedro Lois {@link https://github.com/pedrolois}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_cohortmap extends \admin_setting_configtextarea {
    /**
     * Returns the setting control HTML.
     *
     * @param string $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        $default = $this->get_defaultsetting();
        $style = 'width: 32em; max-width: 100%; height: 7em; resize: vertical;';

        $element = \html_writer::tag('textarea', s($data), [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'rows' => 4,
            'cols' => 40,
            'wrap' => 'virtual',
            'class' => 'form-control text-ltr',
            'style' => $style,
        ]);

        return \format_admin_setting(
            $this,
            $this->visiblename,
            $element,
            $this->description,
            true,
            '',
            $default,
            $query,
        );
    }
}
