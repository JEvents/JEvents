<?php
/**
 *
 * @copyright   Copyright (C) 2015 - JEVENTS_COPYRIGHT GWE Systems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Utility class for Bootstrap or UIKit Modal Popups especially URL based Modals which bootstrap usually fails on
 *
 */
#[\AllowDynamicProperties]
class JevModal
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Add javascript support for Bootstrap modal
	 *
	 * @param   string $selector   The selector for the modal element.
	 * @param   array  $params     An array of options for the modal element.
	 *                             Options for the tooltip can be:
	 *                             - size     string,  Values can be "max" or "h,w" for height and width values
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function modal($selector = "", $params = array())
	{
		// Include Modal framework
		static::framework();

		if ($selector !== "")
		{
			if (!isset(static::$loaded[__METHOD__][$selector]))
			{

				// Include Modal framework
				static::framework();

				$jsonParams = json_encode($params);

				$script = <<< SCRIPT
// Polyfills for MSIE
if (window.NodeList && !NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

document.addEventListener('DOMContentLoaded', function() {
	var targets = document.querySelectorAll('$selector');
	targets.forEach(function(target) {
		target.addEventListener('click', function(evt){
			evt.preventDefault();
			jevModalSelector(target, $jsonParams, evt);
			return false;
		}, target);
	});
});
SCRIPT;
				Factory::getDocument()->addScriptDeclaration($script);

				// Set static array
				static::$loaded[__METHOD__][$selector] = true;
			}
		}
		return;
	}


	/**
	 * Method to load the  JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   mixed $debug Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($debug = null, $forceBoostrap = false, $forceUIkit = false)
	{

		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		$jevparams = ComponentHelper::getParams('com_jevents');

		// UIKit or Bootstrap
		$jinput = Factory::getApplication()->input;

		$comMenus = $jinput->getCmd('option') == "com_menus";
		$comModules = $jinput->getCmd('option') == "com_modules" || $jinput->getCmd('option') == "com_advancedmodules";

		$task = $jinput->getString("task", $jinput->getString("jevtask", ""));
		if (!$forceBoostrap
			&& !$comMenus
			&& !$comModules
			&& (
				Factory::getApplication()->isClient('administrator')
				|| ( $jevparams->get("newfrontendediting", 1) && ($task == "icalevent.edit" || $task == "icalrepeat.edit"  || $task == "icalevent.list"))
			)
		)
		{
			HTMLHelper::script('com_jevents/lib_jevmodal/jevmodal_gsl.js', array('framework' => false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
		}
		else if (Factory::getApplication()->isClient('administrator') && Factory::getApplication()->input->getCmd('option') == 'com_rsvppro')
		{
			HTMLHelper::script('com_jevents/lib_jevmodal/jevmodal_gsl.js', array('framework' => false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
		}
		else if ($forceUIkit)
        {
            HTMLHelper::script('com_jevents/lib_jevmodal/jevmodal_gsl.js', array('framework' => false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
        }
		else if (!$comMenus && !$comModules && strpos($jevparams->get('framework', 'bootstrap'), 'uikit') === 0)
		{
			HTMLHelper::script('com_jevents/lib_jevmodal/jevmodal_uikit.js', array('framework' => false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));

			static::$loaded[__METHOD__] = true;
			return;
		}
		else {
			// Load jQuery
			HTMLHelper::_('jquery.framework');
			if (version_compare(JVERSION, '4', 'ge') &&
				(
					$jevparams->get('framework', 'native') == 'native'
					||
					$jevparams->get('framework', 'native') == 'bootstrap5'
				)
			)
			{
				// Include the Bootstrap component
				Factory::getApplication()
					->getDocument()
					->getWebAssetManager()
					->useScript('bootstrap.modal');
			}
			else if (version_compare(JVERSION, '4', 'lt') && $jevparams->get('framework', 'native') == 'native')
			{
				// Include Bootstrap framework
				JHtml::_('bootstrap.framework');
			}

			HTMLHelper::stylesheet('com_jevents/lib_jevmodal/jevmodal.css', array('relative' => true));
			HTMLHelper::script('com_jevents/lib_jevmodal/jevmodal.js', array('framework' => false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
		}
		static::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Add javascript support for popovers
	 *
	 * Use element's Title as popover content
	 *
	 * @param   string $selector                        Selector for the popover
	 * @param   array  $params                          An array of options for the popover.
	 *                                                  Options for the popover can be:
	 *                                                  animation  boolean          apply a css fade transition to the popover
	 *                                                  html       boolean          Insert HTML into the popover. If false, jQuery's text method will be used to insert
	 *                                                  content into the dom.
	 *                                                  placement  string|function  how to position the popover - top | bottom | left | right
	 *                                                  selector   string           If a selector is provided, popover objects will be delegated to the specified targets.
	 *                                                  trigger    string           how popover is triggered - hover | focus | manual
	 *                                                  title      string|function  default title value if `title` tag isn't present
	 *                                                  content    string|function  default content value if `data-content` attribute isn't present
	 *                                                  delay      number|object    delay showing and hiding the popover (ms) - does not apply to manual trigger type
	 *                                                  If a number is supplied, delay is applied to both hide/show
	 *                                                  Object structure is: delay: { show: 500, hide: 100 }
	 *                                                  container  string|boolean   Appends the popover to a specific element: { container: 'body' }
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function popover($selector = '.hasPopover', $params = array())
	{
		// unset($params['delay']);

		// Only load once
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		static::$loaded[__METHOD__][$selector] = true;

		$jevparams = ComponentHelper::getParams('com_jevents');
		$toolTipType = $jevparams->get('tooltiptype', 'bootstrap');

		// If using uikit framework then override tooltip preference for consistency
		if(strpos($jevparams->get('framework', 'bootstrap'), 'uikit') === 0)
		{
			$toolTipType = 'uikit';
		}

		// UIKit or Bootstrap
		$jinput = Factory::getApplication()->input;
		$option = $jinput->getCmd('option');
		$somethingElse = !in_array($option, array("com_jevents", "com_jevlocations", "com_jeventstags", "com_jevpeople",  "com_rsvppro"));
		if ($somethingElse && Factory::getApplication()->isClient('administrator'))
		{
			$toolTipType = 'bootstrap';
		}

		// Migrate old MooTools tooltips - from old settings
		if ($toolTipType == 'joomla')
		{
			$toolTipType = $jevparams->get('framework', 'bootstrap') == 'uikit' ? 'uikit' : 'bootstrap';
		}

		if (version_compare(JVERSION, '4', 'ge') && $toolTipType !== 'uikit')
		{

            $params["template"] = '<div class="popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>';
			// For Joomla 4 we need to change the data attribute - use the native popover method unless specifically using UIKit
			HTMLHelper::_('bootstrap.popover', $selector, $params);
            $popoverStyling = <<< SCRIPT
document.addEventListener('DOMContentLoaded', function() {
   var elements = document.querySelectorAll("$selector");
   elements.forEach(function(myPopoverTrigger)
   {
        myPopoverTrigger.addEventListener('inserted.bs.popover', function () {
            var title = myPopoverTrigger.getAttribute('data-bs-original-title') || false;
            const popover = bootstrap.Popover.getInstance(myPopoverTrigger);
            if (popover && popover.tip) 
            {
                var header = popover.tip.querySelector('.popover-header');
                var body = popover.tip.querySelector('.popover-body');
                var popoverContent = "";
                if (title)
                {
                    popoverContent += title;
                }
                var content = myPopoverTrigger.getAttribute('data-bs-original-content') || false;
                if (content)
                {
                    popoverContent += content;
                }

                if (header) {
                    header.outerHTML = popoverContent;
                }
                else if (body) {
                    body.outerHTML = popoverContent;
                }

                if (popover.tip.querySelector('.jev-click-to-open a') && 'ontouchstart' in document.documentElement)
                {
                    popover.tip.addEventListener('touchstart', function() {
                       document.location = popover.tip.querySelector('.jev-click-to-open a').href;
                    });
                }
            }
        });

        var title = myPopoverTrigger.getAttribute('data-bs-original-title') || false;
        const popover = bootstrap.Popover.getInstance(myPopoverTrigger);
        if (popover && (popover.tip || title)) 
        {
            if ('ontouchstart' in document.documentElement) {        
                myPopoverTrigger.addEventListener('click', preventPopoverTriggerClick);
            }
        }
   });
});
function preventPopoverTriggerClick(event)
{
    event.preventDefault();
}

SCRIPT;
            Factory::getApplication()
                ->getDocument()
                ->addScriptDeclaration($popoverStyling);

			return;
		}
		/*
		if (version_compare(JVERSION, '4', 'ge') && $jevparams->get('framework', 'native') == 'native')
		{
			// Include the Bootstrap component
			Factory::getApplication()
				->getDocument()
				->getWebAssetManager()
				->useScript('bootstrap.popover');
		}
		*/

		//$params['delay'] = [ 'show' => 50, 'hide' => 20000 ];

		if ($toolTipType !== 'uikit')
		{
			JHtml::_('jquery.framework');
			JHtml::_('bootstrap.framework');
            JLoader::register('JevHtmlBootstrap', JPATH_SITE . "/components/com_jevents/libraries/bootstrap.php");
            JevHtmlBootstrap::loadCss();
		}

		$opt['animation'] = isset($params['animation']) ? $params['animation'] : false;
		$opt['html']      = isset($params['html']) ? $params['html'] : true;
		$opt['placement'] = isset($params['placement']) ? $params['placement'] : false;
		$opt['selector']  = isset($params['selector']) ? $params['selector'] : false;
		$opt['title']     = isset($params['title']) ? $params['title'] : '';
		$opt['trigger']   = isset($params['trigger']) ? $params['trigger'] : 'hover focus';
		$opt['content']   = isset($params['content']) ? $params['content'] : '';
		$opt['delay']     = isset($params['delay']) ? $params['delay'] : false;
		$opt['container'] = isset($params['container']) ? $params['container'] : 'body';
		//$opt['template'] = isset($params['template']) ? $params['template'] : '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>';

		// Custom option to control display on touch devices
		$opt['mouseonly'] = isset($params['mouseonly']) ? $params['mouseonly'] : false;

		$options = json_encode($opt); //HTMLHelper::getJSObject($opt);

		$uikitopt = array();
		$uikitopt['title'] = isset($params['title']) ? $params['title'] : '';
		$uikitopt['pos']   = isset($params['placement']) ? $params['placement'] : 'top';
		$uikitopt['delay'] = isset($params['delay']['show']) ? $params['delay']['show'] : 0;
		$uikitopt['delayHide'] = 200;
		$uikitopt['offset'] = 20;
		$uikitopt['animation'] = 'uk-animation-fade';
		$uikitopt['duration'] = 100;
		$uikitopt['cls'] = 'uk-active uk-card uk-card-default uk-padding-remove  uk-background-default';
		$uikitopt['clsPos'] = isset($params['clsPos']) ? $params['clsPos']: 'uk-tooltip';
		$uikitopt['mode'] = isset($params['trigger']) ? str_replace(" ", ",", $params['trigger']) : 'hover';
		$uikitopt['container'] = isset($params['container']) ? $params['container'] : 'body';
		$uikitoptions = json_encode($uikitopt);

		$container = $opt['container'];

		Factory::getDocument()->addScriptDeclaration(
			<<< SCRIPT
function jevPopover(selector, container) {
	var uikitoptions = $uikitoptions; 
	var bsoptions = $options;
	uikitoptions.container = container;
	bsoptions.container = container;
	
	if (bsoptions.mouseonly && 'ontouchstart' in document.documentElement) {
		return;
	}
	if (jQuery(selector).length){
		try {
			ys_setuppopover(selector, uikitoptions);
		}
		catch (e) {
			if ('$toolTipType' != "uikit"  || typeof UIkit == 'undefined' ) {
			// Do not use this for YooTheme Pro templates otherwise you get strange behaviour!
				if (jQuery(selector).popover )
				{	
					// set data-title and data-content if not set or empty		
					var hoveritems = document.querySelectorAll(selector);
					hoveritems.forEach(function (hoveritem) {
						var title = hoveritem.getAttribute('data-original-title') || hoveritem.getAttribute('title')  || '';
						var body = hoveritem.getAttribute('data-original-content') || hoveritem.getAttribute('data-content') || '';
						if (body == '')
						{
							//hoveritem.setAttribute('data-original-content', 'hello kitty!');
							//hoveritem.setAttribute('data-content', 'hello kitty!');
						}
					});
					jQuery(selector).popover(bsoptions);
				}
				else 
				{
					if ('$toolTipType' != "uikit")
					{
						alert("problem with popovers!  Failed to load Bootstrap popovers");
					}
					else 
					{
						alert("problem with popovers! Failed to load UIkit popovers");
					}
				}
			}
			else 
			{
				// Fall back to native uikit
				var hoveritems = document.querySelectorAll(selector);
				hoveritems.forEach(function (hoveritem) {
					var title = hoveritem.getAttribute('data-yspoptitle') || hoveritem.getAttribute('data-original-title') || hoveritem.getAttribute('title');
					var body = hoveritem.getAttribute('data-yspopcontent') || hoveritem.getAttribute('data-content') || hoveritem.getAttribute('data-bs-content') || '';
					var options = hoveritem.getAttribute('data-yspopoptions') || uikitoptions;
					if (typeof options == 'string') {
						options = JSON.parse(options);
					}
					/*
					var phtml = '<div class="uk-card uk-card-default uk-padding-remove uk-background-default" style="width:max-content;border-top-left-radius: 5px;border-top-right-radius: 5px;">' +
					(title != '' ? '<div class="uk-text-emphasis">' + title + '</div>' : '') +
					(body != '' ? '<div class="uk-card-body uk-text-secondary uk-padding-small" style="width:max-content">' + body + '</div>' : '') +
					'</div>';
					*/						
					var phtml = '' +
					(title != '' ? title.replace("jevtt_title", "uk-card-title uk-text-emphasis uk-padding-small").replace(/color:#(.*);/,'color:#$1!important;')  : '') +
					(body != '' ?  body.replace("jevtt_text", "uk-card-body uk-padding-small uk-text-secondary  uk-background-default")  : '') +
					'';
					options.title = phtml;
					
					if (hoveritem.hasAttribute('title')) {
						hoveritem.removeAttribute('title');
					}
					
					UIkit.tooltip(hoveritem, options);
				});
			}	
		}
	}
}
document.addEventListener('DOMContentLoaded', function()
{
	try {
		jevPopover('$selector', '$container');
	}
	catch (e) 
	{
	}
});
SCRIPT
		);

		static $hide = false;
		if (!$hide)
		{
			$hide = "
(function($) {
	if (typeof $.fn.popover == 'undefined')
	{
		// bootstrap popovers not used or loaded
		return;
	}

    var oldHide = $.fn.popover.Constructor.prototype.hide || false;

    $.fn.popover.Constructor.prototype.hide = function() {
		var bootstrap5 = false;
		var bootstrap4 = false;
		try {
	        var testClass = window.bootstrap.Tooltip || window.bootstrap.Modal;
	        var bsVersion = testClass.VERSION.substr(0,1);

		    bootstrap5 = bsVersion >= 5;
		    bootstrap4 = bsVersion >= 4 && !bootstrap5;
		} catch (e) {
		}
        var bootstrap3 = window.jQuery && (typeof jQuery().emulateTransitionEnd == 'function');
        // Bootstrap  3+         
        if (this.config || bootstrap4 || bootstrap3 || bootstrap5)
        {
            //- This is not needed for recent versions of Bootstrap 4
            /*
	        if (this.config.container == '#jevents_body' && this.config.trigger.indexOf('hover') >=0) {
	            var that = this;
	            // try again after what would have been the delay
	            setTimeout(function() {
	                return that.hide.call(that, arguments);
	            }, that.config.delay.hide);
	            return;
	        }
	        */
        }
        // Earlier Bootstraps 
        else
        {
	        if (this.options.container == '#jevents_body' && this.options.trigger.indexOf('hover') >=0  && this.tip().is(':hover')) {
	            var that = this;
	            // try again after what would have been the delay
	            setTimeout(function() {
	                try {
	                    return that.hide.call(that, arguments);
	                }
	                catch (e) 
	                {
	                }
	            }, that.options.delay.hide);
	            return;
	        }
        }
        if ( oldHide )
        {
            oldHide.call(this, arguments);
        }
    };

})(jQuery);";
			Factory::getDocument()->addScriptDeclaration($hide);
		}

		return;
	}

	/**
	 * Add javascript support for Bootstrap tooltips
	 *
	 * Add a title attribute to any element in the form
	 * title="title::text"
	 *
	 * @param   string $selector                                 The ID selector for the tooltip.
	 * @param   array  $params                                   An array of options for the tooltip.
	 *                                                           Options for the tooltip can be:
	 *                                                           - animation  boolean          Apply a CSS fade transition to the tooltip
	 *                                                           - html       boolean          Insert HTML into the tooltip. If false, jQuery's text method will be used to insert
	 *                                                           content into the dom.
	 *                                                           - placement  string|function  How to position the tooltip - top | bottom | left | right
	 *                                                           - selector   string           If a selector is provided, tooltip objects will be delegated to the specified targets.
	 *                                                           - title      string|function  Default title value if `title` tag isn't present
	 *                                                           - trigger    string           How tooltip is triggered - hover | focus | manual
	 *                                                           - delay      integer          Delay showing and hiding the tooltip (ms) - does not apply to manual trigger type
	 *                                                           If a number is supplied, delay is applied to both hide/show
	 *                                                           Object structure is: delay: { show: 500, hide: 100 }
	 *                                                           - container  string|boolean   Appends the popover to a specific element: { container: 'body' }
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function tooltip($selector = '.hasTooltip', $params = array())
	{
		HTMLHelper::_('bootstrap.tooltip',$selector, $params);

		return;
	}
}
