<?php

namespace Ysts;

use YOOtheme\Config;
use YOOtheme\Path;

class SettingsListener
{
    public static function initCustomizer(Config $config)
    {
        // Add panel, as an example using a dynamic PHP configuration
        $config->set('customizer.panels.my-panel', [
            'title'  => 'My Panel',
            'width'  => 400,
            'fields' => [
                'jevents.option_b' => [
                    'label' => 'Option B',
                    'description' => 'A description text.',
                ],
            ],
        ]);
        $config->set('customizer.sections.settings.fields.settings.items.my-panel', 'My Panel');

        // Add section, as an example using a static JSON configuration
        $config->addFile('customizer', Path::get('../config/customizer.json'));
    }
}
