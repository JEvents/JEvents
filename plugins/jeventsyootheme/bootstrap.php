<?php

use YOOtheme\Builder;
use YOOtheme\Config;
use YOOtheme\Path;

include_once __DIR__ . '/src/SettingsListener.php';
include_once __DIR__ . '/src/StylerListener.php';

return [
	'theme' => [
		'styles' => [
			'components' => [
				'jevents' => Path::get('./assets/less/jevents.less')
			],
		],
	],

	'events' => [
		// Add styler options
		'customizer.init' => [
			Ysts\StylerListener::class => 'initCustomizer',
			Ysts\SettingsListener::class => 'initCustomizer',
		],

		// Customise Styler  2.6
		'styler.imports' => [
		//	Ysts\StylerListener::class => 'stylerImports',
		]
	]
];
/*
return [

	'events' => [

		// Add settings Panels
		'customizer.init' => [
			StylerListener::class => 'initCustomizer',
			//SettingsListener::class => 'initCustomizer',
		],

		// Customise Styler  2.6
		'styler.imports' => [
			StylerListener::class => 'stylerImports',
		]

    ]

];
*/