<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: helper.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.access.access');

class jevCache
{

	public
			function __construct()
	{

	}

	public
			function call()
	{
		// Get callback and arguments
		$args = func_get_args();
		$callback = array_shift($args);

		// Normalize callback
		if (is_array($callback))
		{
			// We have a standard php callback array -- do nothing
		}
		elseif (strstr($callback, '::'))
		{
			// This is shorthand for a static method callback classname::methodname
			list ($class, $method) = explode('::', $callback);
			$callback = array(trim($class), trim($method));
		}
		elseif (strstr($callback, '->'))
		{
			/*
			 * This is a really not so smart way of doing this... we provide this for backward compatability but this
			 * WILL! disappear in a future version.  If you are using this syntax change your code to use the standard
			 * PHP callback array syntax: <http://php.net/callback>
			 *
			 * We have to use some silly global notation to pull it off and this is very unreliable
			 */
			list ($object_123456789, $method) = explode('->', $callback);
			global $$object_123456789;
			$callback = array($$object_123456789, $method);
		}
		else
		{
			// We have just a standard function -- do nothing
		}

		if (!is_array($args))
		{
			$Args = !empty($args) ? array(&$args) : array();
		}
		else
		{
			$Args = &$args;
		}

		ob_start();
		ob_implicit_flush(false);

		$result = call_user_func_array($callback, $Args);
		$output = ob_get_contents();

		ob_end_clean();

		echo $output;
		return $result;

	}

}
