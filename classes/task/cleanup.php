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
        $retentiondays = get_config('local_oc_calendarcleanup', 'retention_days');
        return get_string('taskname', 'local_oc_calendarcleanup');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');

        // Charge the setting for the retention period in days.
        $retentiondays = get_config('local_oc_calendarcleanup', 'retention_days');

        // Conversion of the days in seconds.
        $timeperiod = $retentiondays * 24 * 60 * 60; // Days * hours * minutes * seconds.

        $cutoff = time() - $timeperiod;

        $events = $DB->get_records_select('event', 'timestart < ?', [$cutoff]);

        mtrace('Start delete_old_events task');

        foreach ($events as $event) {
            try {
                // Get end of event time.
                $timeend = $event->timestart + $event->timeduration;

                // Check if cutoff day is bigger than timeend.
                if ($cutoff > $timeend) {
                    $this->delete_event($DB, $event);
                }
            } catch (\Exception $e) {
                // General error treatment for unexpected errors.
                mtrace("Datenbankfehler: " . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
    }

    /**
     * Delete old events
     *
     * @param $mevent
     * @param $DB
     * @param $event
     * @return void
     */
    private function delete_event($DB, $event): void {

        $mevent = \calendar_event::load($event->id);

        // Check if user exist.
        $userexists = $DB->get_record('user', ['id' => $mevent->userid], 'id');

        if ($userexists) {
            $mevent->delete();
            mtrace('Event deleted: Event name (' . $event->name . ') and Event ID (' . $event->id . ')');
        } else {
            $DB->delete_records('event', ['id' => $event->id]);
            mtrace('User not found with ID: ' . $mevent->userid .
                ' Event deleted: Event name (' . $event->name . ') and Event ID (' . $event->id . ')');
        }
    }
}
