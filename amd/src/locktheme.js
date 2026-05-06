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
 * Lock the preferred theme field on Moodle user edit forms.
 *
 * @module     local_roletheme/locktheme
 * @copyright  2026 Pedro Lois
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Add a hidden field and disable the visible theme selector.
 *
 * @param {Object} strings Localised strings for the module.
 */
const lockThemeField = strings => {
    const select = document.getElementById('id_theme');
    if (!select || select.dataset.localRolethemeLocked === '1') {
        return;
    }

    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = select.name;
    hidden.value = select.value;

    select.disabled = true;
    select.name = `${select.name}_disabled`;
    select.dataset.localRolethemeLocked = '1';
    select.parentNode.appendChild(hidden);

    const row = document.getElementById('fitem_id_theme') || select.closest('.fitem') || select.closest('.form-group');
    if (!row) {
        return;
    }

    const note = document.createElement('div');
    note.className = 'form-text text-muted';
    note.textContent = strings.lockNote || '';
    row.appendChild(note);
};

/**
 * Initialise the module.
 *
 * @param {Object} strings Localised strings for the module.
 */
export const init = strings => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => lockThemeField(strings), {once: true});
        return;
    }

    lockThemeField(strings);
};
