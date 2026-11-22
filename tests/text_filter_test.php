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
 * Tests for the local time filter.
 *
 * @package    filter_localtime
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \filter_localtime\text_filter
 */
final class text_filter_test extends \advanced_testcase {
    /**
     * Filter text as a user in the given time zone.
     *
     * @param string $text
     * @param string $usertz
     * @return string
     */
    private function run_filter(string $text, string $usertz): string {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user(['timezone' => $usertz]));
        return (new text_filter(\context_system::instance(), []))->filter($text);
    }

    public function test_converts_to_the_viewers_time_zone(): void {
        $out = $this->run_filter('Live session {localtime 2026-12-01 14:00 Europe/Paris} online', 'Asia/Tokyo');
        // 14:00 in Paris (UTC+1) is 13:00 UTC and 22:00 in Tokyo.
        $this->assertStringContainsString('<time datetime="2026-12-01T13:00:00Z"', $out);
        $this->assertStringContainsString('10:00 PM', $out);
        $this->assertStringStartsWith('Live session ', $out);
        $this->assertStringEndsWith(' online', $out);
    }

    public function test_without_a_zone_uses_the_site_zone(): void {
        $this->resetAfterTest();
        set_config('timezone', 'UTC');
        $out = $this->run_filter('{localtime 2026-12-01 14:00}', 'America/New_York');
        $this->assertStringContainsString('datetime="2026-12-01T14:00:00Z"', $out);
        $this->assertStringContainsString('9:00 AM', $out);
    }

    public function test_bad_input_is_left_alone(): void {
        foreach (['{localtime tomorrow}', '{localtime 2026-13-45 10:00}', '{localtime 2026-12-01 14:00 Mars/Base}'] as $text) {
            $this->assertSame($text, $this->run_filter($text, 'UTC'));
        }
    }

    public function test_text_without_the_tag_is_untouched(): void {
        $this->assertSame('<p>Nothing here</p>', $this->run_filter('<p>Nothing here</p>', 'UTC'));
    }
}
