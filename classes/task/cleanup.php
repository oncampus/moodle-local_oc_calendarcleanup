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
 * Cron task for calendarcleanup
 *
 * @package     local
 * @package     local_oc_calendarcleanup
 * @copyright   2025 oncampus GmbH <support@oncampus.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_oc_calendarcleanup\task;

/**
 * Cron task class
 *
 * @package     local
 * @package     local_oc_calendarcleanup
 * @copyright   2025 oncampus GmbH <support@oncampus.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'local_oc_calendarcleanup');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        $this->delete_old_events();
    }

    /**
     * Deletes or updates old appointments that are older than the configured storage period.
     *
     * This function reads the 'RENTENTION_DAYS' setting from the plugin configuration,
     * calculates the corresponding time and then processes all dates whose
     * The start time before this time. Repeated dates are dealt with:
     * Either the appointment is updated (starting time and duration) if)
     * The series has not yet ended or the appointment will be deleted if the series
     * has already expired. Individual dates without repetition are deleted directly.
     *
     * @return void
     */
    private function delete_old_events() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/calendar/lib.php');

        // Charge the setting for the retention period in days.
        $retentiondays = get_config('local_oc_calendarcleanup', 'retention_days');

        // Conversion of the days in seconds.
        $timeperiod = $retentiondays * 24 * 60 * 60; // Days * hours * minutes * seconds.

        $cutoff = time() - $timeperiod;

        $events = $DB->get_records_select('event', 'timestart < ?', [$cutoff]);

        mtrace('Start delete_old_events task');

        if ($events) { // Check whether appointments were found at all.
            foreach ($events as $event) {
                try {
                    $mevent = \calendar_event::load($event->id);

                    // Check whether it is a repeated appointment.
                    if ($event->repeatid) {

                        // Get end of event time.
                        $timeend = $event->timestart + $event->timeduration;

                        // If the event series has not yet ended.
                        if ((($timeend) > time()) || ($timeend > $cutoff)) {
                            // Shift timestart.
                            if ($event->timestart < $cutoff) {
                                // Get new event duration.
                                $newduration = $event->timeduration - ($cutoff - $event->timestart);
                                $event->timestart = $cutoff;
                                $event->timeduration = $newduration;
                                $mevent->update($event);
                                mtrace('Update repeat Event name (' . $event->name . ') and Event ID (' . $event->id . ')');
                            }
                            // Treatment of repeated appointment.
                        } else {
                            $mevent->delete();
                            mtrace('Event deleted Event name (' . $event->name . ') and Event ID (' . $event->id . ')');
                        }

                    } else {
                        // Delete normal appointment.
                        $mevent->delete();
                        mtrace('Event deleted Event name (' . $event->name . ') and Event ID (' . $event->id . ')');
                    }
                } catch (\Exception $e) {
                    mtrace("Fehler bei Event-ID {$event->id}: " . $e->getMessage());
                }
            }
        }
    }
}
