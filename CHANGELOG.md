# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

#### 02-03-2018 - Tony Partridge
### Fixed
 * event list being filtered after cancelling event creation i.e. events filtered by published state.

#### 27-02-2018 - Tony Partridge
### Fixed
 * Saving bug resulting in call on null / rp_id() / location()
 * Event and event repeat selector not working due to incorrect JSession Check
 * Emptying trashed events
 * Joomla 3.6.x fatal error when trying to load Joomla 3.7 new calendar. 
 * List of Events view return blank on Repeat Summary
 * Set calendar popup width to be 300px; a nice size rather than 100%.
 
#### 17-02-2018 Geraint Edwards
 * JEvents 3.4.44
 * Authorisation check on save should check creator and editor permissions
 * Fix for colour picker being compromised by Joomla settings
 * Allow jomsocial group events plugin to be picked up in edit page layout editor
 * No-op code added to Category filter that can be used by an override to force category filters to apply to ALL selected filters and ANY of the selected filters
 * Workaround for lack of support for electric calendar in recent Joomla date picker update
 * Category selector replaced with style category name if only one choice is available
 * Allow date formatting in layout editor to use date() function syntax as well as strftime format
 * Replace deprecated Joomla method calls
 * Improved meta tag setting 
 * PHP 7.2 Support Changes
 * Fix for setting published state in iCal and csv import
 * Update code to current standards in installer
 * Add default all day event tick selection
 * fixed nonendtime default selection.
 * Fix noendtime config option, was not working. 
 * column selector should show textarea to allow advanced configuration
 * Fix for default category setting when changing calendar in event creation
 * Fix for timezone problems when editing repeats of existing events
 * Add ical and edit dialogs as options for calendar cell
 * calendar module navigation needs to handle UTF-8 data
 * csv export should allow all events to be output even if block show all is enabled
 * iCal export wasn't using maxYear and minYear so DST transitions were not always output
 * gwejson library needs to add slashes to the message
 * EXT Theme Fix Legend to show categories names always.
 * Ext theme legend width to auto so nothing is cut off.
 * Fix for default category setting when creating new event
 * Fix guest check, WE only want to show the message IF NOT guest.
 * Fix for missing cal_day declaration in default layout
 * Add additional param to getListQuery to allow setting if list or item
 * Added filtering to iCalRepeat to be same as iCalEvent

#### 29-08-2017 Geraint Edwards
 * JEvents 3.4.43
 * New fix for #389 Fix for Authorised Users not being able to edit own events when they hit the event limit
 * Stop check on freq==none being case sensitive

#### 26-08-2017 Geraint Edwards
 * JEvents 3.4.42
 * Reverse Tony's changes on max event creation check for authorised users since it broke Joomla ACL permissions
 * Fix for timezone specified repeating events which were not adjusted correctly when re-editing an event

#### 22-08-2017 Geraint Edwards
 * JEvents 3.4.41
 * revert changes to JOIN queries on rrule which had exposed a problem with iCal imported events not showing in the frontend.

#### 22-08-2017 Geraint Edwards
 * JEvents 3.4.40
 * Move Timezone input to before date/time on event edit page (where enabled)
 * Add loc_id integer field to database for locations to improve performance of DB queries on large sites
 * Improve irregular date imports
 * Add location index to event detail table for performance gains
 * Fix toolbar buttons in backend
 * Use Inner join instead of left join for performance gains
 * stray use of | instead of $separator
 * Allow contact field to be truncated in latest events module
 * no end time should default to 0 not ""
 * Allow CDATA through template replacer - was replacing closing ]] with }} which was not good for Google 
 * Fix for Authorised Users not being able to edit own events when they hit the event limit.
 * Added where clause to smart search plugin to only find published events.
 * Modify input sanitization where the user has allowed raw.
 * Make sure visibile filters check only is applied when using JEvents filter module or modules with filters in their params

#### 01-07-2017 Tony Partridge
* Updated Changelog
* Added new default option for no specific endtime.
* Disable system messages for iCal Reloading when a guest.

#### 25-06-2016 Tony Partridge
* Added new custom layout tag: {{{Past or Future:PAST_OR_FUTURE}} to output text past or future depending if the event is in the past or future. Handy for CSS Classing.

#### 21-06-2017 Tony Partridge
* Added CREATED: to the iCal export events following iCal Specification.

#### 20-06-2017 Tony Partridge
* Update ext modstyle.css to stop centralising latest events caused by  my change on the 15th.

#### 16-06-2017 Tony Partridge
* Set admin emails to run through the defaultloadedtemplate.php parser. This means tags used in the Event Details custom layout can now be used within the admin emails.

#### 15-05-2017 Tony Partridge
 * JEvents 3.4.39
 * W3C Validation fix in ext latest events module (Removed inline tags)
 * Added version compare to filtering method onSave.
 * Fixed {DURATION} output which broke in 3.4.38 by Tony's (int) conversion.
 
#### 11-05-2017 Tony Partridge
* JEvents 3.4.38 
* Fixed issue with JInputFilter saving on pre J3.7.1

#### 10-05-2017 Geraint Edwards
* JEvents 3.4.37
* For for checkbox and radio box required fields check
* Fix for no-end time events when editing specific repeats

#### 04-05-2017 Tony Partridge
* Added ability to select week start day within week view menu item to override the core selection.
* Code style improvements and deprecated class updates.
* Added published_fv to the publish/unpublish links within Manage Events View, this keeps the manage events filter state i.e. all events, published or un-published.

#### 03-05-2017 Tony Partridge
*Fixed issue with 504 Gateway timeout when saving on some servers, caused but using JRequest, replaced with JInput

####26-04-2017 Geraint Edwards
* JEvents 3.4.36 
* Joomla 3.7.0 bug workaround for calendar popup

####25-04-2017 Geraint Edwards
* JEvents 3.4.35 
* Joomla 3.7.0 workarounds on assigning parameters

####20-04-2017 Geraint Edwards
* JEvents 3.4.34
* Latest events module option to show only repeating events
* Hardening of catid filtering
* New config option to allow dropping eventdetail from new SEF URLs for event detail
* Latest events module - option to not show any repeating events
* Correct confusing translations for years before and after now

####24-03-2017 Geraint Edwards
* Fix for canPublishOwnEvents call
* Fix for geraint/default theme navigation icons when using relative min/max years
* Clarify message on top of page when editing an existing event with no repeats.

####17-03-2017 Geraint Edwards
* Fix for save button not appearing
* Week count in monthly repeating event editing now uses ordinals and reserves when counting back from the end of the month

####16-03-2016 Tony Partridge
* Tidied up CustomCSS and added new CustomCSS Caller Method

####15-03-2017 Tony Partridge
* Change from Editor to Creator for Save / Apply on new events
* Imrpoved canPublishOwnEvents() method to return $canPublishOwn value if set and no conditions met.

####14-03-2017 Tony Partridge
* Added new Custom CSS View and JForm based form

####14-03-2017 Geraint Edwards
* JEvents 3.4.31
* Use Opacity to make unselected fieldsets on edit screen more obviously disabled
* Switch repeat types if grey boxes are clicked anywhere
* Correct getter method for undeclared variables

####13-03-2017 Geraint Edwards
* JEvents 3.4.30
* Fix for modified column missing in the backend of JEvents and links to translations failing 
* Add Permalink config option on event detail tab of JEvents config - all JEvents detail links can now be pointed to the same menu item regardless of the source
* Fix for uid column duplication during upgrades on some times

####09-03-2017 Geraint Edwards
* JEvents 3.4.30
* Correct category image URL for sites with multiple categories but not all have images attached
* Add new JEvents getter plugin to allow plugins to push data into variables in a different way - needed for standard images update 3.4.9
* Layout customisation tool now inherits category specific layouts

####03-03-2017 Tony Partridge
* Added Global option as default for show all repeats option in Menu Items.

####02-03-2017 Geraint Edwards
* Force default time and date fields to be integers (some people had used 8:00pm instead of 20:00)
* preserve published state when copying and editing event
* move generation of access list to access field definition - will allow default value to be set in template override now
* required fields check fix for radio/check boxes
* export 500 characters of event description instead of just 100
* menu item filters setting parent categories will not pick up events in child categories unless they are specified too
* Add option for date range view to show date as well as time in list presentation

####01-03-2017 Tony Partridge
* More robust check if event title is blank
* Add CATEGORY_ALIAS to layout editor 
* Support for Joomla 3.7

####17-02-2017 Brian Teeman
* Language corrections

####16-02-2017 Tony Partridge
* Range views now always have a date column to the left. So tend not to need the date output again. This change is suggested by @BrianTeeman. Any users requiring the date and do so easily with the additional date string.

####08-02-2017 Tony Partridge
* Fixes issue where users not authorised to publish events auto-published new events.

####03-02-2017 Tony Partridge
* Added strings to .sys.ini for debugged permissions report.

####02-02-2017 Geraint Edwards
* Fix for time offset applied twice when editing event in nonstandard timezone
* Translation support for edit tabs from custom fields
* Max year php_max_int constraint for annual repeating events
* Stop calendar mod direct links to event detail from including tmpl=component
* latest events process match to support formatting options from plugins

####23-01-2017 Geraint Edwards
 * JEvents 3.4.29

####22-01-2017 Geraint Edwards
* Fix for UTF-8 tooltips
* Fix for install error on creating evaccess index 

####12-01-2017 Geraint Edwards
* JEvents 3.4.27
* Fix for sites not using TinyMCE (workaround for descriptions wasn't working in 3.4.26)
* JEvents 3.4.26
* Fix for reseting date filters in date range view

####08-01-2017 Geraint Edwards
* New config option to show multi-day events first in day list view
* Trap for memory overflow on sites with 60,000+ event creators
* Performance gain - no need to check category access in db queries since we already do this in getAccessibleCategories
* Support for %k in duration in latest events module

####07-01-2017 Geraint Edwards
* Code to handle bad TZID from microsoft imports was affecting descriptions and other fields with colons in them - resolve this.

####13-12-2016
* Make publish own an ACL setting
* TinyMCE required description workarounds

####09-12-2016 - Geraint Edwards
* fix for required description field - changes in TinyMCE
* allow irregular repeats to occur more than once on the same day.
* allow day list view to use float theme

####03-11-2016 - Geraint Edwards
* JEvents 3.4.24
* Fix conditional custom fields showon for event edit fields on first tab
* Force Itemid in check conflict script  - some routers/SEF addons were dropping this
* Latest events module default custom format string \n were not being parsed in default value  - replace with <br/>

######01-11-2016 - Geraint Edwards
* Foundation for publish own permissions in ACL - not implemented yet
* Warning message when RSVP Pro and RSVP are both enabled
* Enable category images in calendar cell and tool tip layouts
* Allow gwejson to pick up custom versions of scripts instead of released ones to preserve customisations e.g. use gwesjon_custom_finduser.php to replace gwesjon_finduser.php

####19-10-2016 - Tony Partridge
* Added Category Link Raw
* Added Option to include event detail link in iCal Export, added set default menu item id in Joomla! config under ical export for the time being since it's only used here.

####14-10-2016 - Geraint Edwards
* Add error checks to return to referrer on cancel code

####14-10-2016 - Tony Partridge
* Sends user back to previous url on cancel of event editing.
* Added a more meaningful title to toolbar and browser bar for JEvents cPanel

####12-10-2016 - Geraint Edwards
* Fix for filtermap fields install problem on some servers
* Comma separated by*day fields need to have spaces stripped during save process

####09-10-2016 - Geraint Edwards
* Make hide author setting apply throughout JEvents

####07-10-2016 - Geraint Edwards
* Jevents 3.4.23
* Correct install script error 
* Fix for column selection in list menu item
* Fix for deleting first repeat of repeats when repeat id = event id

####06-10-2016 - Geraint Edwards
* Allow saving and reloading of filter module data - start of re-working of filter system
* Make column selection in list view menu item sortable
* use showon for range view date settings to improve usability of configuration
* Make sure filter module sets option to com_jevents in the form incase the target menu item is not a JEvents one
* Fix for router where task could appear twice if not translated
* Make filter choices in module parameters sortable by drag and drop

####20-08-2016 - Geraint Edwards
* Jevents 3.4.20
* Include fixes from 30-08-2016 in package
* Fix DB query for SELECT event used in editor plugins etc.
* Work around for some imported all day event data representations
* Reset category filter when cancelling ical calendar edit
* Check for duplicate calendar names when creating new ones

####18-08-2016 - Geraint Edwards
* Jevents 3.4.19

####07-09-2016 - Geraint Edwards
* Issue with anonymous event creator name/email not appearing in notification messages.

####05-09-2016 - Geraint Edwards
* New installer plugin to manage installation of club addons

####31-08-2016 - Tony Partridge
 * Fix on setting limits on lists that were ignoring the max count set in menu items

####30-08-2016 - Geraint Edwards
* Fixed parameter saving for plugins where value is an array

####25-08-2016 - Tony Partridge
* Updated defaultloadedtemplate to generate the correct menu item links if the view datamodel contains a menu item for conisitency and custom datacalls.

####25-08-2016 - Geraint Edwards
* Jevents 3.4.18
* Change to GWEJson plugin to fix issue with RSS feeds for some users in Joomla 3.6.2

####17-08-2016 - Geraint Edwards
* Add message to warn user if they create a self-overlapping event
* Add max event option for category list view

####15-08-2016 - Tony Partridge
* FIXED
** Alternative View Search, was falling back to default due to class extends default when it should be alternative 

####05-08-2016 - Geraint Edwards
* JEvents 3.4.17 released
* Fixed missing $rand in email cloak code changes to compensate for Joomla changes
* Remove workaround for problematic cloaking code introduced in Joomla 3.6.1 but then removed in 3.6.2
* Fixed group by issue arising from managed locations that cause some iCal exports to pick up the wrong start date

####04-08-2016 - Tony Partridge
* Updated Category Image ALT to use specified ALT within category if it exists else fall back to a translation.

####03-08-2016 - Geraint Edwards
* Add config option for event list view to output events as a CSV File
* 
####02-08-2016 - Tony Partridge 
* Fixed Club Plugins loading in JEvents EXT Layout
* Removed unused variables. 
* 
####27-07-2016 - Geraint Edwards
* JEvents 3.4.16 release
* Fix for backend filtering of events by creator from 3.4.15
* workaround for cloaking change in Joomla 3.6.1
* upgrade isEventEditor method to take account of users who can only edit in specific categories

####10-08-2016 - Tony Partridge
# Added
* CSV Export Filter for the 'List of Events' menu item. 

####25-07-2016 - Geraint Edwards
* JEvents 3.4.15 Release
* Missing $rand in email cloak code
* re-instate filter of events created by unlogged in users in backend list of events

####22-07-2016 - Geraint Edwards
* isEventPublisher, isEventCreator now check category permissions for enhanced ACL support
* Category Admin users can be chosen from users able to publish event within that category
* manage events list is visible to users who can publish events in addition to JEvents admin users 
* Fix for pagination in list events view where no translated tasks are available.

####18-07-2016 - Geraint Edwards
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

####11-07-2016 - Tony Partridge
* Removed some version compares since no longer needed. 
* Replaced some intval() with (int)
* Fixed Approval Email subject, if editing or creating events it should differ. 

####29-06-2016 - Tony Partridge
* Reverted StringHelper back to JString for Joomla! 3.4 Support

####21-06-2012 - Geraint Edwards
Calendar popovers appear after 150milisecs to avoid lots appearing at once
add chevrons and info icons to plugin configuration

####17-06-2016 - Geraint Edwards
* Fix duplicated output in flat theme date range view.
* Let category link in latest events module respect target menu item is using ${CATEGORYLNK}

####14-06-2016 - Geraint Edwards
* Enable configuration of all JEvents plugins via the main JEvents config/params page
* Allow filtering of all published JEvents layouts i.e. without need to check all the categories in turn
* Fix for jevFilter constructor names
* DTSTAMP for export of repeat exceptions should not have timezone in it
* Allow list of events view to be ordered with more choices
* Installer message when updating using Joomla updater was not being shown
* SMore flexibility on countdown output in latest events module

####13-06-2016 - Tony Partridge
* Updated the google export to support http and https replacing to webcal://, fixes invalid email address issue when adding to google calendar.

####08-06-2016 - Tony Partridge
* Updated constructor classes for PHP7 to avoid deprecated notices.
* Replaced some old intval() usage with (int) since we are using whole numbers in the values, this is also more efficient.

####07-06-2016 - Tony Partridge
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

####06-05-2016 - Geraint Edwards
* Fix for unspecified sender address in new admin email config setting

####04-05-2016 - Tony Partridge
* Updated edit page getEditor to JEditor
* Added Float Block custom layout template codes
* Added Float Theme Language Strings

####02-05-2016 - Tony Partridge
* Added {{EVID}} Custom layout support
* Added configurable options for who email is sent from
* Removed stray debug messages

####20-04-2016 - Tony Partridge
* Added in sender configurable parameters within: Configuration -> Event Editing
* Updated constructor classes to fix PHP7 Deprecation messages.
* Commented out var_dump commands..
