<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die('Restricted access');

/**
 * JEvents version information.
 * Legacy class name: JEventsVersion
 */
class Version
{
	public string $PRODUCT     = 'JEvents';
	public string $RELEASE     = '99.99.99';
	public string $DEV_LEVEL   = '0';
	public string $PATCH_LEVEL = '0';
	public string $DEV_STATUS  = 'Stable';
	public string $COPYRIGHT   = 'Copyright &copy; 2006-JEVENTS_COPYRIGHT';
	public string $COPYRIGHTBY = 'GWE Systems Ltd, JEvents Project Group';
	public string $LINK        = 'http://www.jevents.net';

	public static function &getInstance(): static
	{
		static $instance;

        include_once ( JPATH_ADMINISTRATOR . "/components/com_jevents/helpers/jevents.php");

        if ($instance === null)
		{
			$instance          = new self();
			$instance->RELEASE = \JEventsHelper::JEvents_Version(false);
		}

		return $instance;
	}

	public function get(string $property): mixed
	{
		return $this->$property ?? null;
	}

	public function getUrl(): string
	{
		return $this->LINK;
	}

	public function getShortCopyright(): string
	{
		return $this->COPYRIGHT;
	}

	public function getLongCopyright(): string
	{
		return $this->COPYRIGHT . ' ' . $this->COPYRIGHTBY;
	}

	public function getLongVersion(): string
	{
		return $this->PRODUCT . ' ' . $this->getShortVersion();
	}

	public function getShortVersion(): string
	{
		return 'v' . $this->RELEASE . ' ' . $this->DEV_STATUS;
	}
}

// JevJoomlaVersion is not registered separately in the defines files; it is defined here
// so that loading JEvents\Version also makes JevJoomlaVersion available globally.
// Legacy class name: JevJoomlaVersion (aliased via defines files)
class JevJoomlaVersion
{
	public static function isCompatible(string $minimum): bool
	{
		return version_compare(JVERSION, $minimum, 'ge');
	}
}
