# Changelog

All notable changes to `local_roletheme` should be documented in this file.

## 2026-10-19 - Version 1.1.1 (2026101901)

- Fixed: when no role or cohort mapping applies, the user theme is cleared with an empty string instead of
  NULL. On Moodle 5.3 `\core\user::update_user()` rejected NULL ("Column 'theme' cannot be null") and the
  login observer failed.

## 2026-10-19 - Version 1.1.0 (2026101900)

- Added Moodle 5.0 - 5.3 support (`supported` is now `[405, 503]`).
- Use `\core\user::update_user()` on Moodle 5.3+, where `user_update_user()` is deprecated (MDL-82650).
  Older versions keep using `user_update_user()`.
- Fixed `requires` to the Moodle 4.5 version number (2024100700).

## 2026-05-06 - Repository readiness

- Added `release`, `maturity`, and `supported` metadata to `version.php`.
- Added a Privacy API null provider and matching language string.
- Updated the README to document the privacy declaration.
- Migrated the profile-field locking script to a Moodle AMD/ES6 module under `amd/src`.
- Reworked the simulator page to use the Output API with a renderable, renderer, and Mustache template.
- Removed the legacy root-level JavaScript file and added boilerplate headers to the new frontend assets.
- Migrated the old `before_http_headers` callback to the Moodle Hooks API via `db/hooks.php`.

## 2025-08-27 - Version 2025082704

- Renamed the visible plugin title to `Role and Cohort Theme Switcher`.
- Added cohort-based theme fallback using cohort `idnumber` mappings.
- Added ordered cohort priority support for users belonging to multiple cohorts.
- Added a Theme Resolution Simulator admin page.
- Added a daily scheduled task to resynchronise user preferred themes.
- Added PHPUnit coverage for role and cohort resolution scenarios.
- Reworked plugin strings and README documentation for clarity.
- Added settings validation helpers and compact cohort mapping admin control.
- Improved Moodle coding-style compliance across the plugin files.

## 2025-08-27 - Version 2025082703

- Added reusable resolver helpers for role-based theme resolution.
- Added settings validation for role priority configuration.
- Added the first version of the Theme Resolution Simulator page.
- Added the scheduled task scaffold for bulk theme synchronisation.

## 2025-08-27 - Version 2025082702

- Refactored the plugin to avoid forcing the page theme during page setup.
- Switched to persisting the user preferred theme at login and on role changes.
- Locked the preferred theme profile field on edit pages to avoid manual overrides.
