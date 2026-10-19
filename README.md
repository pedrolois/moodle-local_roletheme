# local_roletheme

`local_roletheme` is a Moodle local plugin that automatically sets a user's preferred theme based on configured role mappings and optional cohort mappings.

The plugin is designed to be predictable and safe for Moodle 4.5+. It does not call `$PAGE->force_theme()` during normal page execution. Instead, it updates the stored user preferred theme at login, when role assignments change, and during a scheduled synchronisation task.

## Features

- Maps Moodle roles to themes using an explicit priority order.
- Optionally maps cohort idnumbers to themes as a fallback when no role mapping matches.
- Supports users who belong to multiple cohorts by applying an ordered cohort priority list.
- Includes a Theme Resolution Simulator page for administrators.
- Includes a daily scheduled task to resynchronise user theme assignments.
- Locks the profile preferred theme selector on user edit pages so the plugin remains authoritative.
- Declares a Privacy API null provider because it does not create its own personal-data storage.

## Requirements

- Moodle 4.5 or later (tested up to Moodle 5.3).
- User themes must be enabled in `config.php`:

```php
$CFG->allowuserthemes = true;
```

## Installation

1. Copy this plugin to `local/roletheme`.
2. Visit `Site administration -> Notifications`.
3. Configure the plugin at `Site administration -> Plugins -> Local plugins -> Role and Cohort Theme Switcher`.

## Configuration overview

### Role Theme Mappings

Use this section to define which theme should be applied for each supported role.

- `Theme for role`: maps an individual role shortname to a theme.
- `Role priority`: decides which role wins when a user has several mapped roles.
- `Check roles only at system context`: limits role resolution to system role assignments only.

### Cohort Theme Mappings

Use this section to define fallback theme rules based on cohort membership.

- `Enable cohort mappings`: enables cohort-based fallback resolution.
- `Cohort priority`: orders cohort idnumbers when a user belongs to multiple mapped cohorts.
- `Cohort to theme mapping`: one mapping per line using `cohortidnumber=theme`.

If a user matches both a role rule and a cohort rule, the role rule wins.

## Theme resolution order

The plugin resolves themes in this order:

1. Site administrator pseudo-role, if mapped.
2. Configured role mappings, using the configured role priority.
3. Configured cohort mappings, using the configured cohort priority.
4. Default site theme, if no mapping applies or if the selected mapping intentionally clears the preferred theme.

## Simulator

The simulator page is available from:

`Site administration -> Plugins -> Local plugins -> Theme Resolution Simulator`

It allows administrators to enter a Moodle user ID and inspect:

- detected roles
- detected cohorts
- configured role priority
- configured cohort priority
- matched rules
- winning source and resulting theme
- configuration issues

## Scheduled task

The plugin defines a daily scheduled task that recomputes stored preferred themes for active users. This complements the event observers by ensuring existing users are resynchronised after configuration changes.

## Development notes

- Event observers are defined in `db/events.php`.
- The scheduled task is defined in `db/tasks.php`.
- Core resolution logic lives in `locallib.php`.
- Privacy API coverage lives in `classes/privacy/provider.php`.
- PHPUnit coverage is in `tests/locallib_test.php`.
- Version history is tracked in `CHANGES.md`.

## Limitations

- The plugin writes to the user preferred theme field, so `allowuserthemes` must remain enabled.
- Cohort mappings rely on cohort `idnumber` values. Empty cohort idnumbers are ignored.
- Role mappings are still the primary decision source even when cohort support is enabled.

## License

GNU GPL v3 or later.
