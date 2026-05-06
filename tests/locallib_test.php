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

declare(strict_types=1);

namespace local_roletheme;

use advanced_testcase;
use context_course;
use context_system;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');

/**
 * Tests for the role theme local library.
 *
 * @package   local_roletheme
 * @covers ::local_roletheme_parse_priority_csv
 * @covers ::local_roletheme_validate_priority_csv
 * @covers ::local_roletheme_resolve_theme_for_user
 * @covers ::local_roletheme_explain_theme_for_user
 * @covers ::local_roletheme_apply_theme_for_user
 * @copyright 2026 Pedro Lois
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class locallib_test extends advanced_testcase {
    /**
     * Reset state before each test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test priority parsing falls back to the default list.
     */
    public function test_parse_priority_fallback(): void {
        $this->assertSame(
            ['siteadmin', 'manager', 'editingteacher', 'teacher', 'student', 'guest'],
            local_roletheme_parse_priority_csv(null)
        );
    }

    /**
     * Test duplicate role validation fails.
     */
    public function test_validate_priority_duplicates(): void {
        $this->assertNotNull(local_roletheme_validate_priority_csv('manager,student,manager'));
    }

    /**
     * Test theme resolution prefers the first matching role in priority.
     */
    public function test_resolve_theme_uses_priority(): void {
        global $DB;

        set_config('priority', 'manager,student', 'local_roletheme');
        set_config('systemcontextonly', 1, 'local_roletheme');
        set_config('rolemap_manager', 'boost', 'local_roletheme');
        set_config('rolemap_student', 'classic', 'local_roletheme');

        $user = $this->getDataGenerator()->create_user();
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);

        role_assign($managerroleid, $user->id, context_system::instance()->id);
        role_assign($studentroleid, $user->id, context_system::instance()->id);

        $this->assertSame('boost', local_roletheme_resolve_theme_for_user((int) $user->id));
    }

    /**
     * Test course context roles are included when system-only mode is disabled.
     */
    public function test_resolve_theme_in_course_context(): void {
        global $DB;

        set_config('priority', 'editingteacher,student', 'local_roletheme');
        set_config('systemcontextonly', 0, 'local_roletheme');
        set_config('rolemap_editingteacher', 'boost', 'local_roletheme');

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);

        role_assign($roleid, $user->id, context_course::instance($course->id)->id);

        $this->assertSame('boost', local_roletheme_resolve_theme_for_user((int) $user->id));
    }

    /**
     * Test explanation contains the winning role.
     */
    public function test_explain_theme_for_user(): void {
        global $DB;

        set_config('priority', 'student', 'local_roletheme');
        set_config('systemcontextonly', 1, 'local_roletheme');
        set_config('rolemap_student', 'classic', 'local_roletheme');

        $user = $this->getDataGenerator()->create_user();
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        role_assign($studentroleid, $user->id, context_system::instance()->id);

        $explanation = local_roletheme_explain_theme_for_user((int) $user->id);
        $this->assertSame('student', $explanation['winningrole']);
        $this->assertSame('classic', $explanation['themename']);
        $this->assertContains('student', $explanation['userroles']);
    }

    /**
     * Test applying a theme persists it to the user record.
     */
    public function test_apply_theme_for_user_updates_record(): void {
        global $DB;

        set_config('priority', 'student', 'local_roletheme');
        set_config('systemcontextonly', 1, 'local_roletheme');
        set_config('rolemap_student', 'classic', 'local_roletheme');

        $user = $this->getDataGenerator()->create_user();
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        role_assign($studentroleid, $user->id, context_system::instance()->id);

        local_roletheme_apply_theme_for_user((int) $user->id);

        $this->assertSame('classic', $DB->get_field('user', 'theme', ['id' => $user->id]));
    }

    /**
     * Test cohort fallback uses configured cohort priority when no role matches.
     */
    public function test_resolve_theme_uses_cohort_priority(): void {
        set_config('priority', 'teacher', 'local_roletheme');
        set_config('enablecohorts', 1, 'local_roletheme');
        set_config('cohortpriority', 'vip,staff', 'local_roletheme');
        set_config('cohortmap', "staff=classic\nvip=boost", 'local_roletheme');

        $user = $this->getDataGenerator()->create_user();
        $vipcohort = $this->getDataGenerator()->create_cohort(['idnumber' => 'vip']);
        $staffcohort = $this->getDataGenerator()->create_cohort(['idnumber' => 'staff']);
        cohort_add_member($vipcohort->id, $user->id);
        cohort_add_member($staffcohort->id, $user->id);

        $this->assertSame('boost', local_roletheme_resolve_theme_for_user((int) $user->id));
    }

    /**
     * Test role mappings still win before cohort mappings.
     */
    public function test_role_mapping_beats_cohort_mapping(): void {
        global $DB;

        set_config('priority', 'student', 'local_roletheme');
        set_config('systemcontextonly', 1, 'local_roletheme');
        set_config('rolemap_student', 'classic', 'local_roletheme');
        set_config('enablecohorts', 1, 'local_roletheme');
        set_config('cohortpriority', 'vip', 'local_roletheme');
        set_config('cohortmap', 'vip=boost', 'local_roletheme');

        $user = $this->getDataGenerator()->create_user();
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        role_assign($studentroleid, $user->id, context_system::instance()->id);

        $vipcohort = $this->getDataGenerator()->create_cohort(['idnumber' => 'vip']);
        cohort_add_member($vipcohort->id, $user->id);

        $explanation = local_roletheme_explain_theme_for_user((int) $user->id);
        $this->assertSame('classic', $explanation['themename']);
        $this->assertSame('role', $explanation['winningsource']);
    }
}
