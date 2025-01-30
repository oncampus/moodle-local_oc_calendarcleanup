<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * View components of occustom in admin tree
 *
 * @package     local_oc_calendarcleanup
 * @copyright   2025 oncampus GmbH <support@oncampus.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_oc_calendarcleanup', get_string('pluginname', 'local_oc_calendarcleanup'));

    $settings->add(new admin_setting_configtext(
        'local_oc_calendarcleanup/retention_days',
        get_string('retentiondays', 'local_oc_calendarcleanup'),
        get_string('retentiondays_desc', 'local_oc_calendarcleanup'),
        365, // Standard: 1 Jahr.
        PARAM_INT
    ));

    $ADMIN->add('localplugins', $settings);
}
