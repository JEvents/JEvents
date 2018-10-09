<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modDetailHelper
{

	public static function getDetailBody($modid)
	{

		if (trim($modid) == "")
		{
			return false;
		}
		$reg     = JevRegistry::getInstance("com_jevents");
		$moddata = $reg->get("dynamicmodules");
		if (isset($moddata[$modid]))
		{
			return $moddata[$modid];
		}

		return false;

	}

}
