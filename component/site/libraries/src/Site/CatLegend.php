<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents\Site;

defined('_JEXEC') or die('Restricted access');

/**
 * Simple value object representing a category legend entry.
 * Legacy class name: catLegend
 */
#[\AllowDynamicProperties]
class CatLegend
{
	public function __construct(
		public int    $id,
		public string $name,
		public string $color,
		public string $description,
		public int    $parent_id = 0,
	) {
	}
}
