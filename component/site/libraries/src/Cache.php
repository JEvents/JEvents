<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die('Restricted access');

/**
 * Callback executor with output buffering.
 * Legacy class name: jevCache
 */
class Cache
{
	public function __construct()
	{
	}

	public function call()
	{
		$args     = func_get_args();
		$callback = array_shift($args);

		if (is_array($callback))
		{
			// standard php callback array
		}
		elseif (strstr($callback, '::'))
		{
			[$class, $method] = explode('::', $callback);
			$callback = [trim($class), trim($method)];
		}
		elseif (strstr($callback, '->'))
		{
			[$object_123456789, $method] = explode('->', $callback);
			global $$object_123456789;
			$callback = [$$object_123456789, $method];
		}

		$Args = is_array($args) ? $args : (!empty($args) ? [&$args] : []);

		ob_start();
		ob_implicit_flush(false);
		$result = call_user_func_array($callback, $Args);
		$output = ob_get_clean();

		echo $output;

		return $result;
	}

	public function get()
	{
		$args     = func_get_args();
		$callback = array_shift($args);

		if (is_array($args) && count($args) == 1 && is_array($args[0]))
		{
			$args = $args[0];
		}

		if (is_array($callback))
		{
			// standard php callback array
		}
		elseif (strstr($callback, '::'))
		{
			[$class, $method] = explode('::', $callback);
			$callback = [trim($class), trim($method)];
		}
		elseif (strstr($callback, '->'))
		{
			[$object_123456789, $method] = explode('->', $callback);
			global $$object_123456789;
			$callback = [$$object_123456789, $method];
		}

		$Args = is_array($args) ? $args : (!empty($args) ? [&$args] : []);

		ob_start();
		ob_implicit_flush(false);
		$result = call_user_func_array($callback, $Args);
		$output = ob_get_clean();

		echo $output;

		return $result;
	}
}