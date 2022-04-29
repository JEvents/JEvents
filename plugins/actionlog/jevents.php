<?php
/**
 * @package     Joomla.Plugins
 * @subpackage  System.actionlogs
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;

JLoader::register('ActionLogPlugin', JPATH_ADMINISTRATOR . '/components/com_actionlogs/libraries/actionlogplugin.php');
JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR . '/components/com_actionlogs/helpers/actionlogs.php');
JLoader::register('JevDate', JPATH_SITE . "/components/com_jevents/libraries/jevdate.php");


class PlgActionlogJEvents extends \Joomla\CMS\Plugin\CMSPlugin
{

	// Event State Triggers
	public function onPublishEvent($ids, $state) {

		$context    = Factory::getApplication()->input->get('option');

		$user       = Factory::getUser();
		$userId     = $user->id;

		$events = array();
		foreach ($ids as $id)
		{
			$dataModel  = new JEventsDataModel("JEventsAdminDBModel");
			$queryModel = new JEventsDBModel($dataModel);
			$events[]      = $queryModel->getEventById($id, 1, "icaldb");
		}

		if ((int) $state === 1)
		{
			$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_STATE_CHANGED_PUBLISHED';

		} else if ((int) $state === 0) {
			$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_STATE_CHANGED_UNPUBLISHED';

		} else if ((int)$state === -1){
			$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_STATE_CHANGED_TRASHED';
		} else {
			$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_STATE_CHANGED';
		}

		foreach ($events AS $event)
		{

			$ev_id      = $event->ev_id();
			$title      = $event->title();
			$startDate  = $event->publish_up();

			$action         = 'update';

			$message = array(
				'action'      => $action,
				'type'        => 'PLG_ACTIONLOG_JEVENTS_TYPE_EVENT',
				'id'          => $ev_id,
				'title'       => $title,
				'eventDate'   => $startDate,
				'itemlink'    => 'index.php?option=com_jevents&task=icalevent.edit&cid=' . $ev_id,
				'userid'      => $userId,
				'username'    => $user->username,
				'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $userId
			);

			$this->addLog(array($message), $messageLangKey, $context, $userId);
		}
	}

	public function onAfterStoreRepeatException(array $data) {

		$context    = Factory::getApplication()->input->get('option', 'com_jevents');

		$user       = Factory::getUser();
		$userId     = $user->id;

		$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_REPEAT_UPDATED';

		$rp_id      = $data['RP_ID'];
		$title      = $data['SUMMARY'];
		$eventDate  = (string) JevDate::getInstance($data['DTSTART']);
		$action = 'update';

		$message = array(
			'action'      => $action,
			'type'        => 'PLG_ACTIONLOG_JEVENTS_TYPE_EVENT_REPEAT',
			'id'          => $rp_id,
			'title'       => $title,
			'eventDate'   => $eventDate,
			'itemlink'    => 'index.php?option=com_jevents&task=icalrepeat.edit&cid=' . $rp_id,
			'userid'      => $userId,
			'username'    => $user->username,
			'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $userId
		);

		$this->addLog(array($message), $messageLangKey, $context, $userId);

	}

	public function onAfterSaveEvent($event, $dryrun ) {

		if ($dryrun)
		{
			return;
		}

		$context    = Factory::getApplication()->input->get('option');

		$ev_id      = $event->ev_id;
		$title      = $event->data['SUMMARY'];
		$eventDate  = (string) JevDate::getInstance($event->data['DTSTART']);
		$user       = Factory::getUser();
		$userId     = $user->id;
		$isNew      = $event->isNew;

		$action         = 'update';
		$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_UPDATED';

		if ($isNew) {
			$action         = 'add';
			$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_CREATED';
		}

		$message = array(
			'action'        => $action,
			'type'          => 'PLG_ACTIONLOG_JEVENTS_TYPE_EVENT',
			'id'            => $ev_id,
			'title'         => $title,
			'eventDate'     => (string) $eventDate,
			'itemlink'      => 'index.php?option=com_jevents&task=icalevent.edit&cid=' . $ev_id,
			'userid'        => $userId,
			'username'      => $user->username,
			'accountlink'   => 'index.php?option=com_users&task=user.edit&id=' . $userId
		);

		$this->addLog(array($message), $messageLangKey, $context, $userId);

	}

	public function onSaveTranslation(array $data, bool $success) {

        // Todo code in Save Translation Action Log
//		echo '<pre>';
//		var_dump($data);
//		echo '</pre>';
//		die('onSaveTranslation');

	}


	public function onAfterDeleteEvent(array $events) {

		$context    = Factory::getApplication()->input->get('option', 'com_jevents');

		foreach ($events AS $event)
		{
			$ev_id      = $event['id'];
			$title      = $event['title'];
			$eventDate  = (string) JevDate::getInstance($event['startDate']);

			$user           = Factory::getUser();
			$userId         = $user->id;

			$action         = 'delete';
			$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_DELETED';

			$message = array(
				'action'      => $action,
				'type'        => 'PLG_ACTIONLOG_JEVENTS_TYPE_EVENT',
				'id'          => $ev_id,
				'title'       => $title,
				'eventDate'   => $eventDate,
				'userid'      => $userId,
				'username'    => $user->username,
				'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $userId
			);

			$this->addLog(array($message), $messageLangKey, $context, $userId);
		}

	}

	public function onAfterDeleteEventRepeat($event) {

		$context    = Factory::getApplication()->input->get('option', 'com_jevents');

		$rp_id      = $event->rp_id();
		$title      = $event->title();
		$eventDate  = (string) JevDate::getInstance($event->_startrepeat);

		$user   = Factory::getUser();
		$userId = $user->id;

		$action         = 'delete';
		$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_EVENT_REPEAT_DELETED';

		$message = array(
			'action'        => $action,
			'type'          => 'PLG_ACTIONLOG_JEVENTS_TYPE_EVENT_REPEAT',
			'id'            => $rp_id,
			'title'         => $title,
			'eventDate'     => $eventDate,
			'userid'        => $userId,
			'username'      => $user->username,
			'accountlink'   => 'index.php?option=com_users&task=user.edit&id=' . $userId
		);

		$this->addLog(array($message), $messageLangKey, $context, $userId);

	}

	// Authorised User Triggers
	public function afterSaveUser($user) {

		$context    = Factory::getApplication()->input->get('option', 'com_jevents');

		$authorisedUser = Factory::getUser($user->user_id);

		$loggedInUser   = Factory::getUser();
		$userId = $loggedInUser->id;

		$action         = 'add';
		$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_ADDED_AUTHORISED_USER';

		if ($user->isNew === 0) {
			$action         = 'update';
			$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_UPDATED_AUTHORISED_USER';
		}

		$message = array(
			'action'        => $action,
			'type'          => 'PLG_ACTIONLOG_JEVENTS_AUTHORISED_USER',
			'id'            => $authorisedUser->id,
			'title'         => $authorisedUser->username,
			'userid'        => $userId,
			'username'      => $loggedInUser->username,
			'itemid'        => 'index.php?option=com_jevents&task=user.edit&cid=' . $authorisedUser->id,
			'accountlink'   => 'index.php?option=com_users&task=user.edit&id=' . $userId
		);

		$this->addLog(array($message), $messageLangKey, $context, $userId);

	}

	// Authorised User Triggers
	public function onAfterRemoveUser(array $users) {

		$context    = Factory::getApplication()->input->get('option', 'com_jevents');


		foreach ($users as $user)
		{

			$authorisedUser = Factory::getUser($user->user_id);

			$loggedInUser = Factory::getUser();
			$userId       = $loggedInUser->id;

			$action         = 'delete';
			$messageLangKey = 'PLG_ACTIONLOG_JEVENTS_REMOVED_AUTHORISED_USER';

			$message = array(
				'action'      => $action,
				'type'        => 'PLG_ACTIONLOG_JEVENTS_AUTHORISED_USER',
				'id'          => $authorisedUser->id,
				'title'       => $authorisedUser->username,
				'userid'      => $userId,
				'username'    => $loggedInUser->username,
				'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $userId
			);

			$this->addLog(array($message), $messageLangKey, $context, $userId);
		}

	}

	protected function addLog($messages, $messageLanguageKey, $context, $userId = null)
	{
		$user   = Factory::getUser($userId);
		$db     = Factory::getDbo();
		$date   = Factory::getDate();
		$params = ComponentHelper::getComponent('com_actionlogs')->getParams();

		if ($params->get('ip_logging', 0))
		{
			$ip = Factory::getApplication()->input->server->get('REMOTE_ADDR', null, 'raw');

			if (!filter_var($ip, FILTER_VALIDATE_IP))
			{
				$ip = 'COM_ACTIONLOGS_IP_INVALID';
			}
		}
		else
		{
			$ip = 'COM_ACTIONLOGS_DISABLED';
		}

		$loggedMessages = array();

		foreach ($messages as $message)
		{
			$logMessage                       = new stdClass;
			$logMessage->message_language_key = $messageLanguageKey;
			$logMessage->message              = json_encode($message);
			$logMessage->log_date             = (string) $date;
			$logMessage->extension            = $context;
			$logMessage->user_id              = $user->id;
			$logMessage->ip_address           = $ip;
			$logMessage->item_id              = isset($message['id']) ? (int) $message['id'] : 0;

			try
			{
				$db->insertObject('#__action_logs', $logMessage);
				$loggedMessages[] = $logMessage;
			}
			catch (RuntimeException $e)
			{
				// Ignore it
			}
		}

	}

}