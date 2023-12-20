<?php

/**
 * @package     GWE Systems
 * @subpackage  System.Gwejson
 *
 * @copyright   Copyright (C)  2015 - JEVENTS_COPYRIGHT GWE Systems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\CMSPlugin;

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
#[\AllowDynamicProperties]
class PlgSystemGwejson extends CMSPlugin
{

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$input = Factory::getApplication()->input;
		$task  = $input->get('task', $input->get('typeaheadtask', '', 'cmd'), 'cmd');

		if ($task != "gwejson")
		{
			return true;
		}
		/*
		 *  In Joomla 4
		 *
		 * We need
		 * Factory::getApplication()->loadDocument();
		 * because
		 * Factory::getApplication()->getDocument();
		 * can return a null e.g. from modules loaded by removeLoader module
		 *
		 */
		if (version_compare(JVERSION, "4", "gt"))
		{
			Factory::getApplication()->loadDocument();
		}
		// Some plugins set the document type too early which messes up our ouput.
		$this->doc = Factory::getDocument();
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

		$input = Factory::getApplication()->input;
		$task  = $input->get('task', $input->get('typeaheadtask', '', 'cmd'), 'cmd');
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
		if (strpos($file, "gwejson_") !== 0)
		{
			$file = "gwejson_" . $file;
		}

		$path  = $input->get('path', 'site', 'cmd');
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
		else if ($path == "module" || $path == "library")
		{
			if ($folder == "")
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
			if ($folder == "")
			{
				$path = $paths[$path] . "/components/$extension/libraries/";
			}
			else
			{
				$path = $paths[$path] . "/components/$extension/$folder/";
			}
		}

		jimport('joomla.filesystem.file');
		// Check for a custom version of the file first!
		$custom_file = str_replace("gwejson_", "gwejson_custom_", $file);
		if (JFile::exists($path . $custom_file . ".php"))
		{
			$file = $custom_file;
		}
		if (!JFile::exists($path . $file . ".php"))
		{
			PlgSystemGwejson::throwerror("Whoops we could not find the action file");

			return true;
		}

		include_once($path . $file . ".php");

		if (!function_exists("gwejson_skiptoken") || !gwejson_skiptoken())
		{
			$token = JSession::getFormToken();;
			if ($token != $input->get('token', '', 'string'))
			{
				if ($input->get('json', '', 'raw'))
				{

				}
				PlgSystemGwejson::throwerror("There was an error - bad token.  Please refresh the page and try again.");
			}
		}

		// we don't want any modules etc.
		//$input->set('tmpl', 'component');
		$input->set('format', 'json');

		ini_set("display_errors", 0);

		// When setting typeahead in the post it overrides the GET value which the prepare function doesn't replace for some reason :(
		if ($input->get('typeahead', '', 'string') != "" || $input->get('prefetch', 0, 'int'))
		{
			try
			{
				$requestObject            = new stdClass();
				$requestObject->typeahead = $input->get('typeahead', '', 'string');
				// Needed for PHP 8
				$data = new stdClass();
				$data = ProcessJsonRequest($requestObject, $data);
			}
			catch (Exception $e)
			{
				//PlgSystemGwejson::throwerror("There was an exception ".$e->getMessage()." ".var_export($e->getTrace()));
				PlgSystemGwejson::throwerror("There was an exception " . addslashes($e->getMessage()));
			}
		}

		// Get JSON data
		else if ($input->get('json', '', 'raw'))
		{
			// Create JSON data structure
			$data         = new stdClass();
			$data->error  = 0;
			$data->result = "ERROR";
			$data->user   = "";

			$requestData = $input->get('json', '', 'raw');

			if (isset($requestData))
			{
				try
				{
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
				catch (Exception $e)
				{
					PlgSystemGwejson::throwerror("There was an exception");
				}

				if (!$requestObject)
				{
					//file_put_contents(dirname(__FILE__) . "/cache/error.txt", var_export($requestData, true));
					PlgSystemGwejson::throwerror("There was an error - no request object ");
				}
				else if (isset($requestObject->error) && $requestObject->error)
				{
					PlgSystemGwejson::throwerror("There was an error - Request object error " . $requestObject->error);
				}
				else
				{
					try
					{
						$data = ProcessJsonRequest($requestObject, $data);
					}
					catch (Exception $e)
					{
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

		if (is_object($data))
		{
			if (defined('_SC_START'))
			{
				list ($usec, $sec) = explode(" ", microtime());
				$time_end     = (float) $usec + (float) $sec;
				$data->timing = round($time_end - _SC_START, 4);
			}
			else
			{
				$data->timing = 0;
			}
		}

		// Must suppress any error messages
		@ob_end_clean();
		echo json_encode($data);

		exit();

	}

	public static function throwerror($msg)
	{
		$data = new stdClass();
		//"document.getElementById('products').innerHTML='There was an error - no valid argument'");
		$data->error  = "alert('" . $msg . "')";
		$data->result = "ERROR";
		$data->user   = "";

		header("Content-Type: application/javascript");
		// Must suppress any error messages
		@ob_end_clean();
		echo json_encode($data);
		exit();
	}


	// Mechanism to inject theme specific config options into module and menu item config
	// Problem is that the fields are not dynamically loaded when you change the theme
	public function onContentPrepareForm($form, $data)
	{
        $input = Factory::getApplication()->input;
        $inputFormData = $input->post->get('jform', [], 'array');

		// this doesn't work yet since there is no way to inject filtering into the category model
		if (false && $form->getName() === "com_categories.categories.jevents.filter" && Factory::getApplication()->isClient('administrator'))
		{
            $jeventsCategoriesFilters = Folder::files(JPATH_ADMINISTRATOR . "/components/com_jevents/models/forms/", 'filter_categories.xml', true, true);
			foreach ($jeventsCategoriesFilters as $jeventsCategoriesFilter)
            {
                $form->loadFile( $jeventsCategoriesFilter, false );
            }
		}
		else if ( ($form->getName() === "com_menus.item" && isset($data->link) && strpos($data->link, "com_jevents&") > 0)
		|| ($form->getName() === "com_modules.module" &&
		    (isset($data->module) && $data->module == "mod_jevents_latest")|| (isset($inputFormData["module"]) && $inputFormData["module"] == "mod_jevents_latest"))
        )
        	{
			if (Factory::getApplication()->isClient('administrator'))
			{
				$menuConfigFiles = Folder::files(JPATH_SITE . "/components/com_jevents/views/", 'menuconfig.xml', true, true);

				foreach ($menuConfigFiles as $menuConfigFile)
				{
					$theme = basename(dirname($menuConfigFile));
					$langfile   = 'files_jevents' . str_replace('files_', '', strtolower(InputFilter::getInstance()->clean((string) $theme, 'cmd')))."layout";
					$lang       = Factory::getLanguage();
					$lang->load($langfile, JPATH_SITE, null, false, true);

					$form->loadFile($menuConfigFile, false);
				}
			}


			$afterfields = $form->getXml()->xpath('//*[@after]');
			foreach ($afterfields as $afterfield)
            {

				$field1Xml = $form->getFieldXml($afterfield->attributes()->name, $afterfield->attributes()->thisgroup);
                $field2Xml = $form->getFieldXml($afterfield->attributes()->after, $afterfield->attributes()->aftergroup);
	            
				if ($field1Xml && $field2Xml)
                {
                    $followingField = dom_import_simplexml( $field1Xml );
                    $fieldToFollow  = dom_import_simplexml( $field2Xml );

                    if ( $fieldToFollow && $followingField )
                    {
                        if ( $fieldToFollow->nextSibling )
                        {
                            $x = $fieldToFollow->parentNode->insertBefore( $followingField, $fieldToFollow->nextSibling );
                        }
                        else
                        {
                            $x = $fieldToFollow->parentNode->appendChild( $followingField );
                        }
                    }
                }
            }

		}
	}


	public
	function onAfterRender()
	{
		if (version_compare(JVERSION, '4.0.0', 'ge'))
		{

			$document = Factory::getApplication()->getDocument();
			$wa      = $document->getWebAssetManager();
			$scripts = $wa->getAssets('script');
		}
	}

	// Experiment in manipulation of Joomla backend menu
	public function XXXonPreprocessMenuItems($context, & $items, $params = null, $enabled = true)
	{
		if (version_compare(JVERSION, '4.0.0', 'ge') && Factory::getApplication()->isClient('administrator') && Factory::getApplication()->input->get('option') == "com_jevents")
		{
			$user = JFactory::getUser();

			$jeventsItems = GslHelper::getLeftIconLinks();
			$cloneRoot = false;
			foreach ($items as $i => $item)
			{
				if ($item->element == 'com_jevents' && $item->level == 1)
				{

					if ($item->hasChildren())
					{
						$children    = $items[$i]->getChildren();

						foreach ($children as $child)
						{
							if (!$cloneRoot)
							{
								$cloneRoot = clone $child;
							}
							else
							{
								// $item->removeChild($child);
							}

							if (strpos($child->link , "redirect.com_jevlocations") > 0)
							{
								$child->link = "index.php?option=com_jevlocations";
							}
							else if (strpos($child->link , "redirect.com_rsvppro") > 0)
							{
								$child->link = "index.php?option=com_rsvppro";
							}
							else if (strpos($child->link , "redirect.com_jeventstags") > 0)
							{
								$child->link = "index.php?option=com_jeventstags";
							}
							else if (strpos($child->link , "redirect.com_jevpeople") > 0)
							{
								$child->link = "index.php?option=com_jevpeople";
							}
							else if (strpos($child->link , "redirect.com_categories") > 0)
							{
								$child->link = "index.php?option=com_categories&view=categories&extension=com_jevents";
							}
						}
					}
					foreach ($jeventsItems as $jeventsItem)
					{
						if (strpos( $jeventsItem->link, "com_yoursites") > 0)
						{
							continue;
						}
						if (strpos( $jeventsItem->icon, "joomla") ===  0)
						{
							continue;
						}
						$matched = false;
						foreach ($children as $child)
						{
							if ($jeventsItem->link == Route::_($child->link, true))
							{
								$child->title = $jeventsItem->label;

								ob_start();
								if (!empty($jeventsItem->icon)) {
									?><span data-gsl-icon='icon: <?php echo $jeventsItem->icon; ?>' class='gsl-margin-small-right me-2'></span><?php
								} else if (!empty($jeventsItem->iconSrc)) {
									?><span class='gsl-margin-small-right me-2'><img src='<?php echo $jeventsItem->iconSrc; ?>' /></span><?php
								}
								$icon = ob_get_clean();

								$icon = "<span aria-hidden='true' class='icon-fw icon-puzzle'></span>";
								$child->title = $icon . $child->title;
								// no use on child menu items!
								//$child->class = "class:puzzle";

								$matched = true;
								break;
							}

							$x = 1;
						}

						if (!$matched)
						{
							$newClone = clone $cloneRoot;
							$newClone->title = $jeventsItem->label;
							$newClone->link = Uri::getInstance($jeventsItem->link);
							$newClone->link = "index.php" . $newClone->link->toString(['query']);

							$items[$i]->addChild($newClone);
						}
					}
				}
			}

		}
	}
}