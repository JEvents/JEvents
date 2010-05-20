Mambo 4.5  Latest Events Module
===============================

* Mar 28/04, rev 1.2  Dave McDonnell
*
* Interfaces to Eric Lamette's Events Component, rev RC 2 - 5a

This module is designed to display upcoming events and possibly recent past events
from the events component provided by Eric Lamette.  A set of optional parameters can be specified
to alter the behavior of this module via the administration module management parameters window.


Output Format:
==============

The module outputs the events within an html table structure of n rows by 1 column, where n is
the number of events displayed.  The absolute max. number of events that can be displayed is 10 (for
those that are php literate, this is easily changed in the code if desired.).  The default
max. number of events displayed is 5.  This can be changed by specifying the parameter 'maxEvents'
to the module thru the admin interface.

**New for rev. 1.1 is the ability to customize the event's display format by specifying a new module parameter 
called 'customFormatStr'.  This parameter can accept raw html with event variables embedded within such as 
the event's date, title, category, etc.  The display format of the date/time is fully customizable by supplying
format specifiers compatible to php's date() or strftime() functions.  By default, the module supports all
languages supported by php, by displaying the event date and time according to the current locale (ie. strftime 
function).  See the 'The customFormatStr Parameter' below.

**New for rev 1.2:  added another custom field variable called ${eventDate} which represents the actual
date of an event, or in the case of a repeating type event, the date of this particular occurence of the
event.  Normally, for a non-repeating event type, the ${startDate} and ${eventDate} will be the same.
Additionally, the default format string is changed to: '${eventDate}<br />${title}'.  The hours, minutes,
and seconds of ${eventsDate} is the same as those for ${startDate}.

Also added a new module parameter: 'norepeat' whose default value is 0.  If set to 1, any events which
have a repeat type will only appear once in the latest events output.

CSS Styling:
============

Each event will display a start date and time in the first line of the table cell, followed by the 
event's title in a second line.  Both the date/time and event title are assigned separate css classes
if style customization is desired. 

The first event displayed has its own css style assigned to it (mod_evets_latest_first).  Any events
beyond the first will use a separate style (mod_events_latest).  This is done simply to control the
appearance of a horizontal line which separates each event.


Module Parameters:
==================

This module will support the use of the following optional customization parameters thru the
Mambo admin module management interface.  These parameters mostly alter the behavior of the
module by specifying a date range relative to the current day to look for events to display.
There is also another parameter called 'displayLinks' which if set to 1, will display the event's
title as a link to the event detail form within the events component module.

Parameters Definitions:
=======================

maxEvents = max. no. of events to display in the module (1 to 10, default is 5)

mode:
  = 0  (default) display closest events for current week and following week only up to 'maxEvents'.

  = 1  same as 'mode'=0 except some past events for the current week will also be
       displayed if num of future events is less than $maxEvents.

  = 2  display closest events for +'days' range relative to current day up to $maxEvents.

  = 3  same as mode 2 except if there are < 'maxEvents' in the range,
       then display past events within -'days' range relative to current day.

  = 4  display closest events for current month up to 'maxEvents' relative to current day.

days: (default=7) range of days relative to current day to display events for mode 1 or 3.

displayLinks = 1 (default is 0) display event titles as links to the 'view_detail' com_events
                   task which will display details of the event.

New for ver 1.01:
=================

displayYear = 1 (default is 0) display the year (ie. YYYY) in the event's date field.

New for ver 1.1:
================

disableDateStyle = 1 (default is 0) do not apply the default mod_events_latest_date css class to the date field
disableTitleStyle = 1 (default is 0) do not apply the default mod_events_latest_content css class to the title field
 
The above two parameters are introduced in order to allow full customization thru the 'customFormatStr' parameter.  For
complex custom formatting, you may want to apply new css style classes of your own.

New for ver 1.2:
================

norepeat = 1 (default is 0) any events which have a repeat type will only appear once in the latest events output.

The customFormatStr Parameter:
==============================
customFormatStr = string (default is null).  allows a customized specification of the desired event fields and
               format to be used to display the event in the module.  The string can specify html directly.
               As well, certain event fields can be specified as ${event_field} in the string.  If desired,
               the user can even specify overriding inline styles in the event format using <div> or <span>
               to delineate.  Or the <div>'s or <span>'s can actually reference new css style classes which you
               can create in the template's css file.

               Event fields available:  ${startDate}, ${eventDate}, ${endDate}, ${title}, ${category}, ${contact}, ${content},
               ${addressInfo}, ${extraInfo}, ${createdByAlias}, ${createdByUserName}, ${createdByUserEmail},
               ${createdByUserEmailLink}, ${eventDetailLink}, ${color}

               The ${startDate}, ${eventDate} and ${endDate} are special event fields which can support further customization
               of the date and time display by allowing a user to specify exactly how to display the date with
               identical format control codes to the PHP 'date()' or 'strftime() functions. If a '%' sign is
               detected in the format string, strftime() is assumed to be used (supports locale international dates).

               An example of a specified date and time format used: ${startDate('D, M jS, Y, @g:ia')}
               This will display the date and time as: 'Fri, Oct 23rd, 2003, @7:30pm'

Note that the default customFormatStr is '${eventDate}<br />${title}' (changed in rev 1.2) which will display the date of an
event, or the particular date of an event instance in the case of a repeating event type.  Not specifying any date format
specifiers within parenthesis of the startDate, eventDate or endDate fields will result in a default format as shown above.

For those not familiar with these date/time format specifiers, you can view the php manual online. A quick 
reference for these codes is shown below.  Note that you can only use the date() or the strftime() specifiers
separately.  You  cannot mix them from each table below.


php date() function format specifiers:
======================================
a        Lowercase Ante meridiem and Post meridiem am or pm 
A        Uppercase Ante meridiem and Post meridiem AM or PM 
B        Swatch Internet time 000 through 999 
d        Day of the month, 2 digits with leading zeros 01 to 31 
D        A textual representation of a day, three letters Mon through Sun 
F        A full textual representation of a month, such as January or March January through December 
g        12-hour format of an hour without leading zeros 1 through 12 
G        24-hour format of an hour without leading zeros 0 through 23 
h        12-hour format of an hour with leading zeros 01 through 12 
H        24-hour format of an hour with leading zeros 00 through 23 
i        Minutes with leading zeros 00 to 59 
I        (capital i) Whether or not the date is in daylights savings time 1 if Daylight Savings Time, 0 otherwise. 
j        Day of the month without leading zeros 1 to 31 
l        (lowercase 'L') A full textual representation of the day of the week Sunday through Saturday 
L        Whether it's a leap year 1 if it is a leap year, 0 otherwise. 
m        Numeric representation of a month, with leading zeros 01 through 12 
M        A short textual representation of a month, three letters Jan through Dec 
n        Numeric representation of a month, without leading zeros 1 through 12 
O        Difference to Greenwich time (GMT) in hours Example: +0200 
r        RFC 822 formatted date Example: Thu, 21 Dec 2000 16:01:07 +0200 
s        Seconds, with leading zeros 00 through 59 
S        English ordinal suffix for the day of the month, 2 characters st, nd, rd or th. Works well with j 
t        Number of days in the given month 28 through 31 
T        Timezone setting of this machine Examples: EST, MDT ... 
U        Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT) See also time() 
w        Numeric representation of the day of the week 0 (for Sunday) through 6 (for Saturday) 
W        ISO-8601 week number of year, weeks starting on Monday (added in PHP 4.1.0) Example: 42 (the 42nd week in the year) 
Y        A full numeric representation of a year, 4 digits Examples: 1999 or 2003 
y        A two digit representation of a year Examples: 99 or 03 
z        The day of the year 0 through 366 
Z        Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. -43200 through 43200 

php strftime() function format specifiers:
==========================================
%a       abbreviated weekday name according to the current locale 
%A       full weekday name according to the current locale 
%b       abbreviated month name according to the current locale 
%B       full month name according to the current locale 
%c       preferred date and time representation for the current locale 
%C       century number (the year divided by 100 and truncated to an integer, range 00 to 99) 
%d       day of the month as a decimal number (range 01 to 31) 
%D       same as %m/%d/%y 
%e       day of the month as a decimal number, a single digit is preceded by a space (range ' 1' to '31') 
%g       like %G, but without the century. 
%G       The 4-digit year corresponding to the ISO week number (see %V). This has the same format and value as %Y, except that if the ISO week number belongs to the previous or next year, that year is used instead. 
%h       same as %b 
%H       hour as a decimal number using a 24-hour clock (range 00 to 23) 
%I       hour as a decimal number using a 12-hour clock (range 01 to 12) 
%j       day of the year as a decimal number (range 001 to 366) 
%m       month as a decimal number (range 01 to 12) 
%M       minute as a decimal number 
%n       newline character 
%p       either `am' or `pm' according to the given time value, or the corresponding strings for the current locale 
%r       time in a.m. and p.m. notation 
%R       time in 24 hour notation 
%S       second as a decimal number 
%t       tab character 
%T       current time, equal to %H:%M:%S 
%u       weekday as a decimal number [1,7], with 1 representing Monday 
%U       week number of the current year as a decimal number, starting with the first Sunday as the first day of the first week 
%V       The ISO 8601:1988 week number of the current year as a decimal number, range 01 to 53, where week 1 is the first week that has at least 4 days in the current year, and with Monday as the first day of the week. (Use %G or %g for the year component that corresponds to the week number for the specified timestamp.) 
%W       week number of the current year as a decimal number, starting with the first Monday as the first day of the first week 
%w       day of the week as a decimal, Sunday being 0 
%x       preferred date representation for the current locale without the time 
%X       preferred time representation for the current locale without the date 
%y       year as a decimal number without a century (range 00 to 99) 
%Y       year as a decimal number including the century 
%Z       time zone or name or abbreviation 
%%       a literal `%' character 


Using the Module Parameters:
============================
To specify these parameters in the module's admin management interface, insert them into the 
'parameters' text window at the very bottom on separate lines like so:

mode=1
days=3
displayLinks=0
customFormatStr='${startDate('M jS, g:ia -')}${endDate(' g:ia')}<br />${category}: ${title}'

The customFormatStr example shown above will display the date/time as: 'Oct 23rd, 7:30pm - 8:30pm', and the category
and title on the following line.

Other Not-so-Obvious event Variables:
=====================================

${createdByUserEmail}		- creator's email address

${createdByUserEmailLink}	- this provides a link address to invoke an email form to send mail to the event creator.
				To create a hyperlink with this, it would be used like:  <a href=${createdByUserEmailLink}>inquire</a>

${eventDetailLink}		- a link to the event details page within the events component.  Note that this is a little
				different to the use of the event link on the event title field as enabled by the 'displayLinks'
				parameter.  An example of the use of this variable is shown in the section below.

${color}			- the assigned 'color' for the event as was done when the event was created in the event
				component.  This can be used in an inline css style within the ${customFormatStr} to control
				background color, font color.


Further Examples for Using the '${customFormatStr}' Parameter:
=============================================================

Ex. 1.
------

displayLinks=1
mode=0
customFormatStr='<div style='background-color:${color};'>${startDate('m/d/y')}, ${title}</div>'

This will display a simple date in MM/DD/YY format followed by the hyperlinked event title, with the same background
color assigned as when the event was created in the events component.  You need to be careful using this since there
currently is no foreground color selection in events.  So depending on colors, you can get poor contrast or sometimes 
the text can disappear into the background if the same color.

Ex. 2.
------

displayLinks=1
disableDateStyle=1
disableContentStyle=1
mode=2
days=14
customFormatStr='<span class='mod_events_latest_date'>${startDate('%x, %X - ')}${endDate('%X')}</span><br /><span class='mod_events_latest_content'>${category}: ${title}</span>'

This example will print the date and time in a default 'preferred' format according to the locale, whatever that may be
with respect the the php function 'strftime()'.  The appropriate language will be used, whether that be english, german, french,
or anything else.  Notice that the 'disableDateStyle' and 'disableContentStyle' parameters are used to suppress the module's 
autoformatting of these around the ${startDate}, ${endDate}, or ${title} fields since we are using several custom fields with
embedded text.  So it is more appropriate to explicitly specify the css class names using a HTML <span> element to delineate
the text to be formatted.

Also note that text will wrap around end of the module's right edge if there is not enough room on the line.  You will either 
need to downsize display fonts thru style control, widen the left display area for modules in your mambo template, or
some other method.

Ex. 3.
------

displayLinks=0
customFormatStr='<div style='cursor: default;' onMouseOver="this.style.border='thin dotted red';" onMouseOut="this.style.border='none';" onclick="parent.location = ${eventDetailLink};">${startDate}<br />${title}</div>'

This is a fairly sophisticated example using inline css styles and mouse events, but it demonstrates the power of the new
customFormatStr fairly well.  These parameter settings above will display the standard date and title fields of the event.
However, now when the cursor is over ANY part of an event's info, a red dotted border surrounds the event.  Clicking the
mouse will now display the event's detail from the events component in the main area of the page.  Basically, what is
being done is a hyperlink.


==============================================

And that's all folks!  You can report bugs to me thru the Modules forum on mamboserver.com  I am
open to suggestions to make the module more generic for people's needs, but I won't do
customization which involves a lot of time.

Cheers,

Dave McDonnell
Sacramento, CA USA
davemac2@yahoo.com
