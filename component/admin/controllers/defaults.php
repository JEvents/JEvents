<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: defaults.php 3308 2012-02-28 10:13:19Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Filesystem\Folder;

jimport('joomla.application.component.controllerform');

#[\AllowDynamicProperties]
class AdminDefaultsController extends Joomla\CMS\MVC\Controller\FormController
{
	/**
	 * Controler for the Control Panel
	 *
	 * @param array        configuration
	 */
	function __construct($config = array())
	{

		parent::__construct($config);

		if (!JEVHelper::isAdminUser())
		{
			Factory::getApplication()->enqueueMessage("Not Authorised - must be admin", 'warning');
			Factory::getApplication()->redirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel");

			return;
		}

		$this->registerTask('list', 'overview');
		$this->registerTask('new', 'edit');
		$this->registerTask('save', 'apply');
		$this->registerDefaultTask("overview");

		// Make sure DB is up to date
		$db = Factory::getDbo();
		$db->setQuery("SELECT * FROM #__jev_defaults where catid='' AND language IN ('', '*')");
		$defaults = $db->loadObjectList("name");
		if (!isset($defaults['icalevent.detail_body']))
		{
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.detail_body',
						title=" . $db->Quote("JEV_EVENT_DETAIL_PAGE") . ",
						subject='',
						value='',
						state=0,
						params='{}'");
			$db->execute();
		}
		else
		{
			$db->setQuery("UPDATE #__jev_defaults set title=" . $db->Quote("JEV_EVENT_DETAIL_PAGE") . " WHERE name='icalevent.detail_body'");
			$db->execute();
		}

		if (!isset($defaults['icalevent.edit_page']))
		{
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.edit_page',
						title=" . $db->Quote("JEV_EVENT_EDIT_PAGE") . ",
						subject='',
						value='',
						state=0,
						params='{}'");
			$db->execute();
		}
		else
		{
			$db->setQuery("UPDATE #__jev_defaults set title=" . $db->Quote("JEV_EVENT_EDIT_PAGE") . " WHERE name='icalevent.edit_page'");
			$db->execute();
		}

		if (!isset($defaults['icalevent.list_row']))
		{
			$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.list_row',
						title=" . $db->Quote("JEV_EVENT_LIST_ROW") . ",
						subject='',
						value='',
						state=0,
						params='{}'");
			$db->execute();
		}
		else
		{
			$db->setQuery("UPDATE #__jev_defaults set title=" . $db->Quote("JEV_EVENT_LIST_ROW") . " WHERE name='icalevent.list_row'");
			$db->execute();
		}

		if (!isset($defaults['month.calendar_cell']))
		{
			$db->setQuery("INSERT INTO  #__jev_defaults set name='month.calendar_cell',
						title=" . $db->Quote("JEV_EVENT_MONTH_CALENDAR_CELL") . ",
						subject='',
						value='',
						state=0,
						params='{}'");
			$db->execute();
		}
		else
		{
			$db->setQuery("UPDATE #__jev_defaults set title=" . $db->Quote("JEV_EVENT_MONTH_CALENDAR_CELL") . " WHERE name='month.calendar_cell'");
			$db->execute();
		}

		if (!isset($defaults['month.calendar_tip']))
		{
			$db->setQuery("INSERT INTO  #__jev_defaults set name='month.calendar_tip',
						title=" . $db->Quote("JEV_EVENT_MONTH_CALENDAR_TIP") . ",
						subject='',
						value='',
						state=0,
						params='{}'");
			$db->execute();
		}
		else
		{
			$db->setQuery("UPDATE #__jev_defaults set title=" . $db->Quote("JEV_EVENT_MONTH_CALENDAR_TIP") . " WHERE name='month.calendar_tip'");
			$db->execute();
		}

		if (!isset($defaults['module.latest_event']))
		{
			$db->setQuery("INSERT INTO  #__jev_defaults set name='module.latest_event',
						title=" . $db->Quote("JEV_TAB_LATEST_MOD") . ",
						subject='',
						value='',
						state=0,
						params='{}'");
			$db->execute();
		}
		else
		{
			$db->setQuery("UPDATE #__jev_defaults set title=" . $db->Quote("JEV_TAB_LATEST_MOD") . " WHERE name='module.latest_event'");
			$db->execute();
		}

		/*
		 * Edit Page config must wait for plugins to be updated!
				if (!isset($defaults['icalevent.edit_page'])){
					$db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.edit_page',
								title=".$db->Quote(Text::_("JEV_EVENT_EDIT_PAGE")).",
								subject='',
								value='',
								state=0");
					$db->execute();
				}
				else {
					$db->setQuery("UPDATE #__jev_defaults set title=".$db->Quote(Text::_("JEV_EVENT_EDIT_PAGE"))." WHERE name='icalevent.edit_page'");
					$db->execute();
				}
		*/

        $path = JPATH_COMPONENT_SITE . "/views/float";
        if (Folder::exists($path))
        {
            foreach ( Folder::files( $path, ".html", true, true ) as $layoutname )
            {
                $d1 = basename( dirname( $layoutname ) );
                if ( $d1 == "defaults" && basename( $layoutname ) !== "index.html" && strpos( $layoutname, ".php" ) === false )
                {
                    $layoutIdentifier = str_replace( ".html", "", basename( $layoutname ) );

                    if ( ! isset( $defaults[$layoutIdentifier] ) && strpos( $layoutname, ".module" ) === false )
                    {
                        $title = $db->Quote( "JEV_FLOAT_LAYOUT_" . strtoupper( str_replace( array(
                                "icalevent.list_",
                                ".module"
                            ), array( "", "_MODULE" ), $layoutIdentifier ) ) );
                        //echo $layoutname . " " . $layoutIdentifier . "<br>";
                        $db->setQuery( "INSERT INTO  #__jev_defaults set name = " . $db->Quote( $layoutIdentifier ) . ",
						title=" . $title . ",
						subject='',
						value='',
						state=0,
						params='{}'" );
                        $db->execute();
                    }
                }
            }
        }
	}

	/**
	 * List Ical Events
	 *
	 */
	function overview()
	{

		$this->populateLanguages();
		$this->populateCategories();

		// get the view
		$this->view = $this->getView("defaults", "html");

		// Set the layout
		$this->view->setLayout('overview');

		// Get/Create the model
		if ($model = $this->getModel("defaults", "defaultsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->display();
	}

		private function populateLanguages()
	{

		$db = Factory::getDbo();

		// get the list of languages first
		$query = $db->getQuery(true);
		$query->select("l.*");
		$query->from("#__languages as l");
		$query->where('l.lang_code <> "xx-XX"');
		$query->order("l.lang_code asc");

		$db->setQuery($query);
		$languages = $db->loadObjectList('lang_code');

		// remove ones where the language is no longer installed
		$query       = $db->getQuery(true);
		$langcodes   = array();
		$langcodes[] = $db->quote("*");
		foreach ($languages as $lang)
		{
			$langcodes[] = $db->quote($lang->lang_code);
		}
		$langcodes = implode(",", $langcodes);
		$query->delete('#__jev_defaults')->where("language NOT IN ($langcodes)");
		$db->setQuery($query);
		$db->execute();

		// not needed if only one language
		if (count($languages) == 1)
		{
			return;
		}

		// Clean up bad data
		$query = $db->getQuery(true);
		$query->select("count(id) as duplicatecount, name, language, catid");
		$query->from("#__jev_defaults as def");
		$query->group("name, language, catid");
		$query->having("duplicatecount > 1");
		$db->setQuery($query);
		$xxx        = (string) $db->getQuery();
		$duplicates = $db->loadObjectList();

		if (count($duplicates))
		{
			foreach ($duplicates as $duplicate)
			{
				$query = $db->getQuery(true);
				$query->select("id, state, name, language, value");
				$query->from("#__jev_defaults as def");
				$query->where('def.name =' . $db->quote($duplicate->name));
				$query->where('def.catid =' . $db->quote($duplicate->catid));
				$query->where('def.language =' . $db->quote($duplicate->language));
				$query->order("name, language, state desc, id desc");
				$db->setQuery($query);
				$duplicatedetails = $db->loadObjectList();
				if (count($duplicatedetails) == $duplicate->duplicatecount)
				{
					// Keep the most up to date published entry
					if ($duplicatedetails[0]->state == 1)
					{
						$dupids = array();
						for ($d = 1; $d < $duplicate->duplicatecount; $d++)
						{
							$dupids[] = $duplicatedetails[$d]->id;
						}
						if (count($dupids) > 0)
						{
							$query = $db->getQuery(true);
							$query->delete("#__jev_defaults");
							$query->where("id in (" . implode(",", $dupids) . ")");
							$db->setQuery($query);
							//var_dump($duplicate);
							//echo "<hr/>";
							//echo (string) $db->getQuery();exit();
							$db->execute();
						}
					}
					else
					{
						// sort by descending value
						usort($duplicatedetails, function ($a, $b) {

							return -1 * strcmp($a->value, $b->value);
						});
						$dupids = array();
						for ($d = 1; $d < $duplicate->duplicatecount; $d++)
						{
							$dupids[] = $duplicatedetails[$d]->id;
						}
						if (count($dupids) > 0)
						{
							$query = $db->getQuery(true);
							$query->delete("#__jev_defaults");
							$query->where("id in (" . implode(",", $dupids) . ")");
							$db->setQuery($query);
							//var_dump($duplicate);
							//echo "<hr/>";
							//echo (string) $db->getQuery();exit();
							$db->execute();
						}
					}
				}
			}
		}

		$query = $db->getQuery(true);
		$query->select("def.*");
		$query->from("#__jev_defaults as def");

		$query->where('def.language = "*"');

		$query->order("def.title asc");
		$db->setQuery($query);
		$allLanguageTitles = $db->loadObjectList();

		$query = $db->getQuery(true);
		$query->select("def.*");
		$query->from("#__jev_defaults as def");

		$query->where('def.language <> "*"');

		$query->order("def.title, language asc");

		$db->setQuery($query);
		$specificLanguageTitles = $db->loadObjectList();

		$missingDefaults = array();
		foreach ($allLanguageTitles as $title)
		{
			foreach ($languages as $lang_code => $lang)
			{
				$matched = false;
				foreach ($specificLanguageTitles as $stitle)
				{
					if ($title->name == $stitle->name && $stitle->language == $lang_code  && $stitle->catid == $title->catid)
					{
						$matched = true;
						break;
					}
				}
				if (!$matched)
				{
					$missingDefaults[] = array("lang_code" => $lang_code, "title" => $title);
				}
			}
		}

		if (count($missingDefaults) > 0)
		{
			$query = $db->getQuery(true);
			$query->insert("#__jev_defaults")->columns("title, name, subject,value,state,params,language, catid");
			foreach ($missingDefaults as $md)
			{
				$values = array($db->quote($md["title"]->title), $db->quote($md["title"]->name), $db->quote($md["title"]->subject), $db->quote($md["title"]->value), 0, $db->quote($md["title"]->params), $db->quote($md["lang_code"]), $db->quote($md["title"]->catid));
				$query->values(implode(",", $values));
			}
			$db->setQuery($query);
			$db->execute();
		}

		//echo $db->getgetErrorMsg();

	} // editdefaults()

	private function populateCategories()
	{

		$db = Factory::getDbo();

		// get the list of categories first
		$query = $db->getQuery(true);
		$query->select("c.*");
		$query->from("#__categories as c");
		$query->where('extension="com_jevents"');
		$query->where('published=1');
		$query->order("c.title asc");

		$db->setQuery($query);
		$categories = $db->loadObjectList('id');

		// remove ones where the category is no longer installed
		$query   = $db->getQuery(true);
		$cats    = array();
		$cats[0] = $db->quote("0");
		foreach ($categories as $cat)
		{
			$cats[$cat->id] = $db->quote($cat->id);
		}
		$incats = implode(",", $cats);
		$query->delete('#__jev_defaults')->where("catid NOT IN ($incats)");
		$db->setQuery($query);
		$db->execute();

		// not needed if only one category
		if (count($cats) == 1)
		{
			return;
		}
		$query = $db->getQuery(true);
		$query->select("def.*");
		$query->from("#__jev_defaults as def");

		//$query->where('def.language = "*"');
		$query->where('def.catid = "0"');
		$query->where('def.name NOT like ("com_jevpeople%") AND def.name NOT like ("com_jevlocations%")');
		$query->order("def.name asc");
		$db->setQuery($query);
		$tempdata      = $db->loadObjectList();
		$allCatidNames = array();
		foreach ($tempdata as $td)
		{
			if (!isset($allCatidNames[$td->name]))
			{
				$allCatidNames[$td->name] = array();
			}
			$allCatidNames[$td->name][] = $td;
		}

		$query = $db->getQuery(true);
		$query->select("def.*");
		$query->from("#__jev_defaults as def");

		$query->where('def.catid<> "0"');
		$query->where('def.name NOT like ("com_jevpeople%") AND def.name NOT like ("com_jevlocations%")');
		$query->order("def.name, catid asc");

		$db->setQuery($query);
		$catdata               = $db->loadObjectList();
		$specificCategoryNames = array();
		foreach ($catdata as $cat)
		{
			if (!isset($specificCategoryNames[$cat->name]))
			{
				$specificCategoryNames[$cat->name] = array();
			}
			$specificCategoryNames[$cat->name][$cat->catid . "." . $cat->language] = $cat;
		}

		$missingDefaults = array();
		foreach ($allCatidNames as $name => $namedata)
		{
			// all language versions to check and populate
			if (!isset($specificCategoryNames[$name]))
			{
				foreach ($cats as $catid => $cat)
				{
					if ($catid == 0) continue;
					foreach ($namedata as $nd)
					{
						$missingDefaults[] = array("catid" => $catid, "lang_code" => $nd->language, "name" => $nd);
						//echo $nd->name." ".$catid." ".$nd->language."<Br/>";
					}
				}
			}
			else
			{
				foreach ($namedata as $nd)
				{
					foreach ($cats as $catid => $cat)
					{
						if ($catid == 0) continue;
						$matched = false;
						foreach ($specificCategoryNames[$name] as $sname)
						{
							if ($nd->name == $sname->name && $nd->language == $sname->language && $sname->catid == $catid)
							{
								$matched = true;
								break;
							}
						}
						if (!$matched)
						{
							$missingDefaults[] = array("catid" => $catid, "lang_code" => $nd->language, "name" => $nd);
							//echo $nd->name." ".$catid." ".$nd->language."<Br/>";
						}
					}
				}

			}

			/*
					foreach ($namedata as $allcat){
						if ( $catid == $sname->catid){
							$matched = false;
							foreach ($specificCategoryNames as $sname){
								if ($name->name == $sname->name && $name->language == $sname->language ){
									$matched = true;
									$count++;
									break;
								}
							}
							if (!$matched){
								$missingDefaults[] = array("catid"=>$catid, "lang_code"=>$name->language, "name"=>$name);
							}
						}
					}
				}
			*/
		}

		if (count($missingDefaults) > 0)
		{
			$query = $db->getQuery(true);
			$query->insert("#__jev_defaults")->columns("title, name, subject,value,state,params,language, catid");

			foreach ($missingDefaults as $md)
			{
				$values = array($db->quote($md["name"]->title), $db->quote($md["name"]->name), $db->quote($md["name"]->subject), $db->quote($md["name"]->value), 0, $db->quote($md["name"]->params), $db->quote($md["lang_code"]), $md["catid"]);
				$query->values(implode(",", $values));
			}

			$db->setQuery($query);

			try
			{
				$db->execute();
			} catch (Exception $e) {
				echo $e;
			}
		}

	}

function edit($key = null, $urlVar = null)
	{

		// get the view
		$this->view = $this->getView("defaults", "html");

		// Set the layout
		$this->view->setLayout('edit');

		// Get/Create the model
		if ($model = $this->getModel("default", "defaultsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$this->view->display();
		//$this->view->edit();

	}

	function cancel($key = null)
	{

		$this->setRedirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.overview", false));
		$this->redirect();
	}

	function unpublish()
	{

		$input = Factory::getApplication()->input;

		$db  = Factory::getDbo();
		$cid = $input->get("cid", array(), "array");
		if (count($cid) < 1)
		{
			$this->setRedirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.overview", false));
			$this->redirect();

			return;
		}
        $cid = ArrayHelper::toInteger($cid);
        $cid = implode(",", $cid);

		$sql  = "UPDATE #__jev_defaults SET state=0 where id IN (" . $cid . ")";
		$db->setQuery($sql);
		$db->execute();

		$this->setRedirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.overview", false));
		$this->redirect();
	}

    function deletemodulelayout()
    {

        $input = Factory::getApplication()->input;

        $db  = Factory::getDbo();
        $mid = $input->getInt("mid", 0);
        if ($mid < 1)
        {
            $this->setRedirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.overview", false));
            $this->redirect();
            return;
        }

        $sql  = "DELETE FROM  #__jev_defaults where name LIKE 'module.%.$mid'";
        $db->setQuery($sql);
        $db->execute();

        $this->setRedirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.overview", false));
        $this->redirect();
    }

	function publish()
	{

		$db     = Factory::getDbo();
		$input = Factory::getApplication()->input;

		$cid = $input->get("cid", array(), "array");
		if (count($cid) < 1)
		{
			$this->setRedirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.overview", false));
			$this->redirect();

			return;
		}
        $cid = ArrayHelper::toInteger($cid);
        $cid = implode(",", $cid);

		// Check if the layout is the same as the default value - if it is then do NOT publish it
		$sql = "SELECT * FROM #__jev_defaults where  id IN (" . $cid . ")";
		$db->setQuery($sql);
		$values = $db->loadObjectList();

        $newcid = array();
        foreach ( $values as $value)
        {

            $defaultvalue  = "";
            $componentname = explode( ".", $value->name, 2 );
            $componentname = $componentname[0];

            if ( $defaultvalue == "" && file_exists( JPATH_ADMINISTRATOR . '/components/' . $componentname . '/views/defaults/tmpl/' . $value->name . ".3.html" ) )
            {
                $defaultvalue = file_get_contents( JPATH_ADMINISTRATOR . '/components/' . $componentname . '/views/defaults/tmpl/' . $value->name . ".3.html" );
            }

            if ( $defaultvalue == "" && file_exists( JPATH_ADMINISTRATOR . '/components/' . $componentname . '/views/defaults/tmpl/' . $value->name . ".html" ) )
            {
                $defaultvalue = file_get_contents( JPATH_ADMINISTRATOR . '/components/' . $componentname . '/views/defaults/tmpl/' . $value->name . ".html" );
            }

            if ( str_replace( " ", "", $defaultvalue ) == str_replace( " ", "", $value->value ) || $value->value == "" )
            {
                Factory::getApplication()->enqueueMessage( Text::_( "JEV_LAYOUT_IS_DEFAULT_NOT_PUBLISHED", "WARNING" ) );
                continue;
            }

            $newcid[] = $value->id;
        }

        if (count($newcid) > 0)
        {
            $cid = ArrayHelper::toInteger( $newcid );
            $cid = implode( ",", $cid );

            $sql = "UPDATE #__jev_defaults SET state=1 where id IN (" . $cid . ")";
            $db->setQuery( $sql );
            $db->execute();
        }
		$this->setRedirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.overview", false));
		$this->redirect();
	}

	/**
	 * Saves the Session Record
	 */
	function save($key = null, $urlVar = null)
	{

		$input = Factory::getApplication()->input;

		$id = $input->getInt("id", 0);
		if ($id > 0)
		{

			// Get/Create the model
			if ($model = $this->getModel("default", "defaultsModel"))
			{
				//TODO find a work around for getting post array with input.
				$data = $input->getArray(array(), null, 'RAW');
				if ($model->store($data))
				{
					if ($input->getCmd("task") == "defaults.apply")
					{
						$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.edit&id=$id", Text::_("JEV_TEMPLATE_SAVED"));
						$this->redirect();

						return;
					}
					$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=defaults.overview", Text::_("JEV_TEMPLATE_SAVED"));
					$this->redirect();

					return;
				}
				else
				{
					echo "<script> alert('" . $model->getErrorMessage() . "'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}


	}
}
