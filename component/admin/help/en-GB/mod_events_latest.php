<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: mod_events_latest.php 1794 2011-03-15 14:36:47Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Events - Help german language


defined( '_JEXEC' ) or die( 'Restricted access' );

$_cal_lang_lev_main_help = <<<END
<div style="width:300px">
<p>
	This module is designed to display upcoming events and possibly recent past events
	from the events component.  A set of optional parameters can be specified
	to alter the behavior of this module.
	Although this module is not packaged with the events component, it is available for download at
	<a href="http://www.jevents.net" title="JEvents" target="_blank">JEvents Projektseite</a>
	It will need to be installed separately and made visible (ie. published).
</p>
<p>
	The module outputs the events within an html table structure of n rows by 1 column, where n is
	the number of events displayed. The default max. number of events displayed is 5.
	This can be changed by alter a parameter value.
</p>

<b>CSS Styling:</b>

<p>
	Each event will display a start date and time in the first line of the table cell, followed by the
	event's title in a second line.  Both the date/time and event title are assigned separate css classes
	if style customization is desired.
</p>

<p>
	The first event displayed has its own css style assigned to it (mod_evets_latest_first).  Any events
	beyond the first will use a separate style (mod_events_latest).  This is done simply to control the
	appearance of a horizontal line which separates each event.
</p>

<p>
	Note that the latest events style classes can be integrated automatically with the module parameter
	defined by the module manager, but it's recommended for XHMTL compliance to include them
	manually into the event components stylesheet.
</p>
</div>
END;

$_cal_lang_lev_custformstr_help = <<<END
<div style="width:450px;font-size:xx-small;">
= string  Certain event fields can be specified as \${event_field} in the string.
If desired, the user can even specify overriding inline styles in the event format using &lt;div&gt; or
&lt;span&gt; to delineate.  Or the &lt;div&gt's or  &lt;span&gt's can actually reference new css style
classes which you can create in the template's css file.<br /><br />

= [cond: string ]  allows a customized string as described above but only is displayed if the condition "cond" is true.<br /><br />
	Available conditions:<br /><br />
	<b>a</b>&nbsp;event is a all-day-event<br />
	<b>!a</b>&nbsp;event is not a all-day-event<br /><br />

 Event fields available:
\${startDate}, \${eventDate}, \${endDate}, \${title}, \${category}, \${contact}, \${content}, \${addressInfo},
\${extraInfo},  \${createdByAlias}, \${createdByUserName}, \${createdByUserEmail}, \${createdByUserEmailLink},
\${eventDetailLink}, \${color}<br /><br /> The  \${startDate}, \${eventDate} and \${endDate} are special event
fields which can support further customization of the date and time display by allowing a user to specify
exactly how to display the date with identical format control codes to the PHP 'date()' or 'JevDate::strftime()
functions. If a '%' sign is detected in the format string, JevDate::strftime() is assumed to be used (<b>supports locale
international dates</b>).<br /><br /> 
An example of a specified date and time format used: \${startDate('D, M jS, Y, @g:ia')}<br />
This will display the date and time as: 'Fri, Oct 23rd, 2003, @7:30pm'<br /><br />

Note that the default customFormatStr is '\${eventDate}[!a: - \${endDate(%I:%M%p)}]&lt;br /&gt;\${title}' which will display the date of an
event, or the particular date of an event instance in the case of a repeating event type.
The time is not displayed in case of an all-day-event. Not specifying any date format specifiers within parenthesis of
the startDate, eventDate or endDate fields will result in a default format as shown above for language setting "english" and
'%a %b %d, %Y @%I:%M%p' for all other languages.
</div>
END;

$_cal_lang_lev_date_help = <<<END
<div style="width:450px;">
<p><b><u>php date() function format specifiers:</u></b></p>
<table cellpadding="0" cellspacing="0" style="table-layout:auto;vertical-align:text-top;font-size:xx-small">
<colgroup>
	<col style="width: 30px;vertical-align:text-top;">
	<col style="vertical-align:text-top;">
</colgroup>
<tbody style="font-size:xx-small">
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">a</td><td style="font-size: xx-small;">Lowercase Ante meridiem and Post meridiem am or pm</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">A</td><td style="font-size: xx-small;">Uppercase Ante meridiem and Post meridiem AM or PM</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">B</td><td style="font-size: xx-small;">Swatch Internet time 000 through 999</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">d</td><td style="font-size: xx-small;">Day of the month, 2 digits with leading zeros 01 to 31</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">D</td><td style="font-size: xx-small;">A textual representation of a day, three letters Mon through Sun</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">F</td><td style="font-size: xx-small;">A full textual representation of a month, such as January or March January through December</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">g</td><td style="font-size: xx-small;">12-hour format of an hour without leading zeros 1 through 12</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">G</td><td style="font-size: xx-small;">24-hour format of an hour without leading zeros 0 through 23</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">h</td><td style="font-size: xx-small;">12-hour format of an hour with leading zeros 01 through 12</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">H</td><td style="font-size: xx-small;">24-hour format of an hour with leading zeros 00 through 23</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">i</td><td style="font-size: xx-small;">Minutes with leading zeros 00 to 59</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">I</td><td style="font-size: xx-small;">(capital i) Whether or not the date is in daylights savings time 1 if Daylight Savings Time, 0 otherwise.</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">j</td><td style="font-size: xx-small;">Day of the month without leading zeros 1 to 31</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">l</td><td style="font-size: xx-small;">(lowercase 'L') A full textual representation of the day of the week Sunday through Saturday</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">L</td><td style="font-size: xx-small;">Whether it's a leap year 1 if it is a leap year, 0 otherwise.</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">m</td><td style="font-size: xx-small;">Numeric representation of a month, with leading zeros 01 through 12</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">M</td><td style="font-size: xx-small;">A short textual representation of a month, three letters Jan through Dec</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">n</td><td style="font-size: xx-small;">Numeric representation of a month, without leading zeros 1 through 12</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">O</td><td style="font-size: xx-small;">Difference to Greenwich time (GMT) in hours Example: +0200</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">r</td><td style="font-size: xx-small;">RFC 822 formatted date Example: Thu, 21 Dec 2000 16:01:07 +0200</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">s</td><td style="font-size: xx-small;">Seconds, with leading zeros 00 through 59</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">S</td><td style="font-size: xx-small;">English ordinal suffix for the day of the month, 2 characters st, nd, rd or th. Works well with j</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">t</td><td style="font-size: xx-small;">Number of days in the given month 28 through 31</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">T</td><td style="font-size: xx-small;">Timezone setting of this machine Examples: EST, MDT ...</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">U</td><td style="font-size: xx-small;">Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">w</td><td style="font-size: xx-small;">Numeric representation of the day of the week 0 (for Sunday) through 6 (for Saturday)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">W</td><td style="font-size: xx-small;">ISO-8601 week number of year, weeks starting on Monday (added in PHP 4.1.0) Example: 42 (the 42nd week in the year)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">Y</td><td style="font-size: xx-small;">A full numeric representation of a year, 4 digits Examples: 1999 or 2003</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">y</td><td style="font-size: xx-small;">A two digit representation of a year Examples: 99 or 03</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">z</td><td style="font-size: xx-small;">The day of the year 0 through 366</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">Z</td><td style="font-size: xx-small;">Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. -43200 through 43200</td></tr>
</tbody>
</table>
</div>
END;

$_cal_lang_lev_strftime_help = <<<END
<div style="width:450px;">
<p><b><u>php JevDate::strftime() function format specifiers(formats according your system locale setting):</u></b></p>
<table cellpadding="0" cellspacing="0" style="table-layout:auto;vertical-align:text-top;font-size:xx-small">
<colgroup>
	<col style="width: 30px;vertical-align:text-top;">
	<col style="vertical-align:text-top;">
</colgroup>
<tbody style="font-size:xx-small">
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%a</td><td style="font-size: xx-small">abbreviated weekday name according to the current locale</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%A</td><td style="font-size: xx-small">full weekday name according to the current locale</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%b</td><td style="font-size: xx-small;">abbreviated month name according to the current locale</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%B</td><td style="font-size: xx-small;">full month name according to the current locale</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%c</td><td style="font-size: xx-small;">preferred date and time representation for the current locale</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%C</td><td style="font-size: xx-small;">century number (the year divided by 100 and truncated to an integer, range 00 to 99)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%d</td><td style="font-size: xx-small;">day of the month as a decimal number (range 01 to 31)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%D</td><td style="font-size: xx-small;">same as %m/%d/%y</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%e</td><td style="font-size: xx-small;">day of the month as a decimal number, a single digit is preceded by a space (range ' 1' to '31')</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%g</td><td style="font-size: xx-small;">like %G, but without the century.</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%G</td><td style="font-size: xx-small;">The 4-digit year corresponding to the ISO week number (see %V).
This has the same format and value as %Y, except that if the ISO week number belongs to the previous
 or next year, that year is used instead.</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%h</td><td style="font-size: xx-small;">same as %b</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%H</td><td style="font-size: xx-small;">hour as a decimal number using a 24-hour clock (range 00 to 23)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%I</td><td style="font-size: xx-small;">hour as a decimal number using a 12-hour clock (range 01 to 12)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%j</td><td style="font-size: xx-small;">day of the year as a decimal number (range 001 to 366)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%m</td><td style="font-size: xx-small;">month as a decimal number (range 01 to 12)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%M</td><td style="font-size: xx-small;">minute as a decimal number</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%n</td><td style="font-size: xx-small;">newline character</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%p</td><td style="font-size: xx-small;">either 'am' or 'pm' according to the given time value, or the corresponding strings for the current locale</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%r</td><td style="font-size: xx-small;">time in a.m. and p.m. notation</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%R</td><td style="font-size: xx-small;">time in 24 hour notation</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%S</td><td style="font-size: xx-small;">second as a decimal number</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%t</td><td style="font-size: xx-small;">tab character</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%T</td><td style="font-size: xx-small;">current time, equal to %H:%M:%S</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%u</td><td style="font-size: xx-small;">weekday as a decimal number [1,7], with 1 representing Monday</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%U</td><td style="font-size: xx-small;">week number of the current year as a decimal number, starting with the first Sunday as the first day of the first week</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%V</td><td style="font-size: xx-small;">The ISO 8601:1988 week number of the current year as a decimal number, range 01 to 53, where week 1 is the first week
 that has at least 4 days in the current year, and with Monday as the first day of the week. (Use %G or %g for the year
  component that corresponds to the week number for the specified timestamp.)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%W</td><td style="font-size: xx-small;">week number of the current year as a decimal number, starting with the first Monday as the first day of the first week</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%w</td><td style="font-size: xx-small;">day of the week as a decimal, Sunday being 0</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%x</td><td style="font-size: xx-small;">preferred date representation for the current locale without the time</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%X</td><td style="font-size: xx-small;">preferred time representation for the current locale without the date</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%y</td><td style="font-size: xx-small;">year as a decimal number without a century (range 00 to 99)</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%Y</td><td style="font-size: xx-small;">year as a decimal number including the century</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%Z</td><td style="font-size: xx-small;">time zone or name or abbreviation</td></tr>
<tr style="vertical-align:text-top;"><td style="font-size: xx-small;">%%</td><td style="font-size: xx-small;">a literal '%' character</td></tr>
</tbody>
</table>
</div>
END;
?>
