<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: iCalImport.php 3467 2012-04-03 09:36:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;

// This class doesn't yet deal with repeating events

class iCalImport
{
	/**
	 * This array saves the iCalendar parsed data as an array - may make a class later!
	 *
	 * @var array
	 */
	var $cal			= array();

	var $key;
	var $rawData		= '';
	var $srcURL			= '';
	var $eventCount		= -1;
	var $todoCount		= -1;

	var $vevents	= array();

	function __construct()
	{

	}

	// constructor
	function import($filename, $rawtext = "")
	{

        $earliestyear = JEVHelper::getMinYear();
        $earliestyearTime = mktime(0, 0, 1, 12, 31, $earliestyear -1);
        $latestyear   = JEVHelper::getMaxYear();

        @ini_set("max_execution_time", 600);

		echo Text::sprintf("Importing events from ical file %s", $filename) . "<br/>";
		$cfg    = JEVConfig::getInstance();
		$option = JEV_COM_COMPONENT;
		// resultant data goes here
		if ($filename != "")
		{
			$file = $filename;
			if (!@file_exists($file))
			{

				$file = JPATH_SITE . "/components/$option/" . $filename;
			}
			if (!file_exists($file))
			{
				echo "I hope this is a URL!!<br/>";
				$file = $filename;
			}

			// get name
			$isFile = false;
			if (isset($_FILES['upload']) && is_array($_FILES['upload']))
			{
				$uploadfile = $_FILES['upload'];
				// MSIE sets a mime-type of application/octet-stream
				if ($uploadfile['size'] != 0 && ($uploadfile['type'] == "text/calendar" || $uploadfile['type'] == "text/csv" || $uploadfile['type'] == "application/octet-stream" || $uploadfile['type'] == "text/html"))
				{
					$this->srcURL = $uploadfile['name'];
					$isFile       = true;
				}
			}
			if ($this->srcURL == "")
			{
				$this->srcURL = $file;
			}

			// $this->rawData = iconv("ISO-8859-1","UTF-8",file_get_contents($file));

			if (!$isFile && is_callable("curl_exec"))
			{
				$ch = curl_init();

				// Set curl option CURLOPT_HTTPAUTH, if the url includes user name and password.
				// e.g. http://username:password@www.example.com/cal.ics
				$username = parse_url($file, PHP_URL_USER);
				$password = parse_url($file, PHP_URL_PASS);
				if ($username != "" && $password != "")
				{
					curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
				}

				// Set proxy if enabled
				$jConfig =  Factory::getConfig();

				if($jConfig->get('proxy_enable') == 1) {
					curl_setopt($ch, CURLOPT_PROXY, $jConfig->get('proxy_host') . ":" . $jConfig->get('proxy_port'));
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $jConfig->get('proxy_user') . ":" . $jConfig->get('proxy_password'));
				}

				curl_setopt($ch, CURLOPT_URL, $file);
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
				curl_setopt($ch, CURLOPT_POST, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, "Jevents.net");
				curl_setopt($ch, CURLOPT_ENCODING, '');
				$this->rawData = curl_exec($ch);

				$charset = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                //$error = curl_error($ch);
				curl_close($ch);

				// try file_get_contents as a backup
				if ($this->rawData === false || $this->rawData == "")
				{
					$this->rawData = @file_get_contents($file);
				}
			}
			else
			{
				$this->rawData = @file_get_contents($file);
			}

			if ($this->rawData === false)
			{
				// file_get_contents: no or blocked wrapper for $file
				Factory::getApplication()->enqueueMessage('file_get_contents() failed, try fsockopen', 'notice');
				$parsed_url = parse_url($file);
				if ($parsed_url === false)
				{
					Factory::getApplication()->enqueueMessage('url not parsed: ' . $file, 'warning');
				}
				else
				{
					if ($parsed_url['scheme'] == 'http' || $parsed_url['scheme'] == 'https' || $parsed_url['scheme'] == 'webcal')
					{
						// try socked connection
						$fsockhost = $parsed_url['host'];
						$fsockport = 80;
						if ($parsed_url['scheme'] == 'https')
						{
							$fsockhost = 'ssl://' . $fsockhost;
							$fsockport = 443;
						}
						if (array_key_exists('port', $parsed_url)) $fsockport = $parsed_url['port'];

						$fh = @fsockopen($fsockhost, $fsockport, $errno, $errstr, 3);
						if ($fh === false)
						{
							// fsockopen: no connect
							Factory::getApplication()->enqueueMessage('fsockopen: no connect for ' . $file . ' - ' . $errstr, 'warning');

							return false;
						}
						else
						{
							$fsock_path = ((array_key_exists('path', $parsed_url)) ? $parsed_url['path'] : '')
								. ((array_key_exists('query', $parsed_url)) ? '?' . $parsed_url['query'] : '')
								. ((array_key_exists('fragment', $parsed_url)) ? '#' . $parsed_url['fragment'] : '');
							fputs($fh, "GET $fsock_path HTTP/1.0\r\n");
							fputs($fh, "Host: " . $parsed_url['host'] . "\r\n\r\n");
							while (!feof($fh))
							{
								$this->rawData .= fread($fh, 4096);
							}
							fclose($fh);
							$this->rawData = StringHelper::substr($this->rawData, StringHelper::strpos($this->rawData, "\r\n\r\n") + 4);
						}
					}
				}
			}

			// Returns true if $string is valid UTF-8 and false otherwise.
			/*
			$isutf8 = $this->detectUTF8($this->rawData);
			if ($isutf8) {
			$this->rawData = iconv("ISO-8859-1","UTF-8",$this->rawData);
			}
			*/

		}
		else
		{
			$this->srcURL  = "n/a";
			$this->rawData = $rawtext;
		};
		// get rid of spurious carriage returns and spaces
		//$this->rawData = preg_replace("/[\r\n]+ ([:;])/","$1",$this->rawData);

		// simplify line feed
		$this->rawData = str_replace("\r\n", "\n", trim($this->rawData));

		// remove spurious lines before calendar start
		if (!StringHelper::stristr($this->rawData, 'BEGIN:VCALENDAR'))
		{

			Factory::getApplication()->triggerEvent('onImportCsvFile', array(& $this->rawData));

			// check for CSV format
			$firstLine = StringHelper::substr($this->rawData, 0, StringHelper::strpos($this->rawData, "\n") + 1);

			if (StringHelper::stristr($firstLine, 'SUMMARY') && StringHelper::stristr($firstLine, 'DTSTART')
				&& StringHelper::stristr($firstLine, 'DTEND') && StringHelper::stristr($firstLine, 'CATEGORIES')
				&& StringHelper::stristr($firstLine, 'TIMEZONE'))
			{
				$timezone      = date_default_timezone_get();
				$csvTrans      = new CsvToiCal($file, ",", $this->rawData);
				$this->rawData = $csvTrans->getRawData();
				date_default_timezone_set($timezone);
			}
			else
			{
				//echo "first line = $firstLine<br/>";
				//echo "raw imported data = ".$this->rawData."<br/>";
				//exit();
				if (Factory::getUser()->get('isRoot') && Factory::getApplication()->isClient('administrator'))
				{
					$config = Factory::getConfig();
					$debug  = (boolean) $config->get('debug');
					if ($debug)
					{
						echo "Unable to fetch calendar data<br/>";
						echo "Raw Data is " . $this->rawData;
						exit();
					}
				}
				Factory::getApplication()->enqueueMessage('Not a valid VCALENDAR data file: ' . $this->srcURL, 'warning');
				// return false so that we don't remove a valid calendar because of a bad URL load!
				return false;
			}
		}
		$begin         = StringHelper::strpos($this->rawData, "BEGIN:VCALENDAR", 0);
		$this->rawData = StringHelper::substr($this->rawData, $begin);
		//		$this->rawData = preg_replace('/^.*\n(BEGIN:VCALENDAR)/s', '$1', $this->rawData, 1);

		// unfold content lines according the unfolding procedure of rfc2445
		$this->rawData = str_replace("\n ", "", $this->rawData);
		$this->rawData = str_replace("\n\t", "", $this->rawData);

		// TODO make sure I can always ignore the second line
		// Some google calendars has spaces and carriage returns in their UIDs

		// Convert string into array for easier processing
		$this->rawData = explode("\n", $this->rawData);

		$skipuntil = null;
		foreach ($this->rawData as $vcLine)
		{
			//$vcLine = trim($vcLine); // trim one line
			if (empty($vcLine))
			{
				continue;
			}
			if (!empty($vcLine))
			{
				// skip unhandled block
				if ($skipuntil)
				{
					if (trim($vcLine) == $skipuntil)
					{
						// found end of block to skip
						$skipuntil = null;
					}
					continue;
				}
				$matches = explode(":", $vcLine, 3);
				// Catch some bad Microsoft timezones e.g. "(UTC+01:00) Amsterdam, Berlin, Bern, Rom, Stockholm, Wien"
				if (count($matches) == 3)
				{
					if (strpos($matches[0], ";TZID") > 0)
					{
						$matches[0] = $matches[0] . ":" . $matches[1];
						$matches[1] = $matches[2];
						unset($matches[2]);
					}
					else
					{
						$matches = explode(":", $vcLine, 2);
					}
				}
				if (count($matches) == 2)
				{
					list($this->key, $value) = $matches;
					//$value = str_replace('\n', "\n", $value);
					//$value = stripslashes($value);
					$append = false;

					// Treat Accordingly
					switch ($vcLine)
					{
						case "BEGIN:VTODO":
							// start of VTODO section
							$this->todoCount++;
							$parent = "VTODO";
							break;

						case "BEGIN:VEVENT":
							// start of VEVENT section
							$this->eventCount++;
							$parent = "VEVENT";
							break;

						case "BEGIN:VCALENDAR":
						case "BEGIN:DAYLIGHT":
						case "BEGIN:VTIMEZONE":
						case "BEGIN:STANDARD":
							$parent = $value; // save tu array under value key
							break;

						case "END:VTODO":
						case "END:VEVENT":

						case "END:VCALENDAR":
						case "END:DAYLIGHT":
						case "END:VTIMEZONE":
						case "END:STANDARD":
							$parent = "VCALENDAR";
							break;

                        default:
							// skip unknown BEGIN/END blocks e.g. BEGIN:VALARM
							if ($this->key == 'BEGIN')
							{
								$skipuntil = 'END:' . $value;
								break;
							}

							// Generic processing
							$this->add_to_cal($parent, $this->key, $value, $append);
							break;
					}
				}
				else
				{
					// ignore these lines go
				}
			}
		}
		// Sort the events into start date order
		// there's little point in doing this id an RRULE is present!
		//	usort($this->cal['VEVENT'], array("iCalImport","comparedates"));

		// Populate vevent class - should do this first trawl through !!
		if (array_key_exists("VEVENT", $this->cal))
		{
			foreach ($this->cal["VEVENT"] as $vevent)
			{
				// trap for badly constructed all day events
				if (isset($vevent["DTSTARTRAW"]) && isset($vevent["DTENDRAW"]) && $vevent["DTENDRAW"] == $vevent["DTSTARTRAW"])
				{
					//$vevent["DTEND"] += 86400;
					$vevent["NOENDTIME"] = 1;
				}
				// some imports do not have UID set
				if (!isset($vevent["UID"]))
				{
					$vevent["UID"] = md5(uniqid(rand(), true));
				}
                /*
                if (isset($vevent["DTEND"]) && $vevent["DTEND"] < $earliestyearTime)
                {
                    continue;
                }
                */
				$this->vevents[] = iCalEvent::iCalEventFromData($vevent);
			}
		}

		return $this;
	}

	function add_to_cal($parent, $key, $value, $append)
	{
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		// I'm not interested in when the events were created/modified
		if (($key == "DTSTAMP") or ($key == "LAST-MODIFIED") or ($key == "CREATED")) return;

		if ($key == "RRULE" && $value != "")
		{
			$value = $this->parseRRULE($value, $parent);
		}

		$rawkey = "";
        $rawtarget = "";
		if (StringHelper::stristr($key, "DTSTART") || StringHelper::stristr($key, "DTEND") || StringHelper::stristr($key, "EXDATE"))
		{
			list($key, $value, $rawkey, $rawvalue) = $this->handleDate($key, $value);

			// if midnight then move back one day (ISO 8601 uses seconds past midnight http://www.iso.org/iso/date_and_time_format)
			// because of the odd way we record midnights
			if (StringHelper::stristr($key, "DTEND") == "DTEND" && StringHelper::strlen($rawvalue) >= 15 && StringHelper::substr($rawvalue, 9, 6) == "000000")
			{
				$value -= 1;  // 1 second
				//$value -= 86400;  // 1 day
			}
			if (StringHelper::stristr($key, "DTEND") == "DTEND" && StringHelper::strlen($rawvalue) == 8)
			{
				// all day event detected YYYYMMDD, set DTEND to last second of previous day
				/* see section 3.6.1 of RFC https://tools.ietf.org/html/rfc5545
				 *
      The following is an example of the "VEVENT" calendar component
      used to represent a multi-day event scheduled from June 28th, 2007
      to July 8th, 2007 inclusively.  Note that the "DTEND" property is
      set to July 9th, 2007, since the "DTEND" property specifies the
      non-inclusive end of the event.

       BEGIN:VEVENT
       UID:20070423T123432Z-541111@example.com
       DTSTAMP:20070423T123432Z
       DTSTART;VALUE=DATE:20070628
       DTEND;VALUE=DATE:20070709
       SUMMARY:Festival International de Jazz de Montreal
       TRANSP:TRANSPARENT
       END:VEVENT
				 */
				$value -= 1;  // 1 second
			}
		}
		if (StringHelper::stristr($key, "DURATION"))
		{
			list($key, $value, $rawkey, $rawvalue) = $this->handleDuration($key, $value);
		}

		switch ($parent)
		{
			case "VTODO":
				$this->cal[$parent][$this->todoCount][$key] = $value;
				break;

			case "VEVENT":
				// strip off unnecessary quoted printable encoding message
				$parts = explode(';', $key);
				if (count($parts) > 1)
				{
					$key = $parts[0];
					for ($i = 1; $i < count($parts); $i++)
					{
						if ($parts[$i] == "ENCODING=QUOTED-PRINTABLE")
						{
							//$value=str_replace("=0D=0A","<br/>",$value);
							if (strpos($value, "=bd") > 0)
							{
								// quoted_printable_decode incorrectly decodes LOWER case encoding e.g. =db instead of =DB thereby messing up URLs in the text
								$value = preg_replace("/=([a-z]+)/", "=::$1", $value);
								$value = quoted_printable_decode($value);
								$value = preg_replace("/=::([a-z]+)/", "=$1", $value);
							}
							else
							{
								$value = quoted_printable_decode($value);
							}
						}
						// drop other bits like language etc.
					}
				}

				// Special treatment of
				if($key=="ATTACH") {
                    if (count($parts) == 2 && stripos($parts[1], "FMTTYPE=image") === 0)
                    {

                    }
					$this->cal[$parent][$this->eventCount][$key][] = $value;
				}
				elseif (StringHelper::strpos($key, "EXDATE") === false)
				{
					$target =& $this->cal[$parent][$this->eventCount][$key];
					$rawtarget =& $this->cal[$parent][$this->eventCount][$rawkey];
				}
				else
				{
                    if (!isset($this->cal[$parent][$this->eventCount]))
                    {
                        $this->cal[$parent][$this->eventCount] = array();
                    }
					if (!array_key_exists("EXDATE", $this->cal[$parent][$this->eventCount]))
					{
						$this->cal[$parent][$this->eventCount]["EXDATE"]    = array();
						$this->cal[$parent][$this->eventCount]["RAWEXDATE"] = array();
					}
					if (is_array($value))
					{
						$this->cal[$parent][$this->eventCount]["EXDATE"]                                                                = array_merge($this->cal[$parent][$this->eventCount]["EXDATE"], $value);
						$this->cal[$parent][$this->eventCount]["RAWEXDATE"][count($this->cal[$parent][$this->eventCount]["RAWEXDATE"])] = $rawvalue;
						break;
					}
					else
					{
						$target    =& $this->cal[$parent][$this->eventCount]["EXDATE"][count($this->cal[$parent][$this->eventCount]["EXDATE"])];
						$rawtarget =& $this->cal[$parent][$this->eventCount]["RAWEXDATE"][count($this->cal[$parent][$this->eventCount]["RAWEXDATE"])];
					}
				}

				// Remove escaping of text
				$value = str_replace('\,', ',', $value);
				$value = str_replace('\;', ';', $value);

				// convert URLs to links but NOT in uid field!!
				//$value = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\">\\0</a>", $value);
				//$value = preg_replace('@(?<![">])\b(?:(?:https?|ftp)://|www\.|ftp\.)[-A-Z0-9+&@#/%=~_|$?!:,.]*[A-Z0-9+&@#/%=~_|$]@',"<a href=\"\\0\">\\0</a>", $value);
				// See http://stackoverflow.com/questions/8414675/preg-replace-for-url-and-download-links and http://regexr.com/3bup3 to test this
				//$value = preg_replace('@(https?://([\w-.]+)+(:\d+)?(/([\w/_\.%\-+~=]*(\?\S+)?)?)?)@u', '<a href="$1">$1</a>', $value);
				// See https://gist.github.com/winzig/8894715
				//$value = preg_replace('@(https?:\/{1,3})?((?:(?:[\w.\-]+\.(?:[a-z]{2,13})|(?<=http:\/\/|https:\/\/)[\w.\-]+)\/)(?:[^\s()<>{}\[\]]+|\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\))+(?:\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’])|(?:(?<!@)(?:\w+(?:[.\-]+\w+)*\.(?:[a-z]{2,13})|(?:(?:[0-9](?!\d)|[1-9][0-9](?!\d)|1[0-9]{2}(?!\d)|2[0-4][0-9](?!\d)|25[0-5](?!\d))[.]?){4})\b\/?(?!@)(?:[^\s()<>{}\[\]]+|\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\))*(?:\([^\s()]*?\([^\s()]+\)[^\s()]*?\)|\([^\s]+?\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’])?))@gi', '<a href="$1$2">$1</a>', $value);

				if  ($params->get("converturlstolinksonimport", 1) && is_string($value) && $key!="UID" && $key!="X-EXTRAINFO" && $key!="ATTACH" && $key!="X-ALT-DESC"){
					if (StringHelper::strpos(str_replace(" ","",StringHelper::strtolower($value)),"<ahref=")===false && StringHelper::strpos(str_replace(" ","",StringHelper::strtolower($value)),"<img")===false && (StringHelper::strpos(StringHelper::strtolower($value),"http://")!==false || StringHelper::strpos(StringHelper::strtolower($value),"https://")!==false)){
                            // See http://stackoverflow.com/questions/8414675/preg-replace-for-url-and-download-links and http://regexr.com/3bup3 to test this
                            $value = preg_replace('@(https?://([\w\-.]+)+(:\d+)?(/([\w/_\.%\-+~=]*(\?\S+)?)?)?)@u', '<a href="$1">$1</a>', $value);
					}
				}

				// Fix some stupid Microsoft IIS driven calendars which don't encode the data properly!
				// see section 2 of http://www.the-art-of-web.com/html/character-codes/
				if ($key == "DESCRIPTION" || $key == "SUMMARY")
				{
					$len  = strlen($value);
					$ulen = StringHelper::strlen($value);
					// Can cause problems with multibyte strings so skip this
					// we need to check this since some UTF-8 characters from Google get truncates otherwise
					if ($len == $ulen)
					{
						$value = str_replace(array("\205", "\221", "\222", "\223", "\224", "\225", "\226", "\227", "\240"), array("...", "'", "'", '"', '"', "*", "-", "--", " "), $value);
					}
				}

                // use x-alt-desc if present!
                if ($key == "X-ALT-DESC" && isset($parts[1]) && $parts[1] == "FMTTYPE=text/html")
                {
                    if (strpos($value, "<body>") !== false)
                    {
                        $dom = new DOMDocument();
                        try
                        {
                            if ( @$dom->loadHTML( $value ) )
                            {
                                $body       = $dom->getElementsByTagName( 'body' )->item( 0 );
                                $cellString = '';
                                $children   = $body->childNodes;
                                foreach ( $children as $child )
                                {
                                    $cellString .= $child->ownerDocument->saveHTML( $child );
                                }

                                $value = str_replace('\n', '', $cellString);
                            }
                        }
                        catch ( \Throwable $e )
                        {

                        }
                    }

                }

				// Strip http:// from UID !!!
				if ($key == "UID" && StringHelper::strpos($value, "http://") !== false)
				{
					$value = str_replace('http://', "http", $value);
				}

				if ($key == "RECURRENCE-ID")
				{
					if (count($parts) > 1)
					{
						for ($i = 1; $i < count($parts); $i++)
						{
							if (StringHelper::stristr($parts[$i], "TZID"))
							{
								$value = $parts[$i] . ";" . $value;
							}
						}
					}
				}

				// THIS IS NEEDED BECAUSE OF DODGY carriage returns in google calendar UID
				// TODO check its enough
				if ($append)
				{
					$target .= $value;
				}
				else
				{
					$target = $value;
				}
				if ($rawkey != "")
				{
					$rawtarget = $rawvalue;
				}
				break;

			default:
				$this->cal[$parent][$key] = $value;
				break;
		}
	}

	function parseRRULE($value, $parent)
	{

		$result = array();
		$parts  = explode(';', $value);
		foreach ($parts as $part)
		{
			if (StringHelper::strlen($part) == 0) continue;
			$portion = explode('=', $part);
			if (StringHelper::stristr($portion[0], "UNTIL"))
			{
				$untilArray             = $this->handleDate($portion[0], $portion[1]);
				$result[$untilArray[0]] = $untilArray[1];
				$result[$untilArray[2]] = $untilArray[3];
			}
			else $result[$portion[0]] = $portion[1];

		}

		return $result;
	}

	function handleDate($key, $value)
	{

		$rawvalue = $value;
		// we have an array of exdates
		if (StringHelper::strpos($key, "EXDATE") === 0 && StringHelper::strpos($value, ",") > 0)
		{
			$parts = explode(",", $value);
			$value = array();
			foreach ($parts as $val)
			{
				$value[] = $this->unixTime($val);
			}
		}
		else
		{
			$tz = false;
			if (StringHelper::strpos($key, "TZID=") > 0)
			{
				$parts = explode(";", $key);
				if (count($parts) >= 2 && StringHelper::strpos($parts[1], "TZID=") !== false)
				{
					$tz = str_replace("TZID=", "", $parts[1]);
					$tz = iCalImport::convertWindowsTzid($tz);
				}
			}
			$value = $this->unixTime($value, $tz);
		}
		$parts = explode(";", $key);

		if (count($parts) < 2 || StringHelper::strlen($parts[1]) == 0)
		{
			$rawkey = $key . "RAW";

			return array($key, $value, $rawkey, $rawvalue);
		}
		$key    = $parts[0];
		$rawkey = $key . "RAW";

		return array($key, $value, $rawkey, $rawvalue);
	}

	// function to convert windows timezone IDs into Olsen equivalent

	/**
	 * iCal spec represents date in ISO 8601 format followed by "T" then the time
	 * a "Z at the end means the time is UTC and not local time zone
	 *
	 * TODO make sure if time is UTC we take account of system time offset properly
	 *
	 */
	function unixTime($ical_date, $tz = false)
	{

		jimport("joomla.utilities.date");

		static $offset = null;
		if (is_null($offset))
		{
			$config = Factory::getConfig();
			$offset = $config->get('config.offset', 0);

		}
		if (!is_numeric($ical_date))
		{
			$t = JevDate::strtotime($ical_date);

			if (StringHelper::strpos($ical_date, "Z") > 0)
			{
				if (is_callable("date_default_timezone_set"))
				{
					$timezone = date_default_timezone_get();
					// See http://www.php.net/manual/en/timezones.php
					$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
					// server offset tiemzone
					if ($params->get("icaltimezone", "") != "")
					{
						date_default_timezone_set($params->get("icaltimezone", ""));
					}

					// server offset PARAMS
					$serveroffset1 = (JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%S', $t)) - JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%SZ', $t))) / 3600;

					// server offset SERVER
					date_default_timezone_set($timezone);
					$serveroffset2 = (JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%S', $t)) - JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%SZ', $t))) / 3600;
					$t             = new JevDate($ical_date, ($serveroffset1 - $serveroffset2));

					//$t = new JevDate($ical_date );

					date_default_timezone_set($timezone);

					echo "icaldate = " . $ical_date . " imported date=" . $t->toMySQL() . "<br/>";
				}
				else
				{
					// Summer Time adjustment
					list($y, $m, $d, $h, $min, $s) = explode(":", JevDate::strftime('%Y:%m:%d:%H:%M:%S', $t));
					$dst = (JevDate::mktime($h, $min, $s, $m, $d, $y, 0) - JevDate::mktime($h, $min, $s, $m, $d, $y, -1)) / 3600;
					// server offset including DST
					$serveroffset = (JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%S', $t)) - JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%SZ', $t))) / 3600;
					$serveroffset += $dst;

					$t = new JevDate($ical_date, -($serveroffset + $offset));
				}
				/*
				echo "<h3>SET TIMEZONE</h3>";
				$timezone= date_default_timezone_get();
				date_default_timezone_set('America/New_York');

				$tempIcal  = "20091020T163000Z";
				echo $tempIcal."<br/>";
				$temp = JevDate::strtotime($tempIcal);
				list($y,$m,$d,$h,$min,$s) = explode(":", JevDate::strftime('%Y:%m:%d:%H:%M:%S',$temp));
				echo "$y,$m,$d,$h,$min,$s<br/>";
				$dst = (JevDate::mktime($h,$min,$s,$m,$d,$y,0)-JevDate::mktime($h,$min,$s,$m,$d,$y,-1))/3600;
				$so = (JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%S',$temp))-JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%SZ',$temp)))/3600;
				echo " dst=".$dst." serverforoffset=".$so."<br/>";
				$so += $dst;
				$t = new JevDate($tempIcal);
				echo $t->toMySQL()."<br><br/>";


				$tempIcal  = "20091029T163000Z";
				echo $tempIcal."<br/>";
				$temp = JevDate::strtotime($tempIcal);
				list($y,$m,$d,$h,$min,$s) = explode(":", JevDate::strftime('%Y:%m:%d:%H:%M:%S',$temp));
				echo "$y,$m,$d,$h,$min,$s<br/>";
				$dst = (JevDate::mktime($h,$min,$s,$m,$d,$y,0)-JevDate::mktime($h,$min,$s,$m,$d,$y,-1))/3600;
				$so = (JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%S',$temp))-JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%SZ',$temp)))/3600;
				echo " dst=".$dst." serverforoffset=".$so."<br/>";
				$so += $dst;
				$t = new JevDate($tempIcal );
				echo $t->toMySQL()."<br><br/>";

				$tempIcal  = "20091103T163000Z";
				echo $tempIcal."<br/>";
				$temp = JevDate::strtotime($tempIcal);
				list($y,$m,$d,$h,$min,$s) = explode(":", JevDate::strftime('%Y:%m:%d:%H:%M:%S',$temp));
				echo "$y,$m,$d,$h,$min,$s<br/>";
				$dst = (JevDate::mktime($h,$min,$s,$m,$d,$y,0)-JevDate::mktime($h,$min,$s,$m,$d,$y,-1))/3600;
				$so = (JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%S',$temp))-JevDate::strtotime(JevDate::strftime('%Y%m%dT%H%M%SZ',$temp)))/3600;
				echo " dst=".$dst." serverforoffset=".$so."<br/>";
				$so += $dst;
				$t = new JevDate($tempIcal);
				echo $t->toMySQL()."<br>";
				*/

			}
			else if ($tz != false && $tz != "")
			{
				// really should use the timezone of the inputted date
                try
                {
                    $tz = new DateTimeZone( $tz );
                }
                catch (Throwable $exception)
                {
                    Factory::getApplication()->enqueueMessage('Invalid Timezone : ' . $tz, 'warning');
                    $tz = new DateTimeZone('UTC');
                }
				$t  = new JevDate($ical_date, $tz);
				echo "icaldate = " . $ical_date . " imported date=" . $t->toMySQL() . "<br/>";

			}
			else
			{
				$compparams = ComponentHelper::getParams(JEV_COM_COMPONENT);
				$jtz        = $compparams->get("icaltimezonelive", "");
				if ($jtz)
				{
					$t = new JevDate($ical_date, $jtz);
				}
				else
				{
					$t = new JevDate($ical_date);
				}
			}
			//$result = $t->toMySQL();
			$result = $t->toUnix();

			return $result;
		}

		$isUTC = false;
		if (StringHelper::strpos($ical_date, "Z") !== false)
		{
			$isUTC = true;
		}
		// strip "T" and "Z" from the string
		$ical_date = str_replace('T', '', $ical_date);
		$ical_date = str_replace('Z', '', $ical_date);

		// split it out intyo YYYY MM DD HH MM SS
		preg_match("#([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})#", $ical_date, $date);
		list($temp, $y, $m, $d, $h, $min, $s) = $date;
		if (!$min) $min = 0;
		if (!$h) $h = 0;
		if (!$d) $d = 0;
		if (!$s) $s = 0;

		// Trap unix dated beofre 1970
		$y = max($y, 1970);
		if ($isUTC)
		{
			$t      = gmJevDate::mktime($h, $min, $s, $m, $d, $y) + 3600 * $offset;
			$result = JevDate::strtotime(gmdate('Y-m-d H:i:s', $t));
		}
		else
		{
			$result = JevDate::mktime($h, $min, $s, $m, $d, $y);
		}

		// double check!!
		//list($y1,$m1,$d1,$h1,$min1,$s1)=explode(":",JevDate::strftime('%Y:%m:%d:%H:%M:%S',$result));
		return $result;
	}

	public static function convertWindowsTzid($wtzid)
	{

		$wtzdata                                                      = array();
		$wtzdata["Midway Island, Samoa"]                              = "Pacific/Midway";
		$wtzdata["Hawaii-Aleutian"]                                   = "America/Adak";
		$wtzdata["Hawaii"]                                            = "Etc/GMT+10";
		$wtzdata["Marquesas Islands"]                                 = "Pacific/Marquesas";
		$wtzdata["Gambier Islands"]                                   = "Pacific/Gambier";
		$wtzdata["Alaska"]                                            = "America/Anchorage";
		$wtzdata["Tijuana, Baja California"]                          = "America/Ensenada";
		$wtzdata["Pitcairn Islands"]                                  = "Etc/GMT+8";
		$wtzdata["Pacific Time (US & Canada)"]                        = "America/Los_Angeles";
		$wtzdata["Mountain Time (US & Canada)"]                       = "America/Denver";
		$wtzdata["Chihuahua, La Paz, Mazatlan"]                       = "America/Chihuahua";
		$wtzdata["Arizona"]                                           = "America/Dawson_Creek";
		$wtzdata["Saskatchewan, Central America"]                     = "America/Belize";
		$wtzdata["Guadalajara, Mexico City, Monterrey"]               = "America/Cancun";
		$wtzdata["Easter Island"]                                     = "Chile/EasterIsland";
		$wtzdata["Central Time (US & Canada)"]                        = "America/Chicago";
		$wtzdata["Eastern Time (US & Canada)"]                        = "America/New_York";
		$wtzdata["Cuba"]                                              = "America/Havana";
		$wtzdata["Bogota, Lima, Quito, Rio Branco"]                   = "America/Bogota";
		$wtzdata["Caracas"]                                           = "America/Caracas";
		$wtzdata["Santiago"]                                          = "America/Santiago";
		$wtzdata["La Paz"]                                            = "America/La_Paz";
		$wtzdata["Faukland Islands"]                                  = "Atlantic/Stanley";
		$wtzdata["Brazil"]                                            = "America/Campo_Grande";
		$wtzdata["Atlantic Time (Goose Bay)"]                         = "America/Goose_Bay";
		$wtzdata["Atlantic Time (Canada)"]                            = "America/Glace_Bay";
		$wtzdata["Newfoundland"]                                      = "America/St_Johns";
		$wtzdata["UTC-3"]                                             = "America/Araguaina";
		$wtzdata["Montevideo"]                                        = "America/Montevideo";
		$wtzdata["Miquelon, St. Pierre"]                              = "America/Miquelon";
		$wtzdata["Greenland"]                                         = "America/Godthab";
		$wtzdata["Buenos Aires"]                                      = "America/Argentina/Buenos_Aires";
		$wtzdata["Brasilia"]                                          = "America/Sao_Paulo";
		$wtzdata["Mid-Atlantic"]                                      = "America/Noronha";
		$wtzdata["Cape Verde Is."]                                    = "Atlantic/Cape_Verde";
		$wtzdata["Azores"]                                            = "Atlantic/Azores";
		$wtzdata["Greenwich Mean Time : Belfast"]                     = "Europe/Belfast";
		$wtzdata["Greenwich Mean Time : Dublin"]                      = "Europe/Dublin";
		$wtzdata["Greenwich Mean Time : Lisbon"]                      = "Europe/Lisbon";
		$wtzdata["Greenwich Mean Time : London"]                      = "Europe/London";
		$wtzdata["Monrovia, Reykjavik"]                               = "Africa/Abidjan";
		$wtzdata["Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna"]  = "Europe/Amsterdam";
		$wtzdata["Belgrade, Bratislava, Budapest, Ljubljana, Prague"] = "Europe/Belgrade";
		$wtzdata["Brussels, Copenhagen, Madrid, Paris"]               = "Europe/Brussels";
		$wtzdata["West Central Africa"]                               = "Africa/Algiers";
		$wtzdata["Windhoek"]                                          = "Africa/Windhoek";
		$wtzdata["Beirut"]                                            = "Asia/Beirut";
		$wtzdata["Cairo"]                                             = "Africa/Cairo";
		$wtzdata["Gaza"]                                              = "Asia/Gaza";
		$wtzdata["Harare, Pretoria"]                                  = "Africa/Blantyre";
		$wtzdata["Jerusalem"]                                         = "Asia/Jerusalem";
		$wtzdata["Minsk"]                                             = "Europe/Minsk";
		$wtzdata["Syria"]                                             = "Asia/Damascus";
		$wtzdata["Moscow, St. Petersburg, Volgograd"]                 = "Europe/Moscow";
		$wtzdata["Nairobi"]                                           = "Africa/Addis_Ababa";
		$wtzdata["Tehran"]                                            = "Asia/Tehran";
		$wtzdata["Abu Dhabi, Muscat"]                                 = "Asia/Dubai";
		$wtzdata["Yerevan"]                                           = "Asia/Yerevan";
		$wtzdata["Kabul"]                                             = "Asia/Kabul";
		$wtzdata["Ekaterinburg"]                                      = "Asia/Yekaterinburg";
		$wtzdata["Tashkent"]                                          = "Asia/Tashkent";
		$wtzdata["Chennai, Kolkata, Mumbai, New Delhi"]               = "Asia/Kolkata";
		$wtzdata["Kathmandu"]                                         = "Asia/Katmandu";
		$wtzdata["Astana, Dhaka"]                                     = "Asia/Dhaka";
		$wtzdata["Novosibirsk"]                                       = "Asia/Novosibirsk";
		$wtzdata["Yangon (Rangoon)"]                                  = "Asia/Rangoon";
		$wtzdata["Bangkok, Hanoi, Jakarta"]                           = "Asia/Bangkok";
		$wtzdata["Krasnoyarsk"]                                       = "Asia/Krasnoyarsk";
		$wtzdata["Beijing, Chongqing, Hong Kong, Urumqi"]             = "Asia/Hong_Kong";
		$wtzdata["Irkutsk, Ulaan Bataar"]                             = "Asia/Irkutsk";
		$wtzdata["Perth"]                                             = "Australia/Perth";
		$wtzdata["Eucla"]                                             = "Australia/Eucla";
		$wtzdata["Osaka, Sapporo, Tokyo"]                             = "Asia/Tokyo";
		$wtzdata["Seoul"]                                             = "Asia/Seoul";
		$wtzdata["Yakutsk"]                                           = "Asia/Yakutsk";
		$wtzdata["Adelaide"]                                          = "Australia/Adelaide";
		$wtzdata["Darwin"]                                            = "Australia/Darwin";
		$wtzdata["Brisbane"]                                          = "Australia/Brisbane";
		$wtzdata["Hobart"]                                            = "Australia/Hobart";
		$wtzdata["Vladivostok"]                                       = "Asia/Vladivostok";
		$wtzdata["Lord Howe Island"]                                  = "Australia/Lord_Howe";
		$wtzdata["Solomon Is., New Caledonia"]                        = "Etc/GMT-11";
		$wtzdata["Magadan"]                                           = "Asia/Magadan";
		$wtzdata["Norfolk Island"]                                    = "Pacific/Norfolk";
		$wtzdata["Anadyr, Kamchatka"]                                 = "Asia/Anadyr";
		$wtzdata["Auckland, Wellington"]                              = "Pacific/Auckland";
		$wtzdata["Fiji, Kamchatka, Marshall Is."]                     = "Etc/GMT-12";
		$wtzdata["Chatham Islands"]                                   = "Pacific/Chatham";
		$wtzdata["Nuku'alofa"]                                        = "Pacific/Tongatapu";
		$wtzdata["Kiritimati"]                                        = "Pacific/Kiritimati";
		$wtzdata["Central Standard Time"]                             = "America/Chicago";

		// manual entries
		$wtzdata["GMT -0500 (Standard) / GMT -0400 (Daylight)"] = "America/New_York";
		$wtzdata["Eastern Standard Time"]                       = "America/New_York";
		$wtzdata["W. Europe Standard Time"]                     = "Europe/Paris";
		$wtzdata["E. Europe Standard Time"]                     = "Europe/Helsinki";
		$wtzdata["FLE Standard Time"]                           = "Europe/Helsinki";
		$wtzdata["Mountain Standard Time"]                      = "America/Denver";
		$wtzdata["Romance Standard Time"]                       = "Europe/Brussels";
		$wtzdata["GMT Standard Time"]                           = "UTC";
		$wtzdata["Tasmania Standard Time"]                      = "Australia/Hobart";

		// Lets check if a file for custom timezones exists
		if (File::exists(JPATH_COMPONENT_SITE . '/libraries/ical_custom_timezones.php'))
		{
			//Load the custom file once
			include('ical_custom_timezones.php');
		}

		$wtzid = str_replace('"', '', $wtzid);

		return array_key_exists($wtzid, $wtzdata) ? $wtzdata[$wtzid] : $wtzid;
	}

	function handleDuration($key, $value)
	{

		$rawvalue = $value;
		// strip "P" from the string
		$value = str_replace('P', '', $value);
		// split it out intyo W D H M S
		preg_match("/([0-9]*W)*([0-9]*D)*T?([0-9]*H)*([0-9]*M)*([0-9]*S)*/", $value, $details);
        if (count($details) < 6)
        {
            $details = array_pad($details, 6, '');
        }
        @list($temp, $w, $d, $h, $min, $s) = $details;

		$duration   = 0;
		$multiplier = 1;
		$duration   += intval(str_replace('S', '', $s)) * $multiplier;
		$multiplier = 60;
		$duration   += intval(str_replace('M', '', $min)) * $multiplier;
		$multiplier = 3600;
		$duration   += intval(str_replace('H', '', $h)) * $multiplier;
		$multiplier = 86400;
		$duration   += intval(str_replace('D', '', $d)) * $multiplier;
		$multiplier = 604800;
		$duration   += intval(str_replace('W', '', $w)) * $multiplier;

		$rawkey = $key . "RAW";

		return array($key, $duration, $rawkey, $rawvalue);
	}

	/**
	 * Compare two unix timestamp
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return integer
	 */
	function comparedates($a, $b)
	{

		if (!array_key_exists('DTSTART', $a) || !array_key_exists('DTSTART', $b))
		{
			echo "help<br/>";
		}
		if ($a['DTSTART'] == $b['DTSTART']) return 0;

		return ($a['DTSTART'] > $b['DTSTART']) ? +1 : -1;
	}


	// from http://fr3.php.net/manual/en/function.mb-detect-encoding.php#50087
	function is_utf8($string)
	{

		// From http://w3.org/International/questions/qa-forms-utf-8.html
		$result = preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
   )*$%xs', $string);

		return $result;

	} // function is_utf8

	// from http://fr3.php.net/manual/en/function.mb-detect-encoding.php#68607
	function detectUTF8($string)
	{

		return preg_match('%(?:
       [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
       |\xE0[\xA0-\xBF][\x80-\xBF]              # excluding overlongs
       |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
       |\xED[\x80-\x9F][\x80-\xBF]              # excluding surrogates
       |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
       |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
       )+%xs', $string);
	}


}
