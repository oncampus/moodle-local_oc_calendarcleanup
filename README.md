# Calendarcleanup #

Moodle does not automatically clean up old calendar entries. Since the calendar quickly becomes full,
this can affect Moodle's performance, especially since the calendar block is widely used.

## Overview

The plugin checks the calendar for old entries. They are only deleted if the starttime and duration of the event added together
are older than what is configured as a retention time in the settings.php

This also works for events that were created by users that are themselves already deleted.

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


## Authors and acknowledgment

- Yoko Rieger <yoko.rieger@oncampus.de>


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
