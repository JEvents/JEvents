<?php
ob_start();
?>
	<ul>
		<li>JEvents 3.6.11
			<ul>
				<li>Force popup edit options dialog to have opacity of 1 when shown</li>
				<li>Allow filtering event editing layouts by category</li>
				<li>Editing event using custom layout in some circumstances $params was not declared.</li>
				<li>Fix for display of child categories in legend module if root parent is is not 1 or 0 due to a bad historic site migration</li>
			</ul>
		</li>
		<li>JEvents 3.6.10
			<ul>
				<li>$app was not declared for isClient call when editing events with a custom layout for event editing with no tabs</li>
				<li>Fix for calendar icon in frontend event filters</li>
				<li>Escape commas in location field in iCal export</li>
			</ul>
		</li>
		<li>JEvents 3.6.9
			<ul>
				<li>When editing events with a custom layout make sure conditional custom fields still work with showon.</li>
				<li>Use min-height on left menu icons to cope with some bad backend template CSS</li>
				<li>Better tooltips for links to&nbsp; translate events</li>
				<li>Allow changing to category when editing layouts to make more intuitive</li>
				<li>Support stripping HTML tags from LD-JSON structured data</li>
				<li>Allow editing latest events module output in the layout editor</li>
				<li>Fix setting Alias on creating new categories if alias has not been set. i.e. Importing Events.</li>
				<li>Added AutoIncremental Fix on installation update</li>
				<li>Quick catch for null test event - when primary key has changed usually.</li>
				<li>Fix for tooltips style when editing JEvents config</li>
				<li>Add support for organiser, event status and online event details in LD-JSON Structured Data</li>
				<li>Fix for double domain appearing in LD-JSON structured data image paths</li>
				<li>Fix for showon of custom fields using block instead of flex display</li>
				<li>Disable showon animation which could cause chosen select elements to appear behind other page elements</li>
				<li>Fix for dynamic saving of user group permissions in JEvents config.xml</li>
				<li>New option to allow auto-printing from print button in event detail view</li>
			</ul>
		</li>
		<li>JEvents 3.6.8
			<ul>
				<li>Fix for custom css and js changes in layouts not being saved</li>
				<li>Added coloured support to state icon for calendars and custom layouts.</li>
				<li>JEvents config layout problems in some languages caused by double underscore in config.xml description attribute</li>
				<li>Only use UIkit modal in event/repeat editing when in the frontend.</li>
				<li>Fix for dynamic saving of user group permissions in JEvents config.xml</li>
				<li>New option to allow auto-printing from print button in event detail view</li>
				<li>Fix for showon of custom fields using block instead of flex display</li>
				<li>Disable showon animation which could cause chosen select elements to appear behind other page elements</li>
			</ul>
		</li>
		<li>&nbsp;JEvents 3.6.7
			<ul>
				<li>Make dynamic field appearance in event editing off by default - can be configured to on in JEvents config</li>
				<li>Update installation landing page</li>
				<li>Fix use of ArrayHelper</li>
				<li>Fix typo in $app-&gt;input for trashing / changing category state.</li>
				<li>Implement our own equivalent of Chosen in UIkit style - always used in J4</li>
				<li>Fix for search button styling</li>
				<li>Fix for missing JEventsHelper declaration when editing location categories</li>
				<li>Fix for required core event fields not showing * in labels</li>
				<li>Show on fixes for editing events</li>
				<li>Use UIkit based modals when frontend uses new editing style</li>
			</ul>
		</li>
		<li>JEvents 3.6.6&nbsp;
			<ul>
				<li>New configuration option to control the behaviour of the left menu in the backend of JEvents</li>
				<li>New configuration option to enable the fields to appear one or two at a time in the event creation pages to guide the user through the event creation process</li>
				<li>New configurable placeholder/hint for event title to be used then revealing fields dynaimcally in event creation pages</li>
				<li>Fix for iCal export when specific years are selected</li>
				<li>Fix to showon handling when event title has quotes or double quotes in them</li>
				<li>Fix for php notice in event legen module</li>
			</ul>
		</li>
		<li>JEvents 3.6.5
			<ul>
				<li>Fix JevRegistry functions for Joomla 4</li>
				<li>Fix popup event selection</li>
				<li>Router fix for iCal Event Export and event search</li>
				<li>Better explantion of cause when failing to delete an event</li>
				<li>Fix for incorrect check before deleting event which could lead to some event being impossible to delete in the backend</li>
				<li>Fix for touch screen behaviour of left menu bar in backend</li>
				<li>Remove stray BOM text from non-JEvents plugins that can interfere with backend layouts</li>
				<li>Catch problem with some event repeats with multi-categories not being editable because of catids being a string instead of an array</li>
				<li>Fix router problems with 404 errors when JEvents is the home menu item<br>Resolve php 7.4 notice</li>
				<li>Fix club theme radio button styling</li>
				<li>Fix filter module reset (some residual mootools javascript was missed)</li>
				<li>Relieve some flickering of left menu by not using uikit-navbar for left navigation</li>
				<li>New config option for left menu to allow response to click instead of hover or even not show the revealing labels at all</li>
				<li>Catch corrupted old event with missing repeates during delete to allow them to be deleted without a 403 error</li>
				<li>Hide showon elements by default before JS takes over to reveal as needed</li>
				<li>Fix iCal export when specific catid or year values are selected</li>
				<li>Fix calendar icon for date picker in old version of event editing</li>
				<li>Make sure php 7 is used before upgrading to JEvents 3.6+</li>
				<li>Added Today and Tomorrow support for event outputs</li>
				<li>Added Creator first and last name display values</li>
				<li>Added 'Alias' for default created category</li>
				<li>Fixed default joomla access level for fields</li>
			</ul>
		</li>
		<li>JEvents 3.6.4
			<ul>
				<li>correct string replacement in bulid process to stop "com august" appearing in language strings</li>
				<li>only hide the main page header in the backend</li>
				<li>Correct use of showon for date fields in event creation</li>
				<li>Allow JEvents to show 404 error when its the home menu item</li>
				<li>Correct JevRegistry so that in Joomla 3.9 JRegistry::getInstance will get the same class as JevRegistry::getInstance - fixes filtering of events using JEvents club addons prior to release of version 3.6.x of club addons</li>
				<li>Events List view- explicitly select the correct repeat from the group by query</li>
				<li>fixDtStart was breaking the start date output for some latest events module repeats</li>
				<li>Allow iCalExport to use secure webcals:// URL type</li>
				<li>Correct date formatting for output of all day events</li>
				<li>Restore menu page heading for event editing in new formatted editing</li>
			</ul>
		</li>
		<li>JEvents 3.6.3
			<ul>
				<li>Disable tooltips on left menu</li>
				<li>Fix active tab on customised event editing pages</li>
				<li>Disabled showon on customised event editing pages</li>
				<li>Load Mootools when editing menu item until new club addons can be released</li>
				<li>Move css and js files to frontend so they can be used in frontend for editing behind htauth protection</li>
				<li>Create our own version of showon which is delayed by 100 milliseconds to help avoid chosen issues</li>
			</ul>
		</li>
		<li>JEvents 3.6.2
			<ul>
				<li>Fix for auto-refresh setting being lost for calendars</li>
				<li>Google chrome radio field presentation - was vertical instead of horizontal</li>
				<li>Extra Info field name correction</li>
				<li>Reveal fields in event edit now working when using single category event setting</li>
				<li>field reveal should be based on catid and title in case we only have one category</li>
				<li>Fix for Chrome issues with chosen being triggered AFTER showon</li>
			</ul>
			JEvents 3.6.1
			<ul>
				<li>Fix for missing icomoon font in some frontend templates</li>
				<li>Styling to category selector in frontend was truncated in some templates</li>
				<li>Remove use of JError (caused problem in Joomla 4)</li>
				<li>Missing $filter declaration in saving repeats in backend</li>
				<li>Stray . after calendar icon in frontend editing</li>
				<li>Missing edit_datetime_uikit.php in frontend repeat editing</li>
				<li>Suppress warning for events where ALL the repeats have been edited.</li>
				<li>Use JSON ENCODE as array for chart labels to avoid escaping problems</li>
			</ul>
		</li>
		<li>JEvents 3.6.0
			<ul>
				<li>New Style in backend and optionally when editing events in the frontend</li>
				<li>Ground work for Joomla 4.x support</li>

				<li>JEvents 3.4.57
			<ul>
				<li>Fixes isssue with foreArrayToInteger warning.</li>
			</ul>
		</li>
		<li>JEvents 3.4.56
			<ul>
				<li>Fix CSV export for for list views</li>
				<li>Change settimeout call to pass reference to function instead of function string - was interpreted as a call to eval by content security policy settings</li>
				<li>Add ALLCATEGORIESSLUGS to layout editor - can be useful for setting class names in layouts</li>
				<li>Resolve PHP notice about assigning by reference</li>
				<li>Fix ical export offering HTML output if this has already been disabled</li>
				<li>Ical export bug when no categories selected for sites with multiple categories enabled</li>
			</ul>
		</li>
		<li>JEvents 3.4.55
			<ul>
				<li>Fixed triggerEvent to always pass array()</li>
			</ul>
		</li>
		<li>JEvents 3.4.54
			<ul>
				<li>Fixed php error on cached pages in 3.4.53</li>
			</ul>
		</li>
		<li>JEvents 3.4.53
			<ul>
				<li>Fixed editing single repeats - missing ev.ev_id column error</li>
			</ul>
		</li>
		<li>JEvents 3.4.52
			<ul>
				<li>Fixed saving events when multiple categories exist.</li>
			</ul>
		</li>
		<li>JEvents 3.4.51
			<ul>
				<li>Fixed to allow colour to be imported as X-COLOR in ical and csv imports</li>
				<li>Fixed to allow colour to be exported as X-COLOR in ical exports</li>
				<li>Added event creators user id output options in layout editor</li>
				<li>Fix to ignore category filter in calendar and latest events modules.</li>
				<li>add getMostRecentRepeat dbmodel method</li>
				<li>CSS fix for resizeable column headings</li>
				<li>timelimit fixes for finder plugin</li>
				<li>bootstrap 4 fix for modals not appearing</li>
				<li>Removed curl brackets from FROM queries ( ) for MariaDB 10.4 support</li>
				<li>Add tag / default loaded from template tag support for notification subject</li>
				<li>Finder plugin needs to save publish up and down times in UTC</li>
				<li>remove orphans from category mapping table automatically</li>
				<li>Add content plugin support for iCal Export</li>
				<li>Include Content plugins in iCal Export Event Descriptions</li>
				<li>Add round up Duration for event details {{DURATION_ROUNDUP}}</li>
				<li>Add a not empty check when building Category Select</li>
				<li>Added {{ALLCATEGORIES_CAT_COLOURED}} - List of categories, span wrapped and colour per category.</li>
				<li>Add allCategoriesColoured as a tag for Latest Events Module</li>
				<li>htmlspecialchars on $keyword before outputting it (to stop XSS)</li>
				<li>htmlspecialchars on event label in list of events (to stop XSS)</li>
				<li>fix for ignorecat filter</li>
				<li>implement getMostRecentRepeat</li>
				<li>Add new config option to allow NOT converting URLS in descriptions into links during import</li>
				<li>Add {{ISMUlTIDAY}} support.</li>
				<li>Added proxy check for ical URL. Thanks to sibelman</li>
				<li>Added additional object value catids which stores and array of the catids for multi-category support. Passed into the AfterSaveEvent so it can be tested / used in plugins.</li>
				<li>Fix include sub cat support for latest events module</li>
				<li>Fix iCalImport URL setting - regex needs - escaping</li>
			</ul>
		</li>
		<li>JEvents 3.4.50
			<ul>
				<li>Security fixes thanks to Hackmanit GmbH.&nbsp; Possible SQL injection in backend calendar editing, XSS in backend list filtering and frontend event searches - thanks to Hackmanit GmbH</li>
				<li>Catching sites that use LDJSON without upgrading standard images</li>
				<li>Re-Work category filter to follow Joomla! category filter, show published and unpublished. But hide if it is trashed or archived.</li>
				<li>Added more ordering options to the date range menu item, recently created ascending, recently edited ascending and descending first.</li>
			</ul>
		</li>
		<li>JEvents 3.4.49
			<ul>
				<li><strong>Add support for structured data output as per <a href="https://developers.google.com/search/docs/data-types/event">https://developers.google.com/search/docs/data-types/event</a></strong></li>
				<li>Language string and layout insertion for&nbsp; LDJSON output in event detail view</li>
				<li><strong>Latest events module - option to ignore newline to br in latest events module config</strong></li>
				<li>Fix to block showing trashed events to event creators in frontend admin panel</li>
				<li>Bootstrap 4 workaround for popovers</li>
				<li>Timelimit plugin wasn't being called in editing layout for event editing page</li>
				<li>Category Link in layout should go to category view! not week</li>
				<li>Allow club filters on manage events menu item type, users may want a specific page for X events.</li>
				<li>Add better support for Joomla note field type when editing parameters</li>
				<li>Fix for published filter when using admin panel in frontend</li>
				<li><strong>Support custom js and custom css in layout editor</strong></li>
				<li>Force end date same as start date if the event is anything but published in finder plugin to make sure its not indexed.</li>
				<li>Do not offer the apply button if the user is a guest.</li>
				<li>Added search by title and list limit filter for Repeating Events view.</li>
				<li>Remove broken file include JToolbarHelper is included by JViewLegacy</li>
				<li>Remove deprecated call to each in flat calendar.php</li>
				<li>Next repeat in JEvents backend list styling and language change</li>
				<li>Add in next repeat to timesheet where applicable, also show the timesheet to be the events actual time sheet.</li>
				<li>Child element should be centered too for bootstrap button styling</li>
				<li>Unauthorised ical reloads should throw a 403 rather than just an error message and home page redirect</li>
				<li>Fix for no-end time events not appearing when summer time ends</li>
				<li>Fix day view specific day selector for latest Joomla! calendar</li>
				<li>Format the background color cell as it is expected it outputs a colour not a hex code.</li>
				<li>Add config option to allow background color to be set for list of events view for each row.</li>
				<li>Improve date input selection and pre-select end date when creating events</li>
				<li>Added missing GWE Json language files</li>
				<li>Making finder indexing it's own to reduce overhead and only plugins which are needed have the function added.</li>
				<li>Finder Plugin - Call in customfieldsmultirow to get additional data for the events and support jevtimelimits start/end publish times.</li>
			</ul>
		</li>
		<li>JEvents 3.4.48<br>
			<ul>
				<li><strong>Fix for checkboxes in event editing in Joomla 3.8.12</strong></li>
				<li>Add ACL control over which users can set event priorities</li>
				<li>Timezone fix for daylight savings impact on all day events in latest events module</li>
			</ul>
		</li>
		<li>JEvents 3.4.47<br>
			<ul>
				<li><strong>Add abiility to sort categories by drag and drop when creating events to set the priority - the primary category will be the first in the list<br></strong></li>
				<li>Fix for event list filtering when canceling event creation</li>
				<li>Disable delays on popover to stop them flashing when the mouse is over the arrow on the calendar view</li>
				<li>Fix for modals appearing and disappearing instantly when editing events in popups</li>
				<li>Fix for irregular date picker where navigating the dates was causing dates to be picked as you webnt along</li>
				<li>Enable setting/saving of geolon and geolat in event edit - disabled by default</li>
				<li>Bootstrap 4 compatability changes</li>
				<li>Move setting of page title for event detail pages so Hidden Event Detail plugin can take effect</li>
				<li>Adding trigger to allow injecting view classes</li>
				<li>IOS 11.3+ scrolling issue on modals</li>
				<li>Enhance finder plugin to take account of publish date range</li>
				<li>Add CSV file pre-processor plugin hook so that non-standard CSV files can be accomodated with suitable plugin installed</li>
			</ul>
		</li>
		<li>JEvents 3.4.46<br> * Fix for badly packaged version 3.4.45 - sites unable to create events with 'cannot unset string offsets' error message</li>
		<li>JEvents 3.4.45<br> * Bug fixes from 3.4.44 - correct issue with emptying trash, use old popup calendar for sites prior to Joomla 3.7.0, fix for category filter for multi-category sites where only one category can be selected<br> * Correct latest events module display of all day event on day where DST/Summertime starts<br> * Workaround for Joomla 3.8.x issue with popup date selectors - affects some sites on event editing and users of time limit plugin</li>
		<li>JEvents 3.4.44<br> * Authorisation check on save should check creator and editor permissions<br> * Fix for colour picker being compromised by Joomla settings<br> * Allow jomsocial group events plugin to be picked up in edit page layout editor<br> * No-op code added to Category filter that can be used by an override to force category filters to apply to ALL selected filters and ANY of the selected filters<br> * Workaround for lack of support for electric calendar in recent Joomla date picker update<br> * Category selector replaced with style category name if only one choice is available<br> * Allow date formatting in layout editor to use date() function syntax as well as strftime format<br> * Replace deprecated Joomla method calls<br> * Improved meta tag setting <br> * PHP 7.2 Support Changes<br> * Fix for setting published state in iCal and csv import<br> * Update code to current standards in installer<br> * Add default all day event tick selection<br> * fixed nonendtime default selection.<br> * Fix noendtime config option, was not working. <br> * column selector should show textarea to allow advanced configuration<br> * Fix for default category setting when changing calendar in event creation<br> * Fix for timezone problems when editing repeats of existing events<br> * Add ical and edit dialogs as options for calendar cell<br> * calendar module navigation needs to handle UTF-8 data<br> * csv export should allow all events to be output even if block show all is enabled<br> * iCal export wasn't using maxYear and minYear so DST transitions were not always output<br> * gwejson library needs to add slashes to the message<br> * EXT Theme Fix Legend to show categories names always.<br> * Ext theme legend width to auto so nothing is cut off.<br> * Fix for default category setting when creating new event<br> * Fix guest check, WE only want to show the message IF NOT guest.<br> * Fix for missing cal_day declaration in default layout<br> * Add additional param to getListQuery to allow setting if list or item<br> * Added filtering to iCalRepeat to be same as iCalEvent</li>
		<li>v. 3.4.43 fix for importing non-repeating events , allow notifications to be sent for events created in backend, fix for save/create button labels being reversed in frontend event editing page</li>
		<li>v.3.4.42 Reverse smart search plugin change in 3.4.40 (blocked saving of trashed events) pending review, fix for repeating event where specific timezone is set (times where not adjusted correctly when re-editing event).</li>
		<li>v. 3.4.41 Fix problem with iCal imported events not displaying in version 3.4.40</li>
		<li>v. 3.4.40
			<p>* Move Timezone input to before date/time on event edit page (where enabled)<br> * Add loc_id integer field to database for locations to improve performance of DB queries on large sites<br> * Improve irregular date imports<br> * Add location index to event detail table for performance gains<br> * Fix toolbar buttons in backend<br> * Use Inner join instead of left join for performance gains<br> * stray use of | instead of $separator<br> * Allow contact field to be truncated in latest events module<br> * no end time should default to 0 not ""<br> * Allow CDATA through template replacer - was replacing closing ]] with }} which was not good for Google <br> * Fix for Authorised Users not being able to edit own events when they hit the event limit.<br> * Added where clause to smart search plugin to only find published events.<br> * Modify input sanitization where the user has allowed raw.<br> * Make sure visibile filters check only is applied when using JEvents filter module or modules with filters in their params<br>* Added new default option for no specific endtime.<br>* Disable system messages for iCal Reloading when a guest.<br>* Added new custom layout tag: {{{Past or Future:PAST_OR_FUTURE}} to output text past or future depending if the event is in the past or future. Handy for CSS Classing.<br>* Set admin emails to run through the defaultloadedtemplate.php parser. This means tags used in the Event Details custom layout can now be used within the admin emails.</p>
		</li>
		<li>V. 3.4.39 Added conditional check for J3.7.1 fixes, removed (int) conversions causing duration issues. Removed inline table styles on ext layout latest events.</li>
		<li>V. 3.4.38 Fixed saving issue.</li>
		<li>V. 3.4.37 For for checkbox and radio box required fields check<br>* Fix for no-end time events when editing specific repeats<br>* Added ability to select week start day within week view menu item to override the core selection.<br>* Code style improvements and deprecated class updates.<br>* Added published_fv to the publish/unpublish links within Manage Events View, this keeps the manage events filter state i.e. all events, published or un-published.<br>* Fixed issue with 504 Gateway timeout when saving on some servers, caused but using JRequest, replaced with JInput</li>
		<li>v. 3.4.36 Temporary workaround for bug in calendar date picker in Joomla 3.7.0, fix for toolbar styling in backend of Joomla 3.7.0</li>
		<li>v. 3.4.35 Joomla 3.7.0 compatability</li>
		<li>v. 3.4.34 * Latest events module option to show only repeating events<br>* Hardening of catid filtering<br>* New config option to allow dropping eventdetail from new SEF URLs for event detail<br>* Latest events module - option to not show any repeating events<br>* Correct confusing translations for years before and after now</li>
		<li>v. 3.4.33 * Fix for missing save buttons in frontend event creation, fix for missing custom css file for new installations.</li>
		<li>v. 3.4.32 * Use opacity to make unselected fieldsets on edit screen more obviously disabled<br>* Switch repeat types if grey boxes are clicked anywhere<br>* Correct getter method for undeclared variables</li>
		<li>v. 3.4.31 * Fix for modified column missing in the backend of JEvents and links to translations failing <br>* Add Permalink config option on event detail tab of JEvents config - all JEvents detail links can now be pointed to the same menu item regardless of the source<br>* Fix for uid column duplication during upgrades on some times</li>
		<li>v. 3.4.30
			<p>* Correct category image URL for sites with multiple categories but not all have images attached<br>* Add new JEvents getter plugin to allow plugins to push data into variables in a different way - needed for standard images update 3.4.9<br>* Layout customisation tool now inherits category specific layouts</p>
			<p>* Added Global option as default for show all repeats option in Menu Items.<br>* Force default time and date fields to be integers (some people had used 8:00pm instead of 20:00)<br>* preserve published state when copying and editing event<br>* move generation of access list to access field definition - will allow default value to be set in template override now<br>* required fields check fix for radio/check boxes<br>* export 500 characters of event description instead of just 100<br>* <strong>menu item filters setting parent categories will not pick up events in child categories unless they are specified too</strong><br>* Add option for date range view to show date as well as time in list presentation<br>* More robust check if event title is blank<br>* Add CATEGORY_ALIAS to layout editor <br>* Support for Joomla 3.7<br>* Language corrections<br>* Range views now always have a date column to the left. So tend not to need the date output again. This change is suggested by @BrianTeeman. Any users requiring the date and do so easily with the additional date string.<br>* Fixes issue where users not authorised to publish events auto-published new events.<br>* Added strings to .sys.ini for debugged permissions report.<br>* Fix for time offset applied twice when editing event in nonstandard timezone<br>* Translation support for edit tabs from custom fields<br>* Max year php_max_int constraint for annual repeating events<br>* Stop calendar mod direct links to event detail from including tmpl=component<br>* latest events process match to support formatting options from plugins</p>
		</li>
		<li>v. 3.4.29 * Skip irregular repeats in iCal export, CSS typo in Flat theme, fix for extra_info tags being stripped in some situations, ass support for SITEBASE and SITEROOT in layout editor, fix UTF-8 characters in calendar tooltips, Fix installation error message from MySQL</li>
		<li>v. 3.4.28 * add warning about using EOL versions of PHP, workaround for sites running php 5.3 (there will be no more of these), flat theme CSS typo correction, fix stripping spaces out of extra info when saving events, add sitebase and siteroot tage to layout editor and latest events custom format string.</li>
		<li>v. 3.4.27 * fix for sites not using TinyMCE or JCE editors, problem in 3.4.26</li>
		<li>v. 3.4.26 * Fix for reseting date filters in date range view * New config option to show multi-day events first in day list view<br>* Trap for memory overflow on sites with 60,000+ event creators<br>* Performance gain - no need to check category access in db queries since we already do this in getAccessibleCategories<br>* Support for %k in duration in latest events module * Code to handle bad TZID from microsoft imports was affecting descriptions and other fields with colons in them - resolve this.* Make publish own an ACL setting<br>* TinyMCE required description workarounds * fix for required description field - changes in TinyMCE<br>* allow irregular repeats to occur more than once on the same day.<br>* allow day list view to use float theme</li>
		<li>v. 3.4.25 Fix layout replacement error in 3.4.24</li>
		<li>v. 3.4.24 * Fix conditional custom fields showon for event edit fields on first tab<br>* Force Itemid in check conflict script&nbsp; - some routers/SEF addons were dropping this<br>* Latest events module default custom format string \n were not being parsed in default value&nbsp; - replace with &lt;br/&gt;<br>* Foundation for publish own permissions in ACL - not implemented yet<br>* Warning message when RSVP Pro and RSVP are both enabled<br>* Enable category images in calendar cell and tool tip layouts<br>* Allow gwejson to pick up custom versions of scripts instead of released ones to preserve customisations e.g. use gwesjon_custom_finduser.php to replace gwesjon_finduser.php<br>* Added Category Link Raw to layout editor<br>* Added Option to include event detail link in iCal Export, added set default menu item id in Joomla! config under ical export for the time being since it's only used here.<br>* Add error checks to return to referrer on cancel code<br>* Sends user back to previous url on cancel of event editing.<br>* Added a more meaningful title to toolbar and browser bar for JEvents cPanel<br>* Fix for filtermap fields install problem on some servers<br>* Comma separated by*day fields need to have spaces stripped during save process<br>* Make hide author setting apply throughout JEvents</li>
		<li>v. 3.4.23 * Fix for problem in install script for version 3.4.22</li>
		<li>v. 3.4.22 * Fix for column selection in list menu item<br>* Fix for deleting first repeat of repeats when repeat id = event id<br>* Allow saving and reloading of filter module data - start of re-working of filter system<br>* Make column selection in list view menu item sortable<br>* use showon for range view date settings to improve usability of configuration<br>* Make sure filter module sets option to com_jevents in the form incase the target menu item is not a JEvents one<br>* Fix for router where task could appear twice if not translated<br>* Make filter choices in module parameters sortable by drag and drop</li>
		<li>v. 3.4.20 * Fix DB query for SELECT event used in editor plugins etc.<br>* Work around for some imported all day event data representations<br>* Reset category filter when cancelling ical calendar edit<br>* Check for duplicate calendar names when creating new ones</li>
		<li>v. 3.4.19 * Issue with anonymous event creator name/email not appearing in notification messages.<br>* New installer plugin to manage installation of club addons<br>* Fix on setting limits on lists that were ignoring the max count set in menu items<br>* Fixed parameter saving for plugins where value is an array<br>* Updated defaultloadedtemplate to generate the correct menu item links if the view datamodel contains a menu item for conisitency and custom datacalls.</li>
		<li>v. 3.4.18 * Change to GWEJson plugin to fix issue with RSS feeds for some users in Joomla 3.6.2 * Add message to warn user if they create a self-overlapping event * Add max event option for category list view * Alternative View Search, was falling back to default due to class extends default when it should be alternative</li>
		<li>v. 3.4.17 * Fixed missing $rand in email cloak code changes to compensate for Joomla changes<br>* Remove workaround for problematic cloaking code introduced in Joomla 3.6.1 but then removed in 3.6.2<br>* Fixed group by issue arising from managed locations that cause some iCal exports to pick up the wrong start date<br>* Add config option for event list view to output events as a CSV File</li>
		<li>v. 3.1.16 * Fix for backend filtering of events by creator from 3.4.15<br>* workaround for cloaking change in Joomla 3.6.1<br>* upgrade isEventEditor method to take account of users who can only edit in specific categories</li>
		<li>v. 3.4.15 * Missing $rand in email cloak code<br>* re-instate filter of events created by unlogged in users in backend list of events</li>
		<li>v. 3.4.14 * Add latest events option to show most recently modified events<br>* More control options for notification messages for new events<br>* Fix for TinyMCE editor issue arising in Joomla 3.6.0<br>* Fix for router issue where tasks has a hyphen in their translations<br>* Fix for editing from an event detail popup and hitting close or save and close<br>* Fix for updating JEvents translations using Joomla Updater<br>* Fix for multiple instances of cloaked email within the same event detail page<br>* Add configuration options for icons to show on iCal export menu item<br>* Better scollable typeahead results<br>* Calendar popovers appear after 150milisecs to avoid lots appearing at once<br>* add chevrons and info icons to plugin configuration<br>* Fix duplicated output in flat theme date range view.<br>* Let category link in latest events module respect target menu item is using ${CATEGORYLNK}<br>* Enable configuration of all JEvents plugins via the main JEvents config/params page<br>* Allow filtering of all published JEvents layouts i.e. without need to check all the categories in turn<br>* Fix for jevFilter constructor names<br>* DTSTAMP for export of repeat exceptions should not have timezone in it<br>* Allow list of events view to be ordered with more choices<br>* Installer message when updating using Joomla updater was not being shown<br>* SMore flexibility on countdown output in latest events module<br>* Updated the google export to support http and https replacing to webcal://, fixes invalid email address issue when adding to google calendar.<br>* Updated constructor classes for PHP7 to avoid deprecated notices.<br>* Migrated from deprecated JApplication::stringURLSafe to JApplicationHelper::stringURLSafe<br>* Fixed Next and Previous repeat navigation in pop-ups. Previously the whole template was being loading within a modal when click next or previous repeat.<br>* Updated hardcoded english for iCal Repeat Deleted and iCal Repeats Deleted.<br>* Fixed undefined variable within iCalRepeat on redirect.<br>* Fixed jevuser.php where $idsstring was undefined, it should have been $idstring<br>* Migrated from deprecated JButton to JToolBarButton<br>* Declared editStrings variable in icalrepat/view.html.php to avoid a coding notice in code editor. Has not other effect but to be cleaner.<br>* Fixed $value implementation within jevtimezone.php JFormFieldJevtimeZone<br>* Updated License to correct Joomla! Version and wording<br>* Migrated from deprecated JString to StringHelper<br>* Migrated from deprecated JArrayHelper with ArrayHelper<br>* Set xhtml to false on JRoute on the Link within the default Later Events View to avoid failing urls.</li>
		<li>v. 3.4.13 Set default earliest/latest years to -1/+5.&nbsp; Workaround for CSV imports where descriptions wrap onto many lines within a cell. Mmissing language string in search plugin, improve styling of select list of events used in several addons, only display authorised user mode when enabled.</li>
		<li>v. 3.4.12 Fix for unspecified sender address in new admin email config setting</li>
		<li>v. 3.4.11 Add support for setting a timezone when editing an event (must be enabled in the JEvents configutation first), fix onSendAdminMail plugin (was being called in the wrong sequence), remove TZID from UTC timestamp in DTSTAMP, PHP7 deprecated warnings, fix for reloadall iCals, add EVID to layout editor, layout editor setting editor to none to use JEditor instead of getEditor, add float block layout to support float layouts, float theme language strings, add option to set sender for core JEvents emails</li>
		<li>v. 3.4.10 Fix for setting ACL permissions in Joomla 3.5+, fix image scaling issue in JEvents 3.4.9, Implement translatable customfields, force mini-cal to not use jQuery caching, new options tab for mini-calendar for better understanding</li>
		<li>v. 3.4.9 Fix for saving JEvents translations and minor fix in saving events - due to Joomla 3.5.0 code changes.</li>
		<li>v. 3.4.8 Fix for overnight repeating events less than 1 day long (problem introducted in 3.4.5 with Brazil timezone fix), workaround for Joomla 3.4 and 3.5 differences in jInput filtering assumptions.</li>
		<li>v. 3.4.7 Fix for error in modal window creation in JEvents 3.4.6</li>
		<li>v. 3.4.6 Joomla 3.5 and PHP 7.0 compatability changes, clean up duplicate layout entries in DB on some servers, add first and last repeat columns to event list layout options, config option to hide or show repeats in event list view, add RGBA opaque background colour option, fix for translation of locations overriding locations plugin output, set&nbsp; grey background of fieldsets using CSS classes rather than inliine CSS.</li>
		<li>v. 3.4.5 fix jump to month in geraint layout. config option to disable multi-day events, allow calendar module with tooltips to link directly to speciric event instead of day list, fix for conflict event checking on some servers, enable day selection for irregular repeating events as config option, fix required to support sized images in the images addon, improved error message if event cannot be viewed on the menu item on which iw was created, fix weely repeat by day date choice when start date is changed, fix for ical ownership during creation/editing, add locations etc. to sidebar, remove mootools usage in required fields checking, support jeventd start of week in joomla date picker, cache fix for popup print page, UTF-8 iCal import fixes, fixes for clock changes impacting on repeating events with special case handling for Brazil, fix for custom module field parsing when used in prejevents position, touch start support for mini-calendars</li>
		<li>v. 3.4.4 add manage event icon support in list views (via layout editor), use Jevents cache script to allow us to avoid Joomla cache on dbmodel queries, fix for keyword search query (caused a problem on some repeated events when location managed was installed), cralwer menu item to use at least 100 events at a time,&nbsp; fix for large dataset db query in backend, fix for canonical URL for event detail page, remove some deprecated function calls, add copyright comment helper method, fix for calendar tooltips (sometimes first date looked different to the others), add support for countdown in list views, add support for allcategoryimgs to layout editor, for for enddate/time issues for multi-day events with no end time in latest events module, run custom module through content plugins (to allow support for toggling etc.)</li>
		<li>v. 3.4.3 correct some system messages (was using non-standard types causing problems on some templates), fix for categroy filter for some users of the filter module where not all categories were visible, remove stray libraries from package</li>
		<li>v. 3.4.2 problem with category restrictions in latest events module in version 3.4.1</li>
		<li>v. 3.4.1 Trap for missing date pickers, remove reference to mootools in filter module, use joomla grid.checkall universally</li>
		<li>v. 3.4.0 Stable release - fix for customised layouts with multibyte content</li>
		<li>v. 3.4.0 RC6 security fix for risk of SQL injection - thanks to our friends at <a href="http://labs.integrity.pt">http://labs.integrity.pt</a> - Filipe Reis,Vitor Oliveira and Fabio Pires - for bringing it to our attention.</li>
		<li>v. 3.4.0 RC5 fix for multiple day selects when re-editing weekly repeating event, fix for hiding creator &amp; hits when not using customised event detail layout, fix for pagination on lists of events view</li>
		<li>v. 3.4.0 RC4 fix for saving latest events module settings</li>
		<li>v. 3.4.0 RC3 remove more MooTools code and redundant files,</li>
		<li>v. 3.4.0 RC Release candidate of JEvents 3.4 that removes usage of MooTools</li>
	</ul>
<?php

$changelog["package_jevents"]["archive"] = ob_get_clean();
