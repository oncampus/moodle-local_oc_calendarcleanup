# OC Calendarcleanup #

Pluginname: Local_oc_calendarcleanup
Moodle has no function to clean up the calendar of old entries. 
Since the calendar quickly becomes full, this can affect Moodle's performance.
The plugin is a cronjob that checks the calendar according to old entries and deletes old calendar entries.
The cronjob runs for a predefined time. 

## Overview

- Old calendar entries are deleted in the background
- The frequency of how often the cronjob should run can be set in the site administrate /admin/settings.php?section=local_oc_calendarcleanup
- Check every calendar entry
- Check whether the user exists

### Admin view

- Only the admin can change the settings
- The deletion date can be entered under /admin/settings.php?section=local_oc_calendarcleanup
- The admin can immediately toast the cronjob via Scheduled tasks
- The admin can check the logs via Scheduled tasks

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/oc_calendarcleanup

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Support

- Yoko Rieger

## Authors and acknowledgment

- Yoko Rieger


## License ##

2024 Yoko Rieger <yoko.rieger@oncampus.de>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
