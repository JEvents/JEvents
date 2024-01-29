<?php
/**
 * See https://github.com/alphp/strftime/blob/master/src/php-8.1-strftime.php
 *
 */

namespace PHP81_BC;

defined('_JEXEC') or die('Restricted access');

use DateTime;
use DateTimeZone;
use DateTimeInterface;
use Joomla\CMS\Language\Text;
use IntlDateFormatter;
use IntlGregorianCalendar;
use InvalidArgumentException;

 function dayToString($day, $abbr = false)
{
    switch ($day) {
        case 0:
            return $abbr ? Text::_('SUN') : Text::_('SUNDAY');
        case 1:
            return $abbr ? Text::_('MON') : Text::_('MONDAY');
        case 2:
            return $abbr ? Text::_('TUE') : Text::_('TUESDAY');
        case 3:
            return $abbr ? Text::_('WED') : Text::_('WEDNESDAY');
        case 4:
            return $abbr ? Text::_('THU') : Text::_('THURSDAY');
        case 5:
            return $abbr ? Text::_('FRI') : Text::_('FRIDAY');
        case 6:
            return $abbr ? Text::_('SAT') : Text::_('SATURDAY');
    }
}

function monthToString($month, $abbr = false)
{
    switch ($month) {
        case 1:
            return $abbr ? Text::_('JANUARY_SHORT') : Text::_('JANUARY');
        case 2:
            return $abbr ? Text::_('FEBRUARY_SHORT') : Text::_('FEBRUARY');
        case 3:
            return $abbr ? Text::_('MARCH_SHORT') : Text::_('MARCH');
        case 4:
            return $abbr ? Text::_('APRIL_SHORT') : Text::_('APRIL');
        case 5:
            return $abbr ? Text::_('MAY_SHORT') : Text::_('MAY');
        case 6:
            return $abbr ? Text::_('JUNE_SHORT') : Text::_('JUNE');
        case 7:
            return $abbr ? Text::_('JULY_SHORT') : Text::_('JULY');
        case 8:
            return $abbr ? Text::_('AUGUST_SHORT') : Text::_('AUGUST');
        case 9:
            return $abbr ? Text::_('SEPTEMBER_SHORT') : Text::_('SEPTEMBER');
        case 10:
            return $abbr ? Text::_('OCTOBER_SHORT') : Text::_('OCTOBER');
        case 11:
            return $abbr ? Text::_('NOVEMBER_SHORT') : Text::_('NOVEMBER');
        case 12:
            return $abbr ? Text::_('DECEMBER_SHORT') : Text::_('DECEMBER');
    }
}

function strftime (string $format, $timestamp = null) : string {

    if (version_compare(PHP_VERSION, "8.2", "lt"))
    {
        return @\strftime($format, $timestamp);
    }

    $intl_formatter = function (DateTimeInterface $timestamp, string $format)  {
        if (class_exists("IntlDateFormatter"))
        {
            $intl_formats = [
                '%a' => 'EEE',	// An abbreviated textual representation of the day	Sun through Sat
                '%A' => 'EEEE',	// A full textual representation of the day	Sunday through Saturday
                '%b' => 'MMM',	// Abbreviated month name, based on the locale	Jan through Dec
                '%B' => 'MMMM',	// Full month name, based on the locale	January through December
                '%h' => 'MMM',	// Abbreviated month name, based on the locale (an alias of %b)	Jan through Dec
            ];

            $tz        = $timestamp->getTimezone();
            $date_type = IntlDateFormatter::FULL;
            $time_type = IntlDateFormatter::FULL;
            $pattern   = '';

            switch ($format)
            {
                // %c = Preferred date and time stamp based on locale
                // Example: Tue Feb 5 00:45:10 2009 for February 5, 2009 at 12:45:10 AM
                case '%c':
                    $date_type = IntlDateFormatter::LONG;
                    $time_type = IntlDateFormatter::SHORT;
                    break;

                // %x = Preferred date representation based on locale, without the time
                // Example: 02/05/09 for February 5, 2009
                case '%x':
                    $date_type = IntlDateFormatter::SHORT;
                    $time_type = IntlDateFormatter::NONE;
                    break;

                // Localized time format
                case '%X':
                    $date_type = IntlDateFormatter::NONE;
                    $time_type = IntlDateFormatter::MEDIUM;
                    break;

                default:
                    $pattern = $intl_formats[$format];
            }

            // In October 1582, the Gregorian calendar replaced the Julian in much of Europe, and
            //  the 4th October was followed by the 15th October.
            // ICU (including IntlDateFormattter) interprets and formats dates based on this cutover.
            // Posix (including strftime) and timelib (including DateTimeImmutable) instead use
            //  a "proleptic Gregorian calendar" - they pretend the Gregorian calendar has existed forever.
            // This leads to the same instants in time, as expressed in Unix time, having different representations
            //  in formatted strings.
            // To adjust for this, a custom calendar can be supplied with a cutover date arbitrarily far in the past.
            $calendar = IntlGregorianCalendar::createInstance();
            $calendar->setGregorianChange(PHP_INT_MIN);

            // get current locale
            $locale = setlocale(LC_TIME, '0');
            // remove trailing part not supported by ext-intl locale
            $locale = preg_replace('/[^\w-].*$/', '', $locale);

            try
            {
                $dateFormatter = new IntlDateFormatter( $locale, $date_type, $time_type, $tz, $calendar, $pattern );
                $iFormat       = $dateFormatter->format( $timestamp );
            }
            catch (\Throwable $e)
            {
                echo $e->getMessage() . "<br>";
                echo "Locale = $locale<br>";
                echo "pattern = $pattern<br>";
                return 'Y-m-d H:i:s';
            }
            return $iFormat;
        }
        else
        {
            $mapping = [
                // time
                '%X' => 'H:i:s', // 29, Based on locale without date

                // date stamps
                '%c' => 'Y-m-d H:i:s', // 32, Date and time stamps based on locale
                '%x' => 'Y-m-d', // 36, Date stamps based on locale

            ];

            foreach ($mapping as $index => $value) {
                $format = str_replace($index, $value, $format);
            }

            return $format;
        }
    };

    // Same order as https://www.php.net/manual/en/function.strftime.php
    $translation_table = [
        // Day
        '%a' => function ($timestamp) {
            return dayToString(date('w', $timestamp), true);
        },
        '%A' => function ($timestamp) {
            return dayToString(date('w', $timestamp), false);
        },
        '%d' => 'd',
        '%e' => function ($timestamp) {
            return sprintf('% 2u', date('j', $timestamp));
        },
        '%j' => function ($timestamp) {
            // Day number in year, 001 to 366
            return sprintf('%03d', date('z', $timestamp) + 1);
        },
        '%u' => 'N',
        '%w' => 'w',

        // Week
        '%U' => function ($timestamp) {
            // Number of weeks between date and first Sunday of year
            $day = new DateTime(sprintf('%d-01 Sunday', date('Y', $timestamp)));
            return sprintf('%02u', 1 + (date('z', $timestamp) - $day->format('z')) / 7);
        },
        '%V' => 'W',
        '%W' => function ($timestamp) {
            // Number of weeks between date and first Monday of year
            $day = new DateTime(sprintf('%d-01 Monday', date('Y', $timestamp)));
            return sprintf('%02u', 1 + (date('z', $timestamp) - $day->format('z')) / 7);
        },

        // Month
        '%m' => 'm',
        '%b' => function ($timestamp) {
            return monthToString(date('n', $timestamp), true);
        },
        '%B' => function ($timestamp) {
            return monthToString(date('n', $timestamp), false);
        },
        '%h' => function ($timestamp) {
            return monthToString(date('n', $timestamp), true);
        },

        // Year
        '%C' => function ($timestamp) {
            // Century (-1): 19 for 20th century
            return floor(date('Y', $timestamp) / 100);
        },
        '%g' => function ($timestamp) {
            return substr(date('0', $timestamp), -2);
        },
        '%G' => 'o',
        '%y' => 'y',
        '%Y' => 'Y',

        // Time
        '%H' => 'H',
        '%k' => function ($timestamp) {
            return sprintf('% 2u', date('G', $timestamp));
        },
        '%I' => 'h',
        '%l' => function ($timestamp) {
            return sprintf('% 2u', date('g', $timestamp));
        },
        '%M' => 'i',
        '%p' => 'A', // AM PM (this is reversed on purpose!)
        '%P' => 'a', // am pm
        '%r' => 'h:i:s A', // %I:%M:%S %p
        '%R' => 'H:i', // %H:%M
        '%S' => 's',
        '%T' => 'H:i:s', // %H:%M:%S
//        '%X' =>

        // Timezone
        '%z' => 'O',
        '%Z' => 'T',

        // Time and Date Stamps
        '%D' => 'm/d/Y',
        '%F' => 'Y-m-d',
        '%s' => 'U',

        // Preferred date and time stamp based on locale
        '%c' => $intl_formatter,
        // Preferred time representation based on locale, without the date
        '%X' => $intl_formatter,
        // Preferred date representation based on locale, without the time
        '%x' => $intl_formatter,
    ];


    $out = preg_replace_callback('/(?<!%)%([_#-]?)([a-zA-Z])/', function ($match) use ($translation_table, $timestamp) {
        $prefix = $match[1];
        $char = $match[2];
        $pattern = '%'.$char;
        if ($pattern == '%n') {
            return "\n";
        } elseif ($pattern == '%t') {
            return "\t";
        }

        if (!isset($translation_table[$pattern])) {
            $replace = str_replace("%", "", $pattern);
        }
        else
        {
            $replace = $translation_table[$pattern];
        }

        if (is_string($replace)) {
            $result = date($replace, $timestamp);
        } else {
            $result = $replace($timestamp, $pattern);
        }

        switch ($prefix) {
            case '_':
                // replace leading zeros with spaces but keep last char if also zero
                return preg_replace('/\G0(?=.)/', ' ', $result);
            case '#':
            case '-':
                // remove leading zeros but keep last char if also zero
                return preg_replace('/^0+(?=.)/', '', $result);
        }

        return $result;
    }, $format);

  //  echo "old = $oldVersion new = $out <br>";
    return $out;
}
