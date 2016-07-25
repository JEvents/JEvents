# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## 25-07-2016 - Geraint Edwards
* JEvents 3.4.15 Release
* Missing $rand in email cloak code
* re-instate filter of events created by unlogged in users in backend list of events

## 22-07-2016 - Geraint Edwards
* isEventPublisher, isEventCreator now check category permissions for enhanced ACL support
* Category Admin users can be chosen from users able to publish event within that category
* manage events list is visible to users who can publish events in addition to JEvents admin users 
* Fix for pagination in list events view where no translated tasks are available.

## 18-07-2016 - Geraint Edwards
* JEvents 3.4.14 Release
* Add latest events option to show most recently modified events
* More control options for notification messages for new events
* Fix for TinyMCE editor issue arising in Joomla 3.6.0
* Fix for router issue where tasks has a hyphen in their translations
* Fix for editing from an event detail popup and hitting close or save and close
* Fix for updating JEvents translations using Joomla Updater
* Fix for multiple instances of cloaked email within the same event detail page
* Add configuration options for icons to show on iCal export menu item
* Better scollable typeahead results

## 29-06-2016 - Tony Partridge
* Reverted StringHelper back to JString for Joomla! 3.4 Support

## 21-06-2012 - Geraint Edwards
Calendar popovers appear after 150milisecs to avoid lots appearing at once
add chevrons and info icons to plugin configuration

## 17-06-2016 - Geraint Edwards
* Fix duplicated output in flat theme date range view.
* Let category link in latest events module respect target menu item is using ${CATEGORYLNK}

## 14-06-2016 - Geraint Edwards
* Enable configuration of all JEvents plugins via the main JEvents config/params page
* Allow filtering of all published JEvents layouts i.e. without need to check all the categories in turn
* Fix for jevFilter constructor names
* DTSTAMP for export of repeat exceptions should not have timezone in it
* Allow list of events view to be ordered with more choices
* Installer message when updating using Joomla updater was not being shown
* SMore flexibility on countdown output in latest events module

## 13-06-2016 - Tony Partridge
* Updated the google export to support http and https replacing to webcal://, fixes invalid email address issue when adding to google calendar.

## 08-06-2016 - Tony Partridge
* Updated constructor classes for PHP7 to avoid deprecated notices.
* Replaced some old intval() usage with (int) since we are using whole numbers in the values, this is also more efficient.

## 07-06-2016 - Tony Partridge
* Migrated from deprecated JApplication::stringURLSafe to JApplicationHelper::stringURLSafe
* Fixed Next and Previous repeat navigation in pop-ups. Previously the whole template was being loading within a modal when click next or previous repeat.
* Updated hardcoded english for iCal Repeat Deleted and iCal Repeats Deleted.
* Fixed undefined variable within iCalRepeat on redirect.
* Fixed jevuser.php where $idsstring was undefined, it should have been $idstring
* Migrated from deprecated JButton to JToolBarButton
* Declared editStrings variable in icalrepat/view.html.php to avoid a coding notice in code editor. Has not other effect but to be cleaner.
* Fixed $value implementation within jevtimezone.php JFormFieldJevtimeZone
* Updated License to correct Joomla! Version and wording
* Migrated from deprecated JString to StringHelper
* Migrated from deprecated JArrayHelper with ArrayHelper
* Set xhtml to false on JRoute on the Link within the default Later Events View to avoid failing urls.

## 06-05-2016 - Geraint Edwards
* Fix for unspecified sender address in new admin email config setting

## 04-05-2016 - Tony Partridge
* Updated edit page getEditor to JEditor
* Added Float Block custom layout template codes
* Added Float Theme Language Strings

## 02-05-2016 - Tony Partridge
* Added {{EVID}} Custom layout support
* Added configurable options for who email is sent from
* Removed stray debug messages

## 20-04-2016 - Tony Partridge
* Added in sender configurable parameters within: Configuration -> Event Editing
* Updated constructor classes to fix PHP7 Deprecation messages.
* Commented out var_dump commands..