@filter @filter_localtime
Feature: Times shown in each learner's own time zone
  In order to join live sessions on time
  As a learner in another country
  I need to see the session time in my own time zone

  Background:
    Given the "localtime" filter is "on"
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | timezone   |
      | student1 | Asia/Tokyo |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity | course | name         | content                                                     |
      | page     | C1     | Live session | Session starts {localtime 2026-12-01 14:00 Europe/Paris}. |

  Scenario: A learner in Tokyo sees Tokyo time
    When I am on the "Live session" "page activity" page logged in as "student1"
    Then I should see "Tuesday, 1 December 2026, 10:00 PM"
    And I should not see "{localtime"
