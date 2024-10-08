<?php

// No direct access
defined('JPATH_BASE') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseDriver;


// on some servers with Xcache both classes seem to be 'compiled' and it throws an error but if we add this second test its ok - go figure .
if (!defined("JEVDATE"))
{
	define("JEVDATE", 1);

    if (version_compare(JVERSION, '5.0.0', 'ge'))
    {
        #[\AllowDynamicProperties]
        class JevDate extends Date {

            public $mytz;

            /**
             * Constructor.
             *
             * @param   string    String in a format accepted by JevDate::strtotime(), defaults to "now".
             * @param   mixed    Time zone to be used for the date.
             *
             * @return    void
             * @throws    JException
             * @since    1.5
             *
             */
            public function __construct( $date = 'now', $tz = null ) {

                // Create the base GMT and server time zone objects.
                if ( empty( self::$gmt ) )
                { //|| empty(self::$stz)) {
                    self::$gmt = new DateTimeZone( 'GMT' );
                    //self::$stz = new DateTimeZone(@date_default_timezone_get());
                }
                // Must get this each time otherwise modules can't set their own timezone
                $compparams = ComponentHelper::getParams( JEV_COM_COMPONENT );
                $jtz        = $compparams->get( "icaltimezonelive", "" );
                if ( $jtz != "" )
                {
                    self::$stz = new DateTimeZone( $jtz );
                }
                else
                {
                    self::$stz = new DateTimeZone( @date_default_timezone_get() );
                }
                $this->mytz = self::$stz;

                // If the time zone object is not set, attempt to build it.
                if ( ! ( $tz instanceof DateTimeZone ) )
                {
                    if ( $tz === null )
                    {
                        $tz = self::$gmt;
                    }
                    elseif ( is_numeric( $tz ) )
                    {
                        if ( ! isset( $this->offsets ) )
                        {
                            $this->offsets = array(
                                '-12'   => 'Etc/GMT-12',
                                '-11'   => 'Pacific/Midway',
                                '-10'   => 'Pacific/Honolulu',
                                '-9.5'  => 'Pacific/Marquesas',
                                '-9'    => 'US/Alaska',
                                '-8'    => 'US/Pacific',
                                '-7'    => 'US/Mountain',
                                '-6'    => 'US/Central',
                                '-5'    => 'US/Eastern',
                                '-4.5'  => 'America/Caracas',
                                '-4'    => 'America/Barbados',
                                '-3.5'  => 'Canada/Newfoundland',
                                '-3'    => 'America/Buenos_Aires',
                                '-2'    => 'Atlantic/South_Georgia',
                                '-1'    => 'Atlantic/Azores',
                                '0'     => 'Europe/London',
                                '1'     => 'Europe/Amsterdam',
                                '2'     => 'Europe/Istanbul',
                                '3'     => 'Asia/Riyadh',
                                '3.5'   => 'Asia/Tehran',
                                '4'     => 'Asia/Muscat',
                                '4.5'   => 'Asia/Kabul',
                                '5'     => 'Asia/Karachi',
                                '5.5'   => 'Asia/Calcutta',
                                '5.75'  => 'Asia/Katmandu',
                                '6'     => 'Asia/Dhaka',
                                '6.5'   => 'Indian/Cocos',
                                '7'     => 'Asia/Bangkok',
                                '8'     => 'Australia/Perth',
                                '8.75'  => 'Australia/West',
                                '9'     => 'Asia/Tokyo',
                                '9.5'   => 'Australia/Adelaide',
                                '10'    => 'Australia/Brisbane',
                                '10.5'  => 'Australia/Lord_Howe',
                                '11'    => 'Pacific/Kosrae',
                                '11.5'  => 'Pacific/Norfolk',
                                '12'    => 'Pacific/Auckland',
                                '12.75' => 'Pacific/Chatham',
                                '13'    => 'Pacific/Tongatapu',
                                '14'    => 'Pacific/Kiritimati'
                            );
                        }
                        // Translate from offset.
                        $tz = new DateTimeZone( $this->offsets[(string) $tz] );
                    }
                    elseif ( is_string( $tz ) )
                    {
                        $tz = new DateTimeZone( $tz );
                    }
                }

                // If the date is numeric assume a unix timestamp and convert it.
                date_default_timezone_set( 'UTC' );
                $date = is_numeric( $date ) ? date( 'c', $date ) : $date;

                // fix potentiall bad date data
                if ( strpos( $date, ":" ) > 0 && strpos( $date, "-" ) == false )
                {
                    $date = str_replace( ":", "", $date );
                }
                // Call the DateTime constructor
                parent::__construct( $date, $tz );

                // reset the timezone !!
                date_default_timezone_set( self::$stz->getName() );

                // Set the timezone object for access later.
                $this->_tz = $tz;

            }

            /**
             * Gets the date in a specific format
             *
             * Returns a string formatted according to the given format. Month and weekday names and
             * other language dependent strings respect the current locale
             *
             * @param   string    The date format specification string (see {@link PHP_MANUAL#JevDate::strftime})
             * @param   boolean    True to return the date string in the local time zone, false to return it in GMT.
             *
             * @return    string    The date as a formatted string.
             * @deprecated    Deprecated since 1.6, use Date::format() instead.
             *
             * @since         1.5
             */
            public function toFormat( $format = '%Y-%m-%d %H:%M:%S', $local = false ) {

                // do not reset the timezone !! - this is needed for the weekdays
                // Set time zone to GMT as JevDate::strftime formats according locale setting.
                // date_default_timezone_set('GMT');
                // Generate the timestamp.
                $time = (int) parent::format( 'U', true );
                // this requires php 5.3!
                //$time = $this->getTimeStamp();
                // If the returned time should be local add the GMT offset.
                if ( $local )
                {
                    $time += $this->getOffsetFromGMT();
                }

                // Manually modify the month and day strings in the format.
                if ( strpos( $format, '%a' ) !== false )
                {
                    $format = str_replace( '%a', $this->dayToString( date( 'w', $time ), true ), $format );
                }
                if ( strpos( $format, '%A' ) !== false )
                {
                    $format = str_replace( '%A', $this->dayToString( date( 'w', $time ) ), $format );
                }
                if ( strpos( $format, '%b' ) !== false )
                {
                    $format = str_replace( '%b', $this->monthToString( date( 'n', $time ), true ), $format );
                }
                if ( strpos( $format, '%h' ) !== false )
                {
                    $format = str_replace( '%h', $this->monthToString( date( 'n', $time ), true ), $format );
                }
                if ( strpos( $format, '%B' ) !== false )
                {
                    $format = str_replace( '%B', $this->monthToString( date( 'n', $time ) ), $format );
                }

                // Generate the formatted string.
                $date = JevDate::strftime( $format, $time );

                // reset the timezone !!
                date_default_timezone_set( self::$stz->getName() );

                return $date;

            }

            /**
             * Return the {@link Date} object
             *
             * @param   mixed  $time      The initial time for the Date object
             * @param   mixed  $tzOffset  The timezone offset.
             *
             * @return Date object
             * @since 1.5
             */
            public static function getDate( $time = 'now', $tzOffset = null ) {

                jimport( 'joomla.utilities.date' );
                static $instances;
                static $classname;
                static $mainLocale;

                if ( ! isset( $instances ) )
                {
                    $instances = array();
                }

                $classname = 'JevDate';
                $key       = $time . '-' . $tzOffset;

                //		if (!isset($instances[$classname][$key])) {
                $tmp = new $classname( $time, $tzOffset );

                return $tmp;

            }

            public function toMySQL( $local = false ) {

                return self::toSql( $local );
                //return $this->toFormat('%Y-%m-%d %H:%M:%S', $local);

            }

            // Timezone aware version!!
            public function toSql( $local = false, DatabaseDriver $db = null ) {

                return $this->toFormat( '%Y-%m-%d %H:%M:%S', $local );

            }

            public static function strtotime( $time, $now = null ) {

                static $date;
                if ( ! isset( $date ) )
                {
                    $date = new JevDate();
                }
                // reset the timezone !!
                date_default_timezone_set( $date->mytz->getName() );
                if ( $now != null )
                {
                    $res = strtotime( $time, $now );
                }
                else
                {
                    $res = strtotime( $time );
                }

                return $res;

            }

            public static function mktime() {

                static $date;
                if ( ! isset( $date ) )
                {
                    $date = new JevDate();
                }
                // reset the timezone !!
                date_default_timezone_set( $date->mytz->getName() );
                $arg = func_get_args();

                $name = "mktime";
                if ( is_callable( $name ) && count( $arg ) > 0 )
                {
                    try
                    {
                        return call_user_func_array( $name, $arg );
                    }
                    catch ( Throwable $e )
                    {
                        echo $e->getMessage() . "<Br>";
                        var_dump( $arg );
                        exit( 0 );
                    }
                }
                else
                {
                    return call_user_func_array( "time", $arg );
                }

            }

            public static function strftime( $format, $timestamp = 'time()', $tzid = false ) {

                static $date;
                if ( ! isset( $date ) )
                {
                    $date = new JevDate();
                }
                $oldtz = date_default_timezone_get();
                // reset the timezone !!
                if ( $tzid )
                {
                    date_default_timezone_set( $tzid );
                }
                else
                {
                    date_default_timezone_set( $date->mytz->getName() );
                }

                include_once JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/strftime.php";

                try
                {
                    $return = PHP81_BC\strftime( $format, $timestamp );
                }
                catch ( Throwable $e )
                {
                    $x      = 1;
                    $return = PHP81_BC\strftime( $format, $timestamp );
                }

                date_default_timezone_set( $oldtz );

                return $return;
            }

            public static function rawStrftime( $format, $timestamp = false ) {

                $timestamp = $timestamp ? $timestamp : time();

                include_once JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/strftime.php";

                try
                {
                    $return = PHP81_BC\strftime( $format, $timestamp );
                }
                catch ( Throwable $e )
                {
                    $x      = 1;
                    $return = PHP81_BC\strftime( $format, $timestamp );
                }

                return $return;
            }

            public function __call( $name, $arguments ) {

                static $date;
                if ( ! isset( $date ) )
                {
                    $date = new JevDate();
                }
                // reset the timezone !!
                date_default_timezone_set( $date->mytz->getName() );
                $args = array_unshift( $arguments, $this );

                if ( is_callable( $name ) )
                {
                    return call_user_func_array( $name, $args );
                }

            }

        }
    }
    else
    {
        #[\AllowDynamicProperties]
        class JevDate extends Date {

            public $mytz;

            /**
             * Constructor.
             *
             * @param   string    String in a format accepted by JevDate::strtotime(), defaults to "now".
             * @param   mixed    Time zone to be used for the date.
             *
             * @return    void
             * @throws    JException
             * @since    1.5
             *
             */
            public function __construct( $date = 'now', $tz = null ) {

                // Create the base GMT and server time zone objects.
                if ( empty( self::$gmt ) )
                { //|| empty(self::$stz)) {
                    self::$gmt = new DateTimeZone( 'GMT' );
                    //self::$stz = new DateTimeZone(@date_default_timezone_get());
                }
                // Must get this each time otherwise modules can't set their own timezone
                $compparams = ComponentHelper::getParams( JEV_COM_COMPONENT );
                $jtz        = $compparams->get( "icaltimezonelive", "" );
                if ( $jtz != "" )
                {
                    self::$stz = new DateTimeZone( $jtz );
                }
                else
                {
                    self::$stz = new DateTimeZone( @date_default_timezone_get() );
                }
                $this->mytz = self::$stz;

                // If the time zone object is not set, attempt to build it.
                if ( ! ( $tz instanceof DateTimeZone ) )
                {
                    if ( $tz === null )
                    {
                        $tz = self::$gmt;
                    }
                    elseif ( is_numeric( $tz ) )
                    {
                        if ( ! isset( $this->offsets ) )
                        {
                            $this->offsets = array(
                                '-12'   => 'Etc/GMT-12',
                                '-11'   => 'Pacific/Midway',
                                '-10'   => 'Pacific/Honolulu',
                                '-9.5'  => 'Pacific/Marquesas',
                                '-9'    => 'US/Alaska',
                                '-8'    => 'US/Pacific',
                                '-7'    => 'US/Mountain',
                                '-6'    => 'US/Central',
                                '-5'    => 'US/Eastern',
                                '-4.5'  => 'America/Caracas',
                                '-4'    => 'America/Barbados',
                                '-3.5'  => 'Canada/Newfoundland',
                                '-3'    => 'America/Buenos_Aires',
                                '-2'    => 'Atlantic/South_Georgia',
                                '-1'    => 'Atlantic/Azores',
                                '0'     => 'Europe/London',
                                '1'     => 'Europe/Amsterdam',
                                '2'     => 'Europe/Istanbul',
                                '3'     => 'Asia/Riyadh',
                                '3.5'   => 'Asia/Tehran',
                                '4'     => 'Asia/Muscat',
                                '4.5'   => 'Asia/Kabul',
                                '5'     => 'Asia/Karachi',
                                '5.5'   => 'Asia/Calcutta',
                                '5.75'  => 'Asia/Katmandu',
                                '6'     => 'Asia/Dhaka',
                                '6.5'   => 'Indian/Cocos',
                                '7'     => 'Asia/Bangkok',
                                '8'     => 'Australia/Perth',
                                '8.75'  => 'Australia/West',
                                '9'     => 'Asia/Tokyo',
                                '9.5'   => 'Australia/Adelaide',
                                '10'    => 'Australia/Brisbane',
                                '10.5'  => 'Australia/Lord_Howe',
                                '11'    => 'Pacific/Kosrae',
                                '11.5'  => 'Pacific/Norfolk',
                                '12'    => 'Pacific/Auckland',
                                '12.75' => 'Pacific/Chatham',
                                '13'    => 'Pacific/Tongatapu',
                                '14'    => 'Pacific/Kiritimati'
                            );
                        }
                        // Translate from offset.
                        $tz = new DateTimeZone( $this->offsets[(string) $tz] );
                    }
                    elseif ( is_string( $tz ) )
                    {
                        $tz = new DateTimeZone( $tz );
                    }
                }

                // If the date is numeric assume a unix timestamp and convert it.
                date_default_timezone_set( 'UTC' );
                $date = is_numeric( $date ) ? date( 'c', $date ) : $date;

                // fix potentiall bad date data
                if ( strpos( $date, ":" ) > 0 && strpos( $date, "-" ) == false )
                {
                    $date = str_replace( ":", "", $date );
                }
                // Call the DateTime constructor
                parent::__construct( $date, $tz );

                // reset the timezone !!
                date_default_timezone_set( self::$stz->getName() );

                // Set the timezone object for access later.
                $this->_tz = $tz;

            }

            /**
             * Gets the date in a specific format
             *
             * Returns a string formatted according to the given format. Month and weekday names and
             * other language dependent strings respect the current locale
             *
             * @param   string    The date format specification string (see {@link PHP_MANUAL#JevDate::strftime})
             * @param   boolean    True to return the date string in the local time zone, false to return it in GMT.
             *
             * @return    string    The date as a formatted string.
             * @deprecated    Deprecated since 1.6, use Date::format() instead.
             *
             * @since         1.5
             */
            public function toFormat( $format = '%Y-%m-%d %H:%M:%S', $local = false ) {

                // do not reset the timezone !! - this is needed for the weekdays
                // Set time zone to GMT as JevDate::strftime formats according locale setting.
                // date_default_timezone_set('GMT');
                // Generate the timestamp.
                $time = (int) parent::format( 'U', true );
                // this requires php 5.3!
                //$time = $this->getTimeStamp();
                // If the returned time should be local add the GMT offset.
                if ( $local )
                {
                    $time += $this->getOffsetFromGMT();
                }

                // Manually modify the month and day strings in the format.
                if ( strpos( $format, '%a' ) !== false )
                {
                    $format = str_replace( '%a', $this->dayToString( date( 'w', $time ), true ), $format );
                }
                if ( strpos( $format, '%A' ) !== false )
                {
                    $format = str_replace( '%A', $this->dayToString( date( 'w', $time ) ), $format );
                }
                if ( strpos( $format, '%b' ) !== false )
                {
                    $format = str_replace( '%b', $this->monthToString( date( 'n', $time ), true ), $format );
                }
                if ( strpos( $format, '%h' ) !== false )
                {
                    $format = str_replace( '%h', $this->monthToString( date( 'n', $time ), true ), $format );
                }
                if ( strpos( $format, '%B' ) !== false )
                {
                    $format = str_replace( '%B', $this->monthToString( date( 'n', $time ) ), $format );
                }

                // Generate the formatted string.
                $date = JevDate::strftime( $format, $time );

                // reset the timezone !!
                date_default_timezone_set( self::$stz->getName() );

                return $date;

            }

            /**
             * Return the {@link Date} object
             *
             * @param   mixed  $time      The initial time for the Date object
             * @param   mixed  $tzOffset  The timezone offset.
             *
             * @return Date object
             * @since 1.5
             */
            public static function getDate( $time = 'now', $tzOffset = null ) {

                jimport( 'joomla.utilities.date' );
                static $instances;
                static $classname;
                static $mainLocale;

                if ( ! isset( $instances ) )
                {
                    $instances = array();
                }

                $classname = 'JevDate';
                $key       = $time . '-' . $tzOffset;

                //		if (!isset($instances[$classname][$key])) {
                $tmp = new $classname( $time, $tzOffset );

                return $tmp;

            }

            public function toMySQL( $local = false ) {

                return self::toSql( $local );
                //return $this->toFormat('%Y-%m-%d %H:%M:%S', $local);

            }

            // Timezone aware version!!
            public function toSql( $local = false, DatabaseDriver $db = null ) {

                return $this->toFormat( '%Y-%m-%d %H:%M:%S', $local );

            }

            public static function strtotime( $time, $now = null ) {

                static $date;
                if ( ! isset( $date ) )
                {
                    $date = new JevDate();
                }
                // reset the timezone !!
                date_default_timezone_set( $date->mytz->getName() );
                if ( $now != null )
                {
                    $res = strtotime( $time, $now );
                }
                else
                {
                    $res = strtotime( $time );
                }

                return $res;

            }

            public static function mktime() {

                static $date;
                if ( ! isset( $date ) )
                {
                    $date = new JevDate();
                }
                // reset the timezone !!
                date_default_timezone_set( $date->mytz->getName() );
                $arg = func_get_args();

                $name = "mktime";
                if ( is_callable( $name ) && count( $arg ) > 0 )
                {
                    try
                    {
                        return call_user_func_array( $name, $arg );
                    }
                    catch ( Throwable $e )
                    {
                        echo $e->getMessage() . "<Br>";
                        var_dump( $arg );
                        exit( 0 );
                    }
                }
                else
                {
                    return call_user_func_array( "time", $arg );
                }

            }

            public static function strftime( $format, $timestamp = 'time()', $tzid = false ) {

                static $date;
                if ( ! isset( $date ) )
                {
                    $date = new JevDate();
                }
                $oldtz = date_default_timezone_get();
                // reset the timezone !!
                if ( $tzid )
                {
                    date_default_timezone_set( $tzid );
                }
                else
                {
                    date_default_timezone_set( $date->mytz->getName() );
                }

                include_once JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/strftime.php";

                try
                {
                    $return = PHP81_BC\strftime( $format, $timestamp );
                }
                catch ( Throwable $e )
                {
                    $x      = 1;
                    $return = PHP81_BC\strftime( $format, $timestamp );
                }

                date_default_timezone_set( $oldtz );

                return $return;
            }

            public static function rawStrftime( $format, $timestamp = false ) {

                $timestamp = $timestamp ? $timestamp : time();

                include_once JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/strftime.php";

                try
                {
                    $return = PHP81_BC\strftime( $format, $timestamp );
                }
                catch ( Throwable $e )
                {
                    $x      = 1;
                    $return = PHP81_BC\strftime( $format, $timestamp );
                }

                return $return;
            }

            public function __call( $name, $arguments ) {

                static $date;
                if ( ! isset( $date ) )
                {
                    $date = new JevDate();
                }
                // reset the timezone !!
                date_default_timezone_set( $date->mytz->getName() );
                $args = array_unshift( $arguments, $this );

                if ( is_callable( $name ) )
                {
                    return call_user_func_array( $name, $args );
                }

            }

        }

    }
}