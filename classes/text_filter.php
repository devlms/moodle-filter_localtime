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

namespace filter_localtime;

/**
 * Shows {localtime YYYY-MM-DD HH:MM [Zone/Name]} in each viewer's own time zone.
 *
 * Without a zone the time is read in the site's default time zone. Anything that does not parse is left as typed,
 * so a mistake stays visible to the author instead of disappearing.
 *
 * @package    filter_localtime
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \core_filters\text_filter {
    /** @var string The tag: date, time, optional IANA zone. */
    const PATTERN = '~\{localtime\s+(\d{4}-\d{2}-\d{2})\s+(\d{1,2}:\d{2})(?:\s+([A-Za-z_]+(?:/[A-Za-z0-9_+\-]+){0,2}))?\s*\}~';

    /**
     * Replace each tag with a <time> element in the viewer's zone.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter($text, array $options = []) {
        if (!is_string($text) || stripos($text, '{localtime') === false) {
            return $text;
        }
        return preg_replace_callback(self::PATTERN, [$this, 'replace'], $text);
    }

    /**
     * One tag to HTML, or the tag unchanged when it does not parse.
     *
     * @param array $m regex match
     * @return string
     */
    protected function replace(array $m): string {
        $zone = $m[3] ?? '';
        if ($zone === '') {
            $zone = \core_date::get_server_timezone();
        } else if (!in_array($zone, \DateTimeZone::listIdentifiers(), true)) {
            return $m[0];
        }
        $dt = \DateTime::createFromFormat('!Y-m-d G:i', $m[1] . ' ' . $m[2], new \DateTimeZone($zone));
        $errors = \DateTime::getLastErrors();
        if (!$dt || ($errors && ($errors['warning_count'] || $errors['error_count']))) {
            return $m[0];
        }
        $ts = $dt->getTimestamp();
        $iso = gmdate('Y-m-d\TH:i:s\Z', $ts);
        $shown = userdate($ts, get_string('strftimedaydatetime', 'langconfig'));
        return '<time datetime="' . $iso . '" class="filter-localtime">' . s($shown) . '</time>';
    }
}
