<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;

/**
 * Database table row for jevents_repetition.
 * Legacy class name: iCalRepetition
 */
class IcalRepetition extends Table
{
	public ?int $rp_id          = null;
	public ?int $eventid        = null;
	public ?int $eventdetail_id = null;
	public ?string $startrepeat = null;
	public ?string $endrepeat   = null;

	public function __construct(&$db)
	{
		parent::__construct('#__jevents_repetition', 'rp_id', $db);
	}
}
