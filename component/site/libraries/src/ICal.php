<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die('Restricted access');

/**
 * Holds an iCalICSFile instance and its associated collection of IcalEvent objects.
 * Legacy class name: jIcal
 */
class ICal
{
	public mixed $icalFile   = null;
	public array $icalEvents = [];

	public function __construct()
	{
		$this->icalEvents = [];
	}
}
