<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icals.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd,2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

#[\AllowDynamicProperties]
class AdminIcalsController extends Joomla\CMS\MVC\Controller\AdminController
{

	var $_debug = false;
	var $queryModel = null;
	var $dataModel = null;

	/**
	 * Controler for the Ical Functions
	 *
	 * @param array        configuration
	 */
	function __construct($config = array())
	{

		parent::__construct($config);
		$this->registerTask('list', 'overview');
		$this->registerTask('new', 'newical');
		$this->registerTask('reload', 'save');
		$this->registerDefaultTask("overview");
		// Need to force this because of Joomla 3.10 changes
		$this->registerTask('unpublish', 'unpublish');

		$cfg          = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		$this->dataModel  = new JEventsDataModel("JEventsAdminDBModel");
		$this->queryModel = new JEventsDBModel($this->dataModel);

	}

	/**
	 * List Icals
	 *
	 */
	function overview()
	{
		// Get the view
		$this->view = $this->getView("icals", "html");

		$this->_checkValidCategories();

		// Get/Create the model
		if ($model = $this->getModel("ical", "jeventsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);

			$rows = $model->getItems();
			$total = $model->getTotal();

		}
		else
		{
			$rows = array();
			$total = 0;
		}
		$app    = Factory::getApplication();

		$option = JEV_COM_COMPONENT;
		$catid      = intval($app->getUserStateFromRequest("catid{$option}", 'catid', 0));
		$limit      = intval($app->getUserStateFromRequest("viewlistlimit", 'limit', $app->get('list_limit', 10)));
		$limitstart = intval($app->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));

		// get list of categories
		$attribs = 'class="gsl-select" size="1" onchange="document.adminForm.submit();"';
		$clist   = JEventsHTML::buildCategorySelect($catid, $attribs, null, true, false, 0, 'filter[catid]');

		$filters = array();
		$filters['catid'] = $clist;

		jimport('joomla.html.pagination');
		$pagination = new \Joomla\CMS\Pagination\Pagination($total, $limitstart, $limit);

		// Set the layout
		$this->view->setLayout('overview');
		$this->view->rows    = $rows;
		$this->view->pagination = $pagination;
		$this->view->filters = $filters;

		$this->view->display();
	}

	function _checkValidCategories()
	{

		// TODO switch this after migration
		$component_name = "com_jevents";

		$db    = Factory::getDbo();
		$query = "SELECT COUNT(*) AS count FROM #__categories WHERE extension = '$component_name' AND `published` = 1;";  // RSH 9/28/10 added check for valid published, J!1.6 sets deleted categoris to published = -2
		$db->setQuery($query);
		$count = intval($db->loadResult());
		if ($count <= 0)
		{
			// RSH 9/28/10 - Added check for J!1.6 to use different URL for reroute
			$redirectURL = "index.php?option=com_categories&extension=" . JEV_COM_COMPONENT;
			$this->setRedirect($redirectURL, "You must first create at least one category");
			$this->redirect();
		}
	}

	function edit($key = null, $urlVar = null)
	{

		if (!JEVHelper::isAdminUser())
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel", "Not Authorised - must be super admin");
			$this->redirect();

			return;
		}

		$app    = Factory::getApplication();
		$input  = $app->input;

		// get the view
		$this->view = $this->getView("icals", "html");

		$cid = $input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);
		if (is_array($cid) && count($cid) > 0) $editItem = $cid[0];
		else $editItem = 0;

		$item = new stdClass();
		if ($editItem != null)
		{
			$db    = Factory::getDbo();
			$query = "SELECT * FROM #__jevents_icsfile as ics where ics.ics_id=$editItem";

			$db->setQuery($query);
			$item = null;
			$item = $db->loadObject();
		}


		// Set the layout
		$this->view->setLayout('edit');

		// for Admin interface only

		$this->view->with_unpublished_cat = $app->isClient('administrator');
		$this->view->editItem   = $item;

		$this->view->display();

	}

	function reloadall()
	{

		@set_time_limit(1800);

		$app    = Factory::getApplication();
		$input  = $app->input;

		if ($app->isClient('administrator'))
		{
			$redirect_task = "icals.list";
		}
		else
		{
			$redirect_task = "day.listevents";

			// Cannot reload all in the frontend if import key is required
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("icalkeyimport", 0))
			{
				$app->enqueueMessage('Fatal Error - You can only load these calendars one at a time with the appropriate security key set', 'error');

				$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task", 'Fatal Error - You can only load these calendars one at a time with the appropriate security key set');
				$this->redirect();
			}
		}

		$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf";
		$db    = Factory::getDbo();
		$db->setQuery($query);
		$allICS = $db->loadObjectList();

		foreach ($allICS as $currentICS)
		{
			//only update cals from url
			if ($currentICS->icaltype == '0' && $currentICS->autorefresh == 1)
			{
				$input->set('icsid', $currentICS->ics_id);
				$this->save();
			}
		}

		$user  = Factory::getUser();
		$guest = (int) $user->get('guest');

		$link    = "index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task";
		$message = Text::_('ICS_ALL_FILES_IMPORTED');

		if ($guest === 1)
		{
			$this->setRedirect($link);
		}
		else
		{
			$this->setRedirect($link, $message);
		}

		$this->redirect();
	}

	function save($key = null, $urlVar = null)
	{

		$app    = Factory::getApplication();
		$input = $app->input;

		$icsFile = false;
		// Check for request forgeries
		if ($input->get("task") !== "icals.reload" && $input->get("task") !== "icals.reloadall")
		{
			Session::checkToken() or jexit('Invalid Token');
		}


		$user  = Factory::getUser();
		$guest = (int) $user->get('guest');

		$authorised = false;

		if ($app->isClient('administrator'))
		{
			$redirect_task = "icals.list";
		}
		else
		{
			$redirect_task = "day.listevents";
		}

		// clean this up later - this is a quick fix for frontend reloading
		$autorefresh = 0;
		$icsid       = $input->getInt('icsid', 0);

		if ($icsid > 0)
		{
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("icalkeyimport", 0))
			{
				$icalkey = $params->get("icalkey", "secret phrase");
				$icalkey = md5($icsid . "something really stupid" . $icalkey);

				$k = $input->getString("k", "");

				if ($k !== $icalkey)
				{
					throw new Exception( Text::_('ALERTNOTAUTH'), 403);
					return false;
				}
			}

			$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf WHERE ics_id=$icsid";
			$db    = Factory::getDbo();
			$db->setQuery($query);
			$currentICS = $db->loadObjectList();

			if (count($currentICS) > 0)
			{
				$currentICS = $currentICS[0];
				if ($currentICS->autorefresh)
				{
					$authorised  = true;
					$autorefresh = 1;
				}
			}
		}

		if (!($authorised || JEVHelper::isAdminUser($user))) {
			throw new Exception( Text::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$cid = $input->input->get('cid', array(0), 'array');
		$cid    = ArrayHelper::toInteger($cid);

		if (is_array($cid) && count($cid) > 0)
		{
			$cid = $cid[0];
		}
		else
		{
			$cid = 0;
		}

		$db = Factory::getDbo();

		// include ical files

		if ($icsid > 0 || $cid != 0)
		{
			$icsid = ($icsid > 0) ? $icsid : $cid;
			$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf WHERE ics_id=$icsid";
			$db->setQuery($query);
			$currentICS = $db->loadObjectList();
			if (count($currentICS) > 0)
			{
				$currentICS = $currentICS[0];
				if ($currentICS->autorefresh)
				{
					$authorised  = true;
					$autorefresh = 1;
				}

			}
			else
			{
				$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task", "Invalid Ical Details");
				$this->redirect();
			}

			$catid = $input->getInt('catid', $currentICS->catid);

			if ($catid <= 0 && $currentICS->catid > 0)
			{
				$catid = intval($currentICS->catid);
			}
			$access = intval($input->getCmd('access', $currentICS->access));
			if ($access < 0 && $currentICS->access >= 0)
			{
				$access = intval($currentICS->access);
			}
			$icsLabel = $input->get('icsLabel', $currentICS->label);
			if (($icsLabel == "" || $input->getCmd("task") == "icals.reload") && StringHelper::strlen($currentICS->label) >= 0)
			{
				$icsLabel = $currentICS->label;
			}
			$isdefault      = $input->getInt('isdefault', $currentICS->isdefault);
			$overlaps       = $input->getInt('overlaps', $currentICS->overlaps);
			$autorefresh    = $input->getInt('autorefresh', $autorefresh);
			$ignoreembedcat = $input->getInt('ignoreembedcat', $currentICS->ignoreembedcat);
			$createnewcategories = $input->getInt('createnewcategories', $currentICS->createnewcategories);

			// This is a native ical - so we are only updating identifiers etc
			if ($currentICS->icaltype == 2)
			{
				$ics = new iCalICSFile($db);
				$ics->load($icsid);
				$ics->catid     = $catid;
				$ics->isdefault = $isdefault;
				$ics->overlaps  = $overlaps;
				$ics->access    = $access;
				$ics->label     = $icsLabel;
				// TODO update access and state
				$ics->updateDetails();
				$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task", Text::_('ICS_SAVED'));
				$this->redirect();
			}

			$state = 1;
			if (StringHelper::strlen($currentICS->srcURL) == 0)
			{
				echo "Can only reload URL based subscriptions";

				return;
			}
			$uploadURL = $currentICS->srcURL;

		}
		else
		{

			$catid          = $input->getInt('catid', 0);
			$ignoreembedcat = $input->getInt('ignoreembedcat', 0);
			$createnewcategories = $input->getInt('createnewcategories', 1);
			// Should come from the form or existing item
			$access    = $input->getInt('access', 0);
			$state     = 1;
			$uploadURL = $input->getString('uploadURL', '');
			$icsLabel  = $input->getString('icsLabel', '');
            $autorefresh = $input->getInt('autorefresh', 0);

        }
		if ($catid == 0)
		{
			// Paranoia, should not be here, validation is done by java script
			$app->enqueueMessage('Fatal Error - ' . Text::_('JEV_E_WARNCAT'), 'error');

			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task", Text::_('JEV_E_WARNCAT'));
			$this->redirect();

			return;
		}

		// I need a better check and expiry information etc.
		if (StringHelper::strlen($uploadURL) > 0)
		{
            try
            {
                $icsFile = iCalICSFile::newICSFileFromURL( $uploadURL, $icsid, $catid, $access, $state, $icsLabel, $autorefresh, $ignoreembedcat, $createnewcategories );
            }
            catch (Throwable $exception)
            {
                $app->enqueueMessage(Text::_('COM_JEVENTS_ICAL_UPDATE_FAILED'), 'warning');
                $icsFile = false;
            }
		}
		else if (isset($_FILES['upload']) && is_array($_FILES['upload']))
		{
			$file = $_FILES['upload'];
			if ($file['size'] == 0)
			{//|| !($file['type']=="text/calendar" || $file['type']=="application/octet-stream")){
				$app->enqueueMessage(Text::_('JEV_EMPTY_FILE_UPLOAD'), 'warning');
				$icsFile = false;
			}
			else
			{
				$icsFile = iCalICSFile::newICSFileFromFile($file, $icsid, $catid, $access, $state, $icsLabel);
			}
		}

		$message = '';
		if ($icsFile !== false)
		{
			// preserve ownership
			if (isset($currentICS) && $currentICS->created_by > 0)
			{
				$icsFile->created_by = $currentICS->created_by;
			}
			else
			{
				$icsFile->created_by = $input->getInt("created_by", 0);
			}

            $icsFileid = $icsFile->store();
			$message   = Text::_('ICS_FILE_IMPORTED');
		}
        else if ($icsid > 0)
        {
            if ( isset( $currentICS ) && $currentICS->created_by > 0 )
            {
                $created_by = $currentICS->created_by;
                if ( $created_by > 0 )
                {
                    $params        = ComponentHelper::getParams( JEV_COM_COMPONENT );
                    $mail          = Factory::getMailer();
                    $sender_config = $params->get( 'sender_config', 9 );

                    $author = JEVHelper::getUser( $created_by );

                    if ( $author )
                    {
                        $authorname  = $author->name;
                        $authoremail = $author->email;

                        try
                        {
                            if ( $sender_config == 0 )
                            {

                                $mail->setSender( array( 0 => $authoremail, 1 => $authorname ) );

                            }
                            elseif ( $sender_config == 1 )
                            {

                                $mail->setSender( array( 0 => $config->mailfrom, 1 => $config->fromname ) );

                            }
                            else
                            {
                                $mail->setSender( array(
                                    0 => $params->get( 'sender_email', '' ),
                                    1 => $params->get( 'sender_name', '' )
                                ) );
                            }

                            $mail->addRecipient( $authoremail );
                            $mail->setSubject( Text::_( 'COM_JEVENTS_ICAL_UPDATE_FAILED' ) );
                            $mail->setBody( Text:: sprintf( 'COM_JEVENTS_ICAL_UPDATE_FAILED_DETAIL', $currentICS->label) . "<br>" . Uri::root());
                            $mail->IsHTML( true );
                            try
                            {
                                $mail->send();
                            }
                            catch ( Exception $e )
                            {
                                $app->enqueueMessage( "JERROR_SENDING_EMAIL", 'warning' );
                            }

                        }
                        catch ( Exception $e )
                        {
                            // $app->enqueueMessage("JEV_UNABLE_TO_SET_EMAIL_SENDER_OR_REPLY_TO", 'warning');
                        }
                    }
                }
            }
        }

        if ($input->getCmd("task") !== "icals.reloadall")
		{
			$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task";

			if ($guest === 1)
			{
				$this->setRedirect($link);
			}
			else
			{
				$this->setRedirect($link, $message);
			}
			$this->redirect();
		}
	}

	/**
	 * This just updates the details not the content of the calendar
	 *
	 */
	function savedetails()
	{

		$user       = Factory::getUser();
		$authorised = false;

		$app    = Factory::getApplication();
		$input  = $app->input;

		// Check for request forgeries
		Session::checkToken() or jexit('Invalid Token');

		if ($app->isClient('administrator'))
		{
			$redirect_task = "icals.list";
		}
		else
		{
			$redirect_task = "month.calendar";
		}

		if (!($authorised || JEVHelper::isAdminUser($user)))
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task", "Not Authorised - must be super admin");
			$this->redirect();

			return;
		}

		$icsid = $input->getInt('icsid', 0);
		$cid   = $input->get('cid', array(0), 'array');
		$cid   = ArrayHelper::toInteger($cid);

        if (is_array($cid) && count($cid) > 0)
		{
			$cid = $cid[0];
		}
		else
		{
			$cid = 0;
		}

		$db = Factory::getDbo();

		if ($icsid > 0 || $cid != 0)
		{
			$icsid = ($icsid > 0) ? $icsid : $cid;
			$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf WHERE ics_id=$icsid";
			$db->setQuery($query);
			$currentICS = $db->loadObjectList();
			if (count($currentICS) > 0)
			{
				$currentICS = $currentICS[0];
			}
			else
			{
				$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task", "Invalid Ical Details");
				$this->redirect();
			}

			$catid = $input->getInt('catid', $currentICS->catid);
			if ($catid <= 0 && $currentICS->catid > 0)
			{
				$catid = (int) $currentICS->catid;
			}
			$access = intval($input->getInt('access', $currentICS->access));
			if ($access < 0 && $currentICS->access >= 0)
			{
				$access = intval($currentICS->access);
			}
			$state = (int) $input->getInt('state', $currentICS->state);
			if ($state < 0 && $currentICS->state >= 0)
			{
				$state = intval($currentICS->state);
			}
			$icsLabel = $input->getString('icsLabel', $currentICS->label);
			if ($icsLabel == "" && StringHelper::strlen($currentICS->icsLabel) >= 0)
			{
				$icsLabel = $currentICS->icsLabel;
			}
			$uploadURL = $input->getString('uploadURL', $currentICS->srcURL);
			if ($uploadURL == "" && StringHelper::strlen($currentICS->srcURL) >= 0)
			{
				$uploadURL = $currentICS->srcURL;
			}
			$isdefault   = $input->getInt('isdefault', $currentICS->isdefault);
			$overlaps    = $input->getInt('overlaps', $currentICS->overlaps);
			$autorefesh  = $input->getInt('autorefresh', $currentICS->autorefresh);
			$ignoreembed = $input->getInt('ignoreembedcat', $currentICS->ignoreembedcat);
			$createnewcategories = $input->getInt('createnewcategories', $currentICS->createnewcategories);

			// We are only updating identifiers etc
			$ics = new iCalICSFile($db);
			$ics->load($icsid);
			$ics->catid          = $catid;
			$ics->isdefault      = $isdefault;
			$ics->overlaps       = $overlaps;
			$ics->created_by     = $input->getInt("created_by", $currentICS->created_by);
			$ics->state          = $state;
			$ics->access         = $access;
			$ics->label          = $icsLabel;
			$ics->srcURL         = $uploadURL;
			$ics->ignoreembedcat = $ignoreembed;
			$ics->createnewcategories = $createnewcategories;
			$ics->autorefresh    = $autorefesh;
			// TODO update access and state
			$ics->updateDetails();
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task", Text::_('ICS_SAVED'));
			$this->redirect();
		}
	}

	function publish()
	{
		$input  = Factory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleICalPublish($cid, 1);
	}

	function toggleICalPublish($cid, $newstate)
	{

		$user = Factory::getUser();
		if (!JEVHelper::isAdminUser($user))
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel", "Not Authorised - must be super admin");
			$this->redirect();

			return;
		}

		$db = Factory::getDbo();
		foreach ($cid as $id)
		{
			$sql = "UPDATE #__jevents_icsfile SET state=$newstate where ics_id='" . $id . "'";
			$db->setQuery($sql);
			$db->execute();
		}
		$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list", Text::_('JEV_ADMIN_ICALSUPDATED'));
		$this->redirect();
	}

	function unpublish()
	{

		$input  = Factory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleICalPublish($cid, 0);
	}

	function autorefresh()
	{

		$input = Factory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleAutorefresh($cid, 1);
	}

	function toggleAutorefresh($cid, $newstate)
	{

		$user = Factory::getUser();
		if (!JEVHelper::isAdminUser($user))
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel", "Not Authorised - must be super admin");
			$this->redirect();

			return;
		}

		$db = Factory::getDbo();
		foreach ($cid as $id)
		{
			$sql = "UPDATE #__jevents_icsfile SET autorefresh=$newstate where ics_id='" . $id . "'";
			$db->setQuery($sql);
			$db->execute();
		}
		$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list", Text::_('JEV_ADMIN_ICALSUPDATED'));
		$this->redirect();
	}

	function noautorefresh()
	{

		$input = Factory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleAutorefresh($cid, 0);
	}

	function isdefault()
	{

		$input = Factory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleDefault($cid, 1);
	}

	function toggleDefault($cid, $newstate)
	{

		$user = Factory::getUser();
		if (!JEVHelper::isAdminUser($user))
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel", "Not Authorised - must be super admin");
			$this->redirect();

			return;
		}

		$db = Factory::getDbo();
		// set all to not default first
		$sql = "UPDATE #__jevents_icsfile SET isdefault=0";
		$db->setQuery($sql);
		$db->execute();

		$id  = $cid[0];
		$sql = "UPDATE #__jevents_icsfile SET isdefault=$newstate where ics_id='" . $id . "'";
		$db->setQuery($sql);
		$db->execute();
		$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list", Text::_('JEV_ADMIN_ICALSUPDATED'));
		$this->redirect();
	}

	function notdefault()
	{

		$input  = Factory::getApplication()->input;
		$cid = $input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleDefault($cid, 0);
	}

	/**
	 * create new ICAL from scratch
	 */
	function newical()
	{

		// Check for request forgeries
		Session::checkToken() or jexit('Invalid Token');

		$app  = Factory::getApplication();
		$input  = $app->input;
		// include ical files
		$catid = (int) $input->get('catid', array());
		// Should come from the form or existing item
		$access   = $input->getInt('access', 0);
		$state    = 1;
		$icsLabel = $input->getString('icsLabel', '');

		if ($catid == 0)
		{
			// Paranoia, should not be here, validation is done by java script
			$app->enqueueMessage('Fatal Error - ' . Text::_("JEV_E_WARNCAT"), 'error');

			// Set option variable.
			$option = JEV_COM_COMPONENT;
			$app->redirect('index.php?option=' . $option);

			return;
		}

		// Check for duplicates
		$db    = Factory::getDbo();
		$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf WHERE label=" . $db->quote($icsLabel);
		$db->setQuery($query);
		$existing = $db->loadObject();
		if ($existing)
		{
			$app->enqueueMessage(Text::_('JEV_DUPLICATE_CALENDAR'), 'error');

			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icals.edit");
			$this->redirect();

			return;

		}

		$icsid                      = 0;
		$icsFile                    = iCalICSFile::editICalendar($icsid, $catid, $access, $state, $icsLabel);
		$icsFile->created_by        = $input->getInt("created_by", 0);
		$icsFile->catid             = $catid;
		$icsFile->isdefault         = $input->getInt('isdefault', 0);
		$icsFile->overlaps          = $input->getInt('overlaps', 0);
		$icsFile->ignoreembedcat    = $input->getInt('ignoreembedcat', 0);
		$icsFile->access            = $access;
		$icsFile->label             = $icsLabel;

		$icsFile->store();

		$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list", Text::_('ICAL_FILE_CREATED'));
		$this->redirect();
	}

	function delete()
	{

		// Check for request forgeries
		Session::checkToken() or jexit('Invalid Token');

		$app    = Factory::getApplication();
		$input  = $app->input;

		$cid = $input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);

		$db = Factory::getDbo();

		// check this won't create orphan events
		$query = "SELECT ev_id FROM #__jevents_vevent WHERE icsid in (" . implode(",", $cid) . ")";
		$db->setQuery($query);
		$kids = $db->loadObjectList();
		if (count($kids) > 0)
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list", Text::_("DELETE_CREATES_ORPHAN_EVENTS"));
			$this->redirect();

			return;
		}

		$icsids = $this->_deleteICal($cid);
		$query  = "DELETE FROM #__jevents_icsfile WHERE ics_id IN ($icsids)";
		$db->setQuery($query);
		$db->execute();

		$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icals.list", "ICal deleted");
		$this->redirect();
	}

	function _deleteICal($cid)
	{

		$db     = Factory::getDbo();
		$icsids = implode(",", $cid);

		$query = "SELECT ev_id FROM #__jevents_vevent WHERE icsid IN ($icsids)";
		$db->setQuery($query);
		$veventids      = $db->loadColumn();
		$veventidstring = implode(",", $veventids);

		if ($veventidstring)
		{
			// TODO the ruccurences should take care of all of these??
			// This would fail if all recurrances have been 'adjusted'
			$query = "SELECT DISTINCT (eventdetail_id) FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
			$db->setQuery($query);
			$detailids      = $db->loadColumn();
			$detailidstring = implode(",", $detailids);

			$query = "DELETE FROM #__jevents_rrule WHERE eventid IN ($veventidstring)";
			$db->setQuery($query);
			$db->execute();

			$query = "DELETE FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
			$db->setQuery($query);
			$db->execute();

			if ($detailidstring)
			{
				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN ($detailidstring)";
				$db->setQuery($query);
				$db->execute();
			}
		}

		if ($icsids)
		{
			$query = "DELETE FROM #__jevents_vevent WHERE icsid IN ($icsids)";
			$db->setQuery($query);
			$db->execute();
		}

		return $icsids;
	}

}
