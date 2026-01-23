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

namespace local_oc_calendarcleanup\task;

use calendar_event;
use coding_exception;
use core\clock;
use core\di;
use core\task\scheduled_task;
use dml_exception;
use Exception;
use moodle_database;
use stdClass;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/calendar/lib.php');

/**
 * Cron task class
 *
 * @package     local
 * @package     local_oc_calendarcleanup
 * @copyright   2025 oncampus GmbH <support@oncampus.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup extends scheduled_task {
    /** @var moodle_database Database */
    private moodle_database $db;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->db = di::get(moodle_database::class);
    }

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     * @throws coding_exception
     */
    public function get_name(): string {
        return get_string('taskname', 'local_oc_calendarcleanup');
    }

    /**
     * Execute the task.
     *
     * @throws dml_exception
     */
    public function execute(): void {
        // Charge the setting for the retention period in days.
        $timeperiod = get_config('local_oc_calendarcleanup', 'retention_days');
        $cutoff = di::get(clock::class)->time() - $timeperiod;

        $events = $this->db->get_records_select('event', 'timestart < ?', [$cutoff], fields: 'id, name');
        mtrace('Start delete_old_events task');
        foreach ($events as $event) {
            $timeend = $event->timestart + $event->timeduration;
            if ($cutoff <= $timeend) {
                continue;
            }

            try {
                $this->delete_event($event->id, $event->name);
            } catch (dml_exception $e) {
                // General error treatment for unexpected errors.
                mtrace("Database error: " . $e->getMessage());
            }
        }
    }

    /**
     * Delete the calendar event with the given id
     *
     * @param int $eventid ID of the event
     * @param string $eventname Name of the event
     * @return void
     * @throws dml_exception
     */
    private function delete_event(int $eventid, string $eventname): void {
        $mevent = calendar_event::load($eventid);
        $userexists = $this->db->record_exists('user', ['id' => $mevent->userid]);

        if ($userexists) {
            $mevent->delete();
            mtrace("Event deleted: Event name ($eventname) and Event ID ($eventid)");
        } else {
            $this->db->delete_records('event', ['id' => $eventid]);
            mtrace("User not found with ID: $mevent->userid" .
                " Event deleted: Event name ($eventname) and Event ID ($eventid)");
        }
    }
}
