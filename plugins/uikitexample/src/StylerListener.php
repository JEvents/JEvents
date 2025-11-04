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
/*
		$config->add('customizer.sections.styler.components', [
				"dynamiclegend" => [
					"name"    => "Dynamic Legend",
					"groups"  => [
						"group" => "@dynamiclegend-group-*",
                        "nav" => "@dynamiclegend-nav-*",
                        "nav item" => "@dynamiclegend-nav-item-*",
                        "nav item line" => [
							"@dynamiclegend-nav-item-line-mode",
							"@dynamiclegend-nav-item-line-position-mode",
							"@dynamiclegend-nav-item-line-slide-mode",
							"@dynamiclegend-nav-item-line-*"
						],
                        "item" => "@dynamiclegend-item-*",
                        "toggle" => "@dynamiclegend-toggle-*",
                        "subtitle" => "@dynamiclegend-subtitle-*",
                        "primary" => "@dynamiclegend-primary-*",
                        "sticky" => "@dynamiclegend-sticky-*",
                        "dropdown" => "@dynamiclegend-dropdown-*",
                        "dropdown grid" => "@dynamiclegend-dropdown-grid-*",
                        "dropdown nav" => "@dynamiclegend-dropdown-nav-*",
                        "dropdown nav item" => "@dynamiclegend-dropdown-nav-item-*",
                        "dropdown nav header" => "@dynamiclegend-dropdown-nav-header-*",
                        "dropdown nav divider" => "@dynamiclegend-dropdown-nav-divider-*",
                        "dropdown nav sublist" => "@dynamiclegend-dropdown-nav-sublist-*",
                        "dropbar" => "@dynamiclegend-dropbar-*"

					],

					//"hover"   => ".uk-dynamiclegend",
					//"inspect" => ".uk-dynamiclegend, .uk-dynamiclegend-title, .uk-dynamiclegend-title > *, .uk-dynamiclegend-content, .uk-dynamiclegend-content > *"
				]
			]
		);
*/
		/*
		$config->add('customizer.sections.styler.components', [
				"example" => [
					"name"    => "Example",
					"groups"  => [
						"item"    => "@example-item-*",
						"title"   => "@example-title-*",
						"icon"    => "@example-icon-*",
						"content" => "@example-content-*"
					],
					"hover"   => ".uk-example",
					"inspect" => ".uk-example, .uk-example-title, .uk-example-title > *, .uk-example-content, .uk-example-content > *"
				]
			]
		);
*/
		// 2.5.10
	//	$data = $config->get('theme.styles.imports', []);
//		$data[] = JPATH_SITE . '/plugins/system/uikitexample/assets/less/example.less';
//		$config->add('theme.styles.imports', $data, true);

		//$config->set('theme.childDir', JPATH_SITE . '/plugins/system/uikitexample/assets/less/');

		// Add section, as an example using a static JSON configuration
		// This is done by the settings listener too!
		$config->addFile('customizer', Path::get('../config/customizer.json'));
	}

	// 2.6
	public static function stylerImports(Config $config, Styler $styler, $imports, $themeid)
	{
		// Really bad and doesn't even work!
		/*
		$file = '/plugins/system/uikitexample/assets/less/example.less';
		$less = file_get_contents(JPATH_SITE . $file);

		$basefile = "/templates/yootheme/vendor/assets/uikit-themes/master/base/_import.less";

		$position = array_search($basefile, array_keys($imports));
		if ($position >= 0)
		{
			$imports[$basefile] .= "\n" . $less;
		}
		return $imports;
		*/

		// This works BUT loads from YooTheme folder!!

		$basefile = "/templates/yootheme/vendor/assets/uikit-themes/master/base/_import.less";
		$importFile = "/templates/yootheme/vendor/assets/uikit-themes/master/base/example.less";
		$position = array_search($basefile, array_keys($imports));
		if ($position >= 0)
		{
			$imports[$basefile] .= "\n" . '@import "example.less";' . "\n";
			$part1 = array_splice($imports, 0, $position);
			$imports = array_merge($part1, array($importFile => file_get_contents(JPATH_SITE . $importFile)), $imports);
		}


		// This works BUT ONLY from YooTheme folder so copy it there!!!
		$srcfile = '/plugins/system/uikitexample/assets/less/examplexxx.less';
		$destfile = '/templates/yootheme/vendor/assets/uikit-themes/master/base/examplexxx.less';
		File::copy(JPATH_SITE . $srcfile, JPATH_SITE . $destfile);
		$importFile = $destfile;

		$basefile = "/templates/yootheme/vendor/assets/uikit-themes/master/base/_import.less";
		$position = array_search($basefile, array_keys($imports));
		if ($position >= 0)
		{
			$imports[$basefile] .= "\n" . '@import "' . $importFile . '";' . "\n";
			$part1 = array_splice($imports, 0, $position);
			$imports = array_merge($part1, array($importFile => file_get_contents(JPATH_SITE . $importFile)), $imports);
		}

		// Customised from '/templates/yootheme/vendor/assets/uikit/src/less/components/navbar.less';
		$importFile = '/templates/yootheme/vendor/assets/uikit/src/less/components/dynamiclegend.less';

		$basefile = "/templates/yootheme/vendor/assets/uikit/src/less/components/_import.less";
		$position = array_search($basefile, array_keys($imports));
		if ($position >= 0)
		{
			$imports[$basefile] .= "\n" . '@import "' . $importFile . '";' . "\n";
			$part1 = array_splice($imports, 0, $position);
			$imports = array_merge($part1, array($importFile => file_get_contents(JPATH_SITE . $importFile)), $imports);
		}

		// Customised from '/templates/yootheme/vendor/assets/uikit/src/less/components/navbar.less';
		$importFile = '/templates/yootheme/vendor/assets/uikit-themes/master/base/dynamiclegend.less';

		$basefile = "/templates/yootheme/vendor/assets/uikit-themes/master/base/_import.less";
		$position = array_search($basefile, array_keys($imports));
		if ($position >= 0)
		{
			$imports[$basefile] .= "\n" . '@import "' . $importFile . '";' . "\n";
			$part1 = array_splice($imports, 0, $position);
			$imports = array_merge($part1, array($importFile => file_get_contents(JPATH_SITE . $importFile)), $imports);
		}

/*
       // Deoesn't work!
		$file = '/plugins/system/uikitexample/assets/less/example.less';

		$importFile = '../../../../../../../plugins/system/uikitexample/assets/less/example.less';

		$basefile = "/templates/yootheme/vendor/assets/uikit-themes/master/base/_import.less";
		$path = realpath(JPATH_SITE . "/templates/yootheme/vendor/assets/uikit-themes/master/base/" . $importFile);
		$position = array_search($basefile, array_keys($imports));
		if ($position >= 0)
		{
			$imports[$basefile] .= "\n" . '@import "' . $importFile . '";' . "\n";
			$part1 = array_splice($imports, 0, $position);
			$imports = array_merge($part1, array($importFile => file_get_contents(JPATH_SITE . $file)), $imports);
		}


		//$file =  '/plugins/system/uikitexample/assets/less/william.less';
*/
		return $imports;

	}
}
