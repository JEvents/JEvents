<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: commonfunctions.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// functions common to component and modules
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\String\StringHelper;

// Joomla 1.5
// tasker/controller
jimport('joomla.application.component.controller');

class JEV_CommonFunctions
{

	public static function loadJEventsViewLang()
	{

		$jEventsView = JEV_CommonFunctions::getJEventsViewName();
		$lang        = Factory::getLanguage();
		$lang->load(JEV_COM_COMPONENT . "_" . $jEventsView);
		$lang->load("files_jevents" . $jEventsView . "layout");
	}

	public static function getJEventsViewName()
	{

		static $jEventsView;

		if (!isset($jEventsView))
		{
			$cfg    = JEVConfig::getInstance();
			$input = Factory::getApplication()->input;
			// priority of view setting is url, cookie, config,
			$jEventsView = $cfg->get('com_calViewName', "flat");
			$jEventsView = $input->cookie->getString("jevents_view", $jEventsView, null);
			$jEventsView = $input->getString("jEV", $jEventsView);
			// security check
			if (!in_array($jEventsView, JEV_CommonFunctions::getJEventsViewList()))
			{
				$jEventsView = "flat";
			}
		}

		return $jEventsView;
	}

	public static function getJEventsViewList($viewtype = null)
	{

		$jEventsViews = array();
		switch ($viewtype)
		{
			case  "mod_jevents_latest" :
			case  "mod_jevents_cal" :
				$handler = opendir(JPATH_SITE . "/modules/$viewtype/tmpl/");
				while ($file = readdir($handler))
				{
					if ($file != '.' && $file != '..' && $file != '.svn')
					{
						if (is_dir(JPATH_SITE . "/modules/$viewtype/tmpl/" . $file))
						{
							$jEventsViews[] = $file;
						}
					}
				}
				break;
			default :
				$handler = opendir(JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/");
				while ($file = readdir($handler))
				{
					if ($file != '.' && $file != '..' && $file != '.svn')
					{
						if (is_dir(JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $file) && (
								file_exists(JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $file . "/month") ||
								file_exists(JPATH_SITE . "/components/" . JEV_COM_COMPONENT . "/views/" . $file . "/config.xml")
							))
							$jEventsViews[] = $file;
					}
				}
		}

		return $jEventsViews;
	}

	public static function setColours($row)
	{

		$cfg = JEVConfig::getInstance();
		if (!$cfg->get("multicategory", 0))
		{
			return array(JEV_CommonFunctions::setColor($row));
		}

		static $catData;
		if (!isset($catData)) $catData = JEV_CommonFunctions::getCategoryData();

		$colors = array();

		foreach ($row->catids() as $catid)
		{
			if (is_object($row) && strtolower(get_class($row)) != "stdclass")
			{
				if ($cfg->get('com_calForceCatColorEventForm', 2) == '2')
				{
					$colors[] = ($catid > 0 && isset($catData[$catid])) ? $catData[$catid]->color : '#333333';
				}
				else $colors[] = $row->useCatColor() ? ($catid > 0 && isset($catData[$catid])) ? $catData[$catid]->color : '#333333' : $row->color_bar();
			}
			else
			{
				if ($cfg->get('com_calForceCatColorEventForm', 2) == '2')
				{
					$colors[] = ($row->catid > 0 && isset($catData[$catid])) ? $catData[$row->catid]->color : '#333333';
				}
				else $colors[] = $row->useCatColor ? ($row->catid > 0 && isset($catData[$catid])) ? $catData[$row->catid]->color : '#333333' : $row->color_bar;
			}
		}

		return $colors;
	}

	public static function setColor($row)
	{

		$cfg = JEVConfig::getInstance();

		static $catData;
		if (!isset($catData)) $catData = JEV_CommonFunctions::getCategoryData();

		if (is_object($row) && strtolower(get_class($row)) != "stdclass")
		{
			if ($cfg->get('com_calForceCatColorEventForm', 2) == '2')
			{
				$color = ($row->catid() > 0 && isset($catData[$row->catid()])) ? $catData[$row->catid()]->color : '#333333';
			}
			else $color = $row->useCatColor() ? (($row->catid() > 0 && isset($catData[$row->catid()])) ? $catData[$row->catid()]->color : '#333333') : $row->color_bar();

		}
		else
		{
			if ($cfg->get('com_calForceCatColorEventForm', 2) == '2')
			{
				$color = ($row->catid > 0 && isset($catData[$row->catid()])) ? $catData[$row->catid]->color : '#333333';
			}
			else $color = $row->useCatColor ? ($row->catid > 0 && isset($catData[$row->catid()])) ? $catData[$row->catid]->color : '#333333' : $row->color_bar;

		}

		if ($color == "")
		{
			$color = "#ccc";
		}

		//$color = $row->useCatColor ? ( $row->catid > 0 ) ? $catData[$row->catid]->color : '#333333' : $row->color_bar;
		return $color;
	}

	/**
	 * get all events_categories to use category color
	 * @return  object
	 */
	public static function getCategoryData()
	{

		static $cats;

		$app    = Factory::getApplication();

		if (!isset($cats))
		{
			$db = Factory::getDbo();

			$sql = "SELECT c.* FROM #__categories as c WHERE extension='" . JEV_COM_COMPONENT . "' order by c.lft asc";
			$db->setQuery($sql);
			$cats = $db->loadObjectList('id');
			foreach ($cats as &$cat)
			{
				$cat->name     = $cat->title;
				$params        = new JevRegistry($cat->params);
				$cat->color    = $params->get("catcolour", "");
				$cat->overlaps = $params->get("overlaps", 0);
			}
			unset ($cat);

			$app->triggerEvent('onGetCategoryData', array(& $cats));

		}

		$app->triggerEvent('onGetAccessibleCategories', array(& $cats, false));


		return $cats;
	}

	/**
	 * Cloaks html link whith javascript
	 *
	 * @param string $url     The cloaking URL
	 * @param string $text    The link text
	 * @param array  $attribs additional attributes
	 *
	 * @return string HTML
	 */
	public static function jEventsLinkCloaking($url = '', $text = '', $attribs = array())
	{

		static $linkCloaking;

		if (!isset($linkCloaking))
		{
			$cfg          = JEVConfig::getInstance();
			$linkCloaking = $cfg->get('com_linkcloaking', 0);
		}

		if (!is_array($attribs))
		{
			$attribs = array();
		}
		if ($linkCloaking)
		{
			$cloakattribs = array('onclick' => '"window.location.href=\'' . Route::_($url) . '\';return false;"');

			return JEV_CommonFunctions::jEventsDoLink("", $text, array_merge($cloakattribs, $attribs));
		}
		else
		{
			return JEV_CommonFunctions::jEventsDoLink(Route::_($url), "$text", $attribs);
		}
	}

	public static function jEventsDoLink($url = "", $alt = "alt", $attr = array())
	{

		if (StringHelper::strlen($url) == 0) $url = "javascript:void(0)";
		$link = "<a href='" . $url . "' ";
		if (count($attr) > 0)
		{
			foreach ($attr as $key => $val)
			{
				$link .= " $key=$val";
			}
		}
		$link .= ">$alt</a>";

		return $link;
	}


	/**
	 * Support all JevDate::strftime() parameter for Window systems
	 *
	 * @param string $format
	 * @param int    $timestamp
	 *
	 * @return string formated string
	 */
	public static function jev_strftime($format = '', $timestamp = null, $timezone = false, $datetime = false)
	{

		if (!$timestamp) $timestamp = time();

		// Replace names by own translation to get rid of improper os system library
		if (strpos($format, '%a') !== false)
			$format = str_replace('%a', JEVHelper::getShortDayName(date('w', $timestamp)), $format);
		if (strpos($format, '%A') !== false)
			$format = str_replace('%A', JEVHelper::getDayName(date('w', $timestamp)), $format);
		if (strpos($format, '%b') !== false)
			$format = str_replace('%b', JEVHelper::getShortMonthName(date('n', $timestamp)), $format);
		if (strpos($format, '%B') !== false)
			$format = str_replace('%B', JEVHelper::getMonthName(date('n', $timestamp)), $format);
		if (strpos($format, '%Z') !== false && $timezone)
		{
			$format = str_replace('%Z', $timezone->getName(), $format);
		}
		if (strpos($format, '%z') !== false && $timezone && $datetime)
		{
			$offset = $timezone->getOffset($datetime);
			$format = str_replace('%z', $offset/3600 , $format);
		}

		if (IS_WIN)
		{
			if (!class_exists('JEV_CompatWin'))
			{
				require_once(dirname(__FILE__) . '/compatwin.php');
			}

			return JEV_CompatWin::win_strftime($format, $timestamp);
		}
		else
		{
			return JevDate::strftime($format, $timestamp);
		}

	}


	/**
	 * Test to see if user is creator of the event or editor or above
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
	 *
	 * @return unknown
	 */
	public static function hasAdvancedRowPermissions($row, $user = null)
	{

		// TODO make this call a plugin
		if ($user == null)
		{
			$user = Factory::getUser();
		}

		// strictt publishing test
		if (JEVHelper::isEventEditor() || JEVHelper::isEventPublisher(true))
		{
			return true;
		}
		if (is_null($row))
		{
			return false;
		}
		else if ($row->created_by() == $user->id)
		{
			return true;
		}

		return false;
	}


	public static function notifyAuthorPublished($event)
	{


		JLoader::register('JEventsCategory', JEV_ADMINPATH . "/libraries/categoryClass.php");

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$app   = Factory::getApplication();

		$db  = Factory::getDbo();
		$cat = new JEventsCategory($db);
		$cat->load($event->catid());
		$adminuser = $cat->getAdminUser();

		$adminEmail = $adminuser->email;
		$adminName  = $adminuser->name;
		$config     = new JConfig();
		$sitename   = $config->sitename;

		$subject = Text::sprintf('JEV_NOTIFY_AUTHOR_SUBJECT', $sitename);

		$Itemid = JEVHelper::getItemid();
		// reload the event to get the reptition ids
		$evid = (int) $event->ev_id();

		$dataModel  = new JEventsDataModel("JEventsAdminDBModel");
		$queryModel = new JEventsDBModel($dataModel);

		$testevent = $queryModel->getEventById($evid, 1, "icaldb");

		// attach anonymous creator etc.
		PluginHelper::importPlugin('jevents');

		$app->triggerEvent('onDisplayCustomFields', array(&$event));

		$rp_id = $testevent->rp_id();

		list($year, $month, $day) = JEVHelper::getYMD();

		$uri = Uri::getInstance(Uri::base());
		if ($app->isClient('administrator'))
		{
			$root       = $uri->toString(array('scheme', 'host', 'port', 'path'));
			$root       = str_replace("/administrator", "", $root);
			$detaillink = '<a href="' . $root . 'index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.detail&rp_id=' . $evid . '&rp_id=' . $rp_id . '&Itemid=' . $Itemid . "&year=$year&month=$month&day=$day" . '">' . $event->title() . '</a>' . "\n";
		}
		else
		{
			$root       = $uri->toString(array('scheme', 'host', 'port'));
			$detaillink = '<a href="' . $root . Route::_('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.detail&rp_id=' . $evid . '&rp_id=' . $rp_id . '&Itemid=' . $Itemid . "&year=$year&month=$month&day=$day") . '">' . $event->title() . '</a>' . "\n";
		}

		$content = sprintf(Text::_('JEV_NOTIFY_AUTHOR_Message'), $detaillink, $sitename);

		$authorname  = "";
		$authoremail = "";
		if ($event->created_by() > 0)
		{
			$author = JEVHelper::getUser($event->created_by());
			if (!$author) return;
			$authorname  = $author->name;
			$authoremail = $author->email;
		}
		else if (isset($event->authoremail) && $event->authoremail != "")
		{
			$authorname  = $event->authorname;
			$authoremail = $event->authoremail;
		}
		if ($authoremail == "") return;

		// mail function
		$mail          = Factory::getMailer();
		$sender_config = $params->get('sender_config', 9);

		try
		{
			if ($sender_config == 0)
			{

				$mail->setSender(array(0 => $adminEmail, 1 => $adminName));

			}
			elseif ($sender_config == 1)
			{

				$mail->setSender(array(0 => $config->mailfrom, 1 => $config->fromname));

			}
			else
			{
				$mail->setSender(array(0 => $params->get('sender_email', ''), 1 => $params->get('sender_name', '')));
			}

			if ($params->get('email_replyto', 0) == 1)
			{
				$mail->addReplyTo($adminEmail);
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage("JEV_UNABLE_TO_SET_EMAIL_SENDER_OR_REPLY_TO", 'warning');
		}

		$mail->addRecipient($authoremail);
		$mail->setSubject($subject);
		$mail->setBody($content);
		$mail->IsHTML(true);
		try
		{
			$mail->send();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage("JERROR_SENDING_EMAIL", 'warning');
		}
	}

	public static function sendAdminMail($adminName, $adminEmail, $subject = '', $title = '', $content = '', $day = '', $month = '', $year = '', $start_time = '', $end_time = '', $author = '', $live_site = "", $modifylink = "", $viewlink = "", $event = false, $cc = "")
	{

		$config = new JConfig();

		$app    = Factory::getApplication();

		if (!$adminEmail) return;

		$recipient = $adminEmail;
		
		if ((strpos($adminEmail, '@example.com') !== false)) return;

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		if ((int) $params->get("com_notifyboth", 0) == 3)
		{
			return; // no notifications
		}

		$messagetemplate = $params->get("notifymessage", Text::_('JEV_DEFAULT_NOTIFYMESSAGE'));

		if (strpos($messagetemplate, "JEV_DEFAULT_NOTIFYMESSAGE") !== false || trim(strip_tags($messagetemplate)) == "")
		{
			$messagetemplate = sprintf(Text::_('JEV_EMAIL_EVENT_TITLE'), "{TITLE}") . "<br/><br/>\n";
			$messagetemplate .= "{DESCRIPTION}<br/><br/>\n";
			$messagetemplate .= sprintf(Text::_('JEV_MAIL_TO_ADMIN'), "{LIVESITE}", "{AUTHOR}") . "<br/>\n";
			$messagetemplate .= sprintf(Text::_('JEV_EMAIL_VIEW_EVENT'), "{VIEWLINK}") . "<br/>\n";
			$messagetemplate .= sprintf(Text::_('JEV_EMAIL_EDIT_EVENT'), "{EDITLINK}") . "<br/>\n";
			$messagetemplate .= sprintf(Text::_('JEV_MANAGE_EVENTS'), "{MANAGEEVENTS}") . "<br/>";
		}

		$uri       = Uri::getInstance(Uri::base());
		$root      = $uri->toString(array('scheme', 'host', 'port'));
		$adminLink = $root . Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=admin.listevents&Itemid=" . JEVHelper::getAdminItemid());

		$messagetemplate = str_replace("{TITLE}", $title, $messagetemplate);
		$messagetemplate = str_replace("{DESCRIPTION}", $content, $messagetemplate);
		if ($event)
		{
			$messagetemplate = str_replace("{CATEGORY}", $event->catname(), $messagetemplate);
			//$messagetemplate = str_replace("{EXTRA}", $event->extra_info(),$messagetemplate);

		}
		$messagetemplate = str_replace("{LIVESITE}", $live_site, $messagetemplate);
		$messagetemplate = str_replace("{AUTHOR}", $author, $messagetemplate);
		$messagetemplate = str_replace("{DAY}", $day, $messagetemplate);
		$messagetemplate = str_replace("{MONTH}", $month, $messagetemplate);
		$messagetemplate = str_replace("{YEAR}", $year, $messagetemplate);
		$messagetemplate = str_replace("{STARTTIME}", $start_time, $messagetemplate);
		$messagetemplate = str_replace("{ENDTIME}", $end_time, $messagetemplate);
		$messagetemplate = str_replace("{VIEWLINK}", $viewlink, $messagetemplate);
		$messagetemplate = str_replace("{EDITLINK}", $modifylink, $messagetemplate);
		$messagetemplate = str_replace("{MANAGEEVENTS}", $adminLink, $messagetemplate);

		// mail function
		// JEvents category admin only or both get notifications
		if ($params->get("com_notifyboth", 0) == 0 || $params->get("com_notifyboth", 0) == 1)
		{
			if ($params->get("com_notifyboth", 0) == 1)
			{
				$jevadminuser = new  User($params->get("jevadmin", 62));
				if ($jevadminuser->email != $adminEmail)
				{
					$add_cc = $jevadminuser->email;
				}
				else
				{
					$recipient = $adminEmail;
				}
			}
		}
		// Just JEvents admin user
		else if ($params->get("com_notifyboth", 0) == 2)
		{
			$jevadminuser = new  User($params->get("jevadmin", 62));
			if ($jevadminuser->email != $adminEmail)
			{
				$recipient = $jevadminuser->email;
			}
			else
			{
				$recipient = $adminEmail;
			}
			//Check not emailing the same user who is editing:
			$user = Factory::getUser();
			if ($user->email === $adminEmail)
			{
				// Get out of here we don't want to notify the admin of their own change
				return;
			}
		}

		$mail          = Factory::getMailer();
		$sender_config = $params->get('sender_config', 0);
		try
		{
			if ($sender_config == 0)
			{

				$mail->setSender(array(0 => $adminEmail, 1 => $adminName));

			}
			elseif ($sender_config == 1)
			{

				$mail->setSender(array(0 => $config->mailfrom != "" ? $config->mailfrom : $adminEmail, 1 => $config->fromname != "" ? $config->fromname : $adminName));

			}
			else
			{
				$mail->setSender(array(0 => $params->get('sender_email', $adminEmail), 1 => $params->get('sender_name', $adminName)));
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage("JEV_UNABLE_TO_SET_EMAIL_SENDER_OR_REPLY_TO", 'warning');
		}

		// attach anonymous creator etc.
		PluginHelper::importPlugin('jevents');
		try
		{
			$app->triggerEvent('onDisplayCustomFields', array(&$event));
		}
		catch (Exception $e)
		{
			// Ignored sometimes e.g. if target menu item blocks display of the event!
		}

		try
		{
			if (!isset($event->authoremail) && $params->get('email_replyto', 0) == 1)
			{
				$mail->addReplyTo($adminEmail);
			}
			else if (isset($event->authoremail) && $event->authoremail !== '')
			{
				$mail->addReplyTo($event->authoremail);
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage("JEV_UNABLE_TO_SET_EMAIL_SENDER_OR_REPLY_TO", 'warning');
		}

		if (isset($add_cc) && $add_cc !== "")
		{
			$mail->addCc($add_cc);
		}

		$mail->addRecipient($recipient);


		//Leave old replacements in place for now, and now run through default loadedfromtemplate
		// if there is an event!
		if ($event)
		{
			include_once(JEV_PATH . "/views/default/helpers/defaultloadedfromtemplate.php");
			ob_start();
			DefaultLoadedFromTemplate(false, false, $event, 0, $messagetemplate);
			$messagetemplate = ob_get_clean();

			// Process the subject too
            ob_start();
            DefaultLoadedFromTemplate(false, false, $event, 0, $subject);
            $subject = ob_get_clean();
		}

		$mail->setSubject($subject);
		$mail->setBody($messagetemplate);

		if ($event)
		{
			PluginHelper::importPlugin("jevents");
			$res = $app->triggerEvent('onSendAdminMail', array(&$mail, $event));
		}

		if ($cc != "")
		{
			$mail->addCC($cc);
		}
		$mail->IsHTML(true);
		try
		{
			$mail->send();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage("JERROR_SENDING_EMAIL", 'warning');
		}

	}

}

