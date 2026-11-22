# Local time (filter_localtime)

Write a time once, and every learner sees it in their own time zone.

    Live session {localtime 2026-12-01 14:00 Europe/Paris}

A learner in Tokyo sees "Tuesday, 1 December 2026, 10:00 PM". One in Paris sees 2:00 PM. Without a zone, the time is read in the site's default time zone. A tag that does not parse is left as typed, so the author sees the mistake.

Works anywhere Moodle formats text: pages, labels, forum posts, assignment descriptions. Moodle 4.5 to 5.3. Enable it in Site administration > Plugins > Filters > Manage filters.

Built by DevLMS: https://devlms.com
