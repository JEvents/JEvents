<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

/**
 * Database table row for jevents_exception.
 * Legacy class name: iCalException
 */
class IcalException extends Table
{
	public ?int    $ex_id          = null;
	public ?int    $rp_id          = null;
	public ?int    $eventid        = null;
	public ?int    $eventdetail_id = null;
	/** 0 = deleted, 1 = other exception */
	public ?int    $exception_type = null;
	public string  $startrepeat    = '0000-00-00 00:00:00';
	public string  $oldstartrepeat = '0000-00-00 00:00:00';

	public function __construct(&$db)
	{
		parent::__construct('#__jevents_exception', 'ex_id', $db);
	}

	public static function loadByRepeatId(int $rp_id): static|false
	{
		$db   = Factory::getDbo();
		$db->setQuery('SELECT * FROM #__jevents_exception WHERE rp_id=' . $rp_id);
		$data = $db->loadObject();

		if (!$data)
		{
			return false;
		}

		$exception = new static($db);
		$exception->bind(get_object_vars($data));

		return $exception;
	}
}
