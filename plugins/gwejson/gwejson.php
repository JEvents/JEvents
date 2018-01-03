<?php

/**
 * @package     GWE Systems
 * @subpackage  System.Gwejson
 *
 * @copyright   Copyright (C)  2015 GWE Systems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

/*
  if (defined('_SC_START')){
  list ($usec, $sec) = explode(" ", microtime());
  $time_end = (float) $usec + (float) $sec;
  echo "Executed in ". round($time_end - _SC_START, 4)."<Br/>";
  }
 */

/**
 * System plugin to execute JSON requests without the overhead of full Joomla infrastructure being loaded
 * For best performance should be the first plugin to run
 *
 * @since  2.5
 */
class PlgSystemGwejson extends JPlugin
{

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$input = JFactory::getApplication()->input;
		$task = $input->get('task', $input->get('typeaheadtask', '', 'cmd'), 'cmd');

		if ($task != "gwejson")
		{
			return true;
		}
                // Some plugins set the document type too early which messes up our ouput.
                $this->doc = JFactory::getDocument();
	}
    
	/**
	 * Method to catch the onAfterInitialise event.
	 *
	 * @return  boolean  True on success
	 *
	 */
	public
			function onAfterInitialise()
	{

		$input = JFactory::getApplication()->input;
		$task = $input->get('task', $input->get('typeaheadtask', '', 'cmd'), 'cmd');
		// in frontend SEF
		if ($task != "gwejson")
		{
			return true;
		}

		$file = $input->get('file', '', 'cmd');
		// Library file MUST start with "gwejson_" for security reasons to stop other files being included maliciously
		if ($file == "")
		{
			return true;
		}
		if ( strpos($file, "gwejson_")!==0){
			$file = "gwejson_".$file;
		}

		$path = $input->get('path', 'site', 'cmd');
		$paths = array("site" => JPATH_SITE, "admin" => JPATH_ADMINISTRATOR, "plugin" => JPATH_SITE . "/plugins", "module" => JPATH_SITE . "/modules", "library" => JPATH_LIBRARIES);
		if (!in_array($path, array_keys($paths)))
		{
			return true;
		}
		$folder = $input->get('folder', '', 'string');
		if ($path == "plugin")
		{
			$plugin = $input->get('plugin', '', 'string');
			if ($folder == "" || $plugin == "")
			{
				return true;
			}
			$path = $paths[$path] . "/$folder/$plugin/";
		}
		else if ($path == "module" || $path == "library") {
			if ($folder == "" )
			{
				return true;
			}
			$path = $paths[$path] . "/$folder/";
		}
		else
		{
			$extension = $input->get('option', $input->get('ttoption', '', 'cmd'), 'cmd');
			if ($extension == "")
			{
				return true;
			}
			if ($folder == "" )
			{
				$path = $paths[$path] . "/components/$extension/libraries/";
			}
			else {
				$path = $paths[$path] . "/components/$extension/$folder/";
			}
		}

		jimport('joomla.filesystem.file');
                // Check for a custom version of the file first!
                $custom_file =  str_replace("gwejson_", "gwejson_custom_", $file);
                if (JFile::exists($path . $custom_file . ".php"))
                {
                        $file = $custom_file;
                }
                if (!JFile::exists($path . $file . ".php"))
                {
	                PlgSystemGwejson::throwerror("Opps we could not find the file: " . $path . $file . ".php");
	                return true;
                }

		include_once ($path . $file . ".php");

		if (!function_exists("gwejson_skiptoken") || !gwejson_skiptoken()){
			$token = JSession::getFormToken();;
			if ($token != $input->get('token', '', 'string')){
				if ($input->get('json', '', 'raw')){
					
				}
				PlgSystemGwejson::throwerror("There was an error - bad token.  Please refresh the page and try again.");
			}
		}

		// we don't want any modules etc.
		//$input->set('tmpl', 'component');
		$input->set('format', 'json');

		ini_set("display_errors",0);

		// When setting typeahead in the post it overrides the GET value which the prepare function doesn't replace for some reason :(
		if ($input->get('typeahead', '', 'string')!="" || $input->get('prefetch', 0, 'int'))
		{
			try {
				$requestObject = new stdClass();
				$requestObject->typeahead = $input->get('typeahead', '', 'string');
				$data = null;
				$data = ProcessJsonRequest($requestObject, $data);
			}
			catch (Exception $e) {
				//PlgSystemGwejson::throwerror("There was an exception ".$e->getMessage()." ".var_export($e->getTrace()));
				PlgSystemGwejson::throwerror("There was an exception " . addslashes($e->getMessage()));
			}
		}

		// Get JSON data
		else  if ($input->get('json', '', 'raw'))
		{
			// Create JSON data structure
			$data = new stdClass();
			$data->error = 0;
			$data->result = "ERROR";
			$data->user = "";

			$requestData =  $input->get('json', '', 'raw');

			if (isset($requestData))
			{
				try {
					if (ini_get("magic_quotes_gpc"))
					{
						$requestData = stripslashes($requestData);
					}

					$requestObject = json_decode($requestData, 0);
					if (!$requestObject)
					{
						$requestObject = json_decode(utf8_encode($requestData), 0);
					}
				}
				catch (Exception $e) {
					PlgSystemGwejson::throwerror("There was an exception");
				}

				if (!$requestObject)
				{
					//file_put_contents(dirname(__FILE__) . "/cache/error.txt", var_export($requestData, true));
					PlgSystemGwejson::throwerror("There was an error - no request object ");
				}
				else if ($requestObject->error)
				{
					PlgSystemGwejson::throwerror("There was an error - Request object error " . $requestObject->error);
				}
				else
				{
					try {
						$data = ProcessJsonRequest($requestObject, $data);
					}
					catch (Exception $e) {
						//PlgSystemGwejson::throwerror("There was an exception ".$e->getMessage()." ".var_export($e->getTrace()));
						PlgSystemGwejson::throwerror("There was an exception " . $e->getMessage());
					}
				}
			}
			else
			{
				PlgSystemGwejson::throwerror("Invalid Input");
			}
		}
		else
		{
			PlgSystemGwejson::throwerror("There was an error - no request data");
		}

		header("Content-Type: application/javascript; charset=utf-8");

		if (is_object($data)){
			if (defined('_SC_START'))
			{
				list ($usec,$sec) = explode(" ", microtime());
				$time_end = (float)$usec + (float)$sec;
				$data->timing = round($time_end - _SC_START,4);
			}
			else {
				$data->timing = 0;
			}
		}

		// Must suppress any error messages
		@ob_end_clean();
		echo json_encode($data);

		exit();

	}

	public static function throwerror ($msg){
		$data = new stdClass();
		//"document.getElementById('products').innerHTML='There was an error - no valid argument'");
		$data->error = "alert('".$msg."')";
		$data->result = "ERROR";
		$data->user = "";

		header("Content-Type: application/javascript");
		// Must suppress any error messages
		@ob_end_clean();
		echo json_encode($data);
		exit();
	}
}
