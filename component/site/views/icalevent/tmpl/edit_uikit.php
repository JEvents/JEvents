<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

// In the frontend we need to force the styling of Chosen
$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("bootstrapchosen", 1))
{
	ob_start();
	?>
        jQuery(function($) {
            'use strict';

            var $w = $(window);

            $(document.body)
                // add color classes to chosen field based on value
                .on('liszt:ready', 'select[class^="chzn-color"], select[class*=" chzn-color"]', function () {
                    var $select = $(this);
                    var cls = this.className.replace(/^.(chzn-color[a-z0-9-_]*)$.*/, '$1');
                    var $container = $select.next('.chzn-container').find('.chzn-single');

                    $container.addClass(cls).attr('rel', 'value_' + $select.val());
                    $select.on('change click', function () {
                        $container.attr('rel', 'value_' + $select.val());
                    });
                })
        });
	<?php
	$script = ob_get_clean();
	Factory::getDocument()->addScriptDeclaration($script);
}

include_once(JEV_ADMINPATH . "/views/icalevent/tmpl/" . basename(__FILE__));
