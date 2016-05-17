<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: csvToiCal.php 3285 2012-02-21 14:56:25Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

include_once("csvLine.php");

if (!function_exists('str_getcsv'))
{

	function str_getcsv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null)
	{
		$temp = fopen("php://memory", "rw");
		fwrite($temp, $input);
		fseek($temp, 0);
		$r = fgetcsv($temp, 4096, $delimiter, $enclosure);
		fclose($temp);
		return $r;

	}

}

/**
 * Class used for CSV transformation to iCal format
 */
class CsvToiCal
{

	var $rawText;
	var $file;
	var $columnSeparator;
	var $colsOrder = array();
	var $colsNum = 0; // to check if every line has right number of columns
	var $tmpFileName;
	var $tmpfile;
	var $timezone = "UTC"; // default timezone
	var $data;

	/**
	 * default constructor
	 *
	 * @param file filename to process
	 * @param columnSeparator separator of columns in CSV file - default ,
	 */

	public function csvToiCal($file, $columnSeparator = ",", $data = false)
	{
		$this->file = $file;
		$this->data = $data;
		$this->columnSeparator = $columnSeparator;

		$this->parseFileHeader();

		if (!$this->detectHeadersValidity())
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JEV_NOT_A_VALID_CSV_UPLOADED'), 'warning');

			return false;
		}

		if (!$this->convertFile())
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JEV_IMPORT_CORRUPT_CANCELLED'), 'warning');

			return false;
		}

		JFactory::getApplication()->enqueueMessage(JText::_('JEV_IMPORT_CSV_CONVERTED'), 'notice');

	}

	/**
	 * Function for retrive converted temp file information
	 *
	 * @return array with name and path to temp file
	 */
	public function getConvertedTempFile()
	{
		$file = array("name" => JString::substr($this->tmpFileName, strrpos($this->tmpFileName, DIRECTORY_SEPARATOR) + 1),
			"tmp_name" => $this->tmpFileName);
		return $file;

	}

	/**
	 * Function for retrieve raw converted data from temp file
	 *
	 * @return raw data converted CSV to iCal
	 */
	public function getRawData()
	{
		return @file_get_contents($this->tmpFileName);

	}

	/**
	 * Function parses first line of the CSV input with headers
	 */
	private function parseFileHeader()
	{
		if ($this->data){
			$line = JString::substr($this->data,0,JString::strpos($this->data,"\n")+1);
		}
		else {
			$fp = fopen($this->file, 'r');
			$line = fgets($fp, 4096);
			$line = trim($line); // remove white spaces, at the end always \n (or \r\n)
			fclose($fp);
		}

		$headers = explode($this->columnSeparator, $line);
		$this->colsNum = count($headers);
		for ($i = 0; $i < $this->colsNum; $i++)
		{
			// -------- remove the utf-8 BOM ----
			 $headers[$i] = str_replace("\xEF\xBB\xBF",'', $headers[$i]);
			$this->colsOrder[str_replace('"', '', trim($headers[$i]))] = $i;
			// some people let white space at the end of text, so better to trim
			// CSV has often begining and ending " - replace it
		}
	}

	/**
	 * Function parses Csv line due previously detected column order,
	 * special treatment for mandatory columns
	 *
	 * @return parsed data line or false if error
	 */
	private function parseCsvLine($line)
	{
		$data = str_getcsv($line);
		// different count of data cols than header cols, bad CSV
		if (count($data) != $this->colsNum && count($data) != 1)
		{ // == 1 probably last empty line
			// different number of cols than in header, file is not in correct format
			return false;
		}
		$dataLine = new CsvLine($data[$this->colsOrder["CATEGORIES"]],
						$data[$this->colsOrder["SUMMARY"]],
						$data[$this->colsOrder["DTSTART"]],
						$data[$this->colsOrder["DTEND"]]);
		foreach ($this->colsOrder as $col => $order)
		{
			switch ($col) {
				case "TIMEZONE":
					$dataLine->setTimezone($data[$order]);
					break;
				case "LOCATION":
					$dataLine->setLocation($data[$order]);
					break;
				case "DTSTAMP":
					$dataLine->setDtstamp($data[$order]);
					break;
				case "X-EXTRAINFO":
					$dataLine->setExtraInfo($data[$order]);
					break;
				case "CONTACT":
					$dataLine->setContact($data[$order]);
					break;
				case "DESCRIPTION":
					$dataLine->setDescription($data[$order]);
					break;
				case "RRULE":
					$dataLine->setRrule($data[$order]);					
					break;
				case "UID":
					$dataLine->setUid($data[$order]);
					break;
				case "CATEGORIES":
				case "SUMMARY":
				case "DTSTART":
				case "DTEND":
					break;
				case "NOENDTIME":
					$dataLine->setNoendtime($data[$order]);					
					break;
				case "MULTIDAY":
					$dataLine->setMultiday($data[$order]);
					break;
				default:
					$dataLine->customField($data[$order], $col);
					break;
			}
		}
		return $dataLine;

	}

	/**
	 * Check, if mandatory cols are present
	 *
	 * @return true if necessary headers present, false if not
	 */
	private function detectHeadersValidity()
	{
		if (isset($this->colsOrder["CATEGORIES"]) &&
				isset($this->colsOrder["SUMMARY"]) &&
				isset($this->colsOrder["DTSTART"]) &&
				isset($this->colsOrder["DTEND"]) &&
				isset($this->colsOrder["TIMEZONE"]))
			return true;
		else{	
			return false;
		}

	}

	/**
	 * Constructs new temporary file in iCal format, which will be
	 * used in CSV transformation
	 */
	private function createNewTmpICal()
	{
		$config =  JFactory::getConfig();
		$path = $config->get('config.tmp_path') ? $config->get('config.tmp_path') : $config->get('tmp_path');
		echo "create temp CSV conversion file in ".$path."<br/>";
		$this->tmpFileName = tempnam($path, "phpJE");
		//$this->tmpFileName = tempnam("/tmp", "phpJE");
		$this->tmpfile = fopen($this->tmpFileName, "w");
		fwrite($this->tmpfile, "BEGIN:VCALENDAR\n");
		fwrite($this->tmpfile, "VERSION:2.0\n");
		fwrite($this->tmpfile, "PRODID:-//jEvents 2.0 for Joomla//EN\n");
		fwrite($this->tmpfile, "CALSCALE:GREGORIAN\n");
		fwrite($this->tmpfile, "METHOD:PUBLISH\n");

	}

	/**
	 * Function finalizes temporary iCal file
	 */
	private function finalizeTmpICal()
	{
		fwrite($this->tmpfile, "END:VCALENDAR\n");
		fclose($this->tmpfile);

	}

	/**
	 * Function converts file from CSV to iCal
	 *
	 * @return true if success, false in case of error
	 */
	private function convertFile($delimiter="\n")
	{
		$this->createNewTmpICal();  // creates new temporary iCal file

		if ($this->data){
			// unfold content lines according the unfolding procedure of rfc2445
			$this->data = str_replace("\n ","",$this->data);
			$this->data = str_replace("\n\t","",$this->data);
                        
			// Convert string into array for easier processing
			$this->data = explode("\n", $this->data);
                        for ($i=0;$i<count($this->data); $i++){
                            $buffer = $this->data[$i];
                            while ((!$line = $this->parseCsvLine($buffer)) && $i+1<count($this->data))
                            {
                                $i++;
                                $buffer .= $this->data[$i];
                            }
                            if (!$line)
                            {
					// something gone wrong, CSV is corrupted, cancel
					return false;
                            }
                            if ($i == 0)
				continue; // fist line is header, so continue
                            fwrite($this->tmpfile, $line->getInICalFormat()); // write to the converted file				
                        }
                        /*
                         * this approach doesn't deal with carraige returns within fields!
			$i = 1;
			foreach ($this->data as $buffer){
				if ($buffer == "")
					continue; // end of file or empty line
				if (!$line = $this->parseCsvLine($buffer))
				{
					// something gone wrong, CSV is corrupted, cancel
					return false;
				}
				if ($i++ == 1)
					continue; // fist line is header, so continue
				fwrite($this->tmpfile, $line->getInICalFormat()); // write to the converted file				
			}
                         */
		}
		else {
			$fp = fopen($this->file, 'r');
			$i = 1;
			while (!feof($fp))
			{
				$buffer = fgets($fp);
				if ($buffer == "")
					break; // end of file or empty line
				if (!$line = $this->parseCsvLine($buffer))
				{
					// something gone wrong, CSV is corrupted, cancel
					return false;
				}
				if ($i++ == 1)
					continue; // fist line is header, so continue
				fwrite($this->tmpfile, $line->getInICalFormat()); // write to the converted file
				$buffer = ''; // clear the buffer
			}
		}
				
		$this->finalizeTmpICal();
		return true;

	}

}