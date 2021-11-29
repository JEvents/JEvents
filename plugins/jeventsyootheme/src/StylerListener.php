<?php

namespace Ysts;
use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Theme\Styler;
use Joomla\Filesystem\File;

class StylerListener
{
	public static function initCustomizer(Config $config)
	{

		// Add entry to styler in global group
		//$config->add('customizer.sections.styler.components.global.groups', ['william' => "@global-tertiary-*"]);

		// Add section, as an example using a static JSON configuration
		// This is done by the settings listener too!
		$config->addFile('customizer', Path::get('../config/customizer.json'));
	}


}
