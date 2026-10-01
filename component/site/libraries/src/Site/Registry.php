<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents\Site;

defined('_JEXEC') or die('Restricted access');

/**
 * JEvents reference-capable registry, extending Joomla's Registry.
 * Legacy class name: JevRegistry
 */
class Registry extends \Joomla\Registry\Registry
{
	protected $jevregistry;
	protected string $jevDefaultNameSpace = 'default';

	protected static array $jevInstances = [];
	protected static array $jevInstancesWithReferences = [];

	public static function &getInstance(string $id, string $namespace = 'default'): static
	{
		if (empty(self::$jevInstances[$id]))
		{
			self::$jevInstances[$id] = new static($namespace);
		}

		return self::$jevInstances[$id];
	}

	public static function &getInstanceWithReferences(string $id, string $namespace = 'default'): static
	{
		if (empty(self::$jevInstancesWithReferences[$id]))
		{
			self::$jevInstancesWithReferences[$id] = new static($namespace);
		}

		return self::$jevInstancesWithReferences[$id];
	}

	public function setReference(string $regpath, mixed &$value): mixed
	{
		$nodes = explode('.', $regpath);
		$count = count($nodes);

		$namespace = $count < 2 ? $this->jevDefaultNameSpace : array_shift($nodes);
		$count     = count($nodes);

		if (!isset($this->jevregistry[$namespace]))
		{
			$this->makeNameSpace($namespace);
		}

		$ns        = &$this->jevregistry[$namespace]['data'];
		$pathNodes = max(0, $count - 1);

		for ($i = 0; $i < $pathNodes; $i++)
		{
			if (!isset($ns->{$nodes[$i]}))
			{
				$ns->{$nodes[$i]} = new \stdClass();
			}

			$ns = &$ns->{$nodes[$i]};
		}

		$ns->{$nodes[$i]} = &$value;

		return $ns->{$nodes[$i]};
	}

	public function &getReference(string $regpath, mixed $default = null): mixed
	{
		$result = $default;
		$nodes  = explode('.', $regpath);

		if ($nodes)
		{
			$count = count($nodes);

			if ($count < 2)
			{
				$namespace = $this->jevDefaultNameSpace;
				$nodes[1]  = $nodes[0];
			}
			else
			{
				$namespace = $nodes[0];
			}

			if (isset($this->jevregistry[$namespace]))
			{
				$ns        = &$this->jevregistry[$namespace]['data'];
				$pathNodes = $count - 1;

				for ($i = 1; $i < $pathNodes; $i++)
				{
					if (isset($ns->{$nodes[$i]}))
					{
						$ns = &$ns->{$nodes[$i]};
					}
				}

				if (isset($ns->{$nodes[$i]}))
				{
					$result = $ns->{$nodes[$i]};
				}
			}
		}

		return $result;
	}

	public function makeNameSpace(string $namespace): bool
	{
		$this->jevregistry[$namespace] = ['data' => new \stdClass()];

		return true;
	}
}
