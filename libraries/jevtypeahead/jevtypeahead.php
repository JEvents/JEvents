<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for Bootstrap elements.
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.0
 */
class JevTypeahead
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	

	/**
	 * Method to load the Bootstrap JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   mixed  $debug  Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($debug = null)
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		// Load jQuery
		JHtml::_('jquery.framework');
		JHtml::stylesheet('com_jevents/lib_jevtypeahead/jevtypeahead.css',array(),true);
		JHtml::script('com_jevents/lib_jevtypeahead/typeahead.bundle.min.js',array("framework"=>false,"relative"=>true));

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug = (boolean) $config->get('debug');
		}
		
		static::$loaded[__METHOD__] = true;

		return;
	}
	
	/**
	 * Add javascript support for Bootstrap typeahead
	 *
	 * @param   string  $selector  The selector for the typeahead element.
	 * @param   array   $params    An array of options for the typeahead element.
	 *                             Options for the tooltip can be:
	 *                             - prefetch     string,  the url to get initial data from.
	 *                                                             The function is passed two arguments, the query value in the input field and the
	 *                                                             process callback. The function may be used synchronously by returning the data
	 *                                                             source directly or asynchronously via the process callback's single argument.
	 *                             - field       string           the field value with the value of the typeahead.
	 *                             - remote    string           the url to get the data if prefetch is not enough.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function typeahead($selector = '.jevtypeahead', $params = array())
	{
		if (!isset(static::$loaded[__METHOD__][$selector]))
		{
			// NEEDS TO BE DIFFERENT FOR EACH SELECTOR TO SUPPORT MULTIPLE INSTANCES ON ONE PAGE!
			// so we also need different javascript variable names
			$jsname = "typeaheadData".md5($selector);

			// Include Typeahead framework
			static::framework();

			// Setup options object
			$opt['prefetch']	= isset($params['prefetch']) ? $params['prefetch'] : '';
			$opt['remote']		= isset($params['remote']) ? $params['remote'] : '';
			$opt['data_value']	= isset($params['data_value']) ? $params['data_value'] : 'value';
			$opt['menu']	= isset($params['menu']) ? $params['menu'] : 'tt-menu';

			$opt['data_id']    = isset($params['data_id']) ? $params['data_id'] : 'id';
			$opt['field_selector']    = isset($params['field_selector']) ? $params['field_selector'] : '';
			$opt['highlight']	= isset($params['highlight']) ? $params['highlight'] : 'true';
			$opt['minLength']	= isset($params['minLength']) ? (int) $params['minLength'] : '3';
			$opt['limit']	= isset($params['limit']) ? (int) $params['limit'] : '10';
			$opt['scrollable']	= isset($params['scrollable']) ? (int) $params['scrollable'] : '0';
			$opt['emptyCallback']	= isset($params['emptyCallback']) ?  $params['emptyCallback'] : '';
			// Call back method which receives the matched data
			$opt['callback']	= isset($params['callback']) ?  $params['callback'] : '';
			$opt['json']	= isset($params['json']) ?  $params['json'] : '';

			if ($opt['scrollable']){
				JFactory::getDocument()->addStyleDeclaration( ".scrollable-dropdown-menu .tt-menu, .scrollable-dropdown-menu .tt-dropdown-menu, #scrollable-dropdown-menu .tt-menu, #scrollable-dropdown-menu .tt-dropdown-menu{max-height: 150px; overflow-y: auto; }");
			}

			$options = json_encode($opt);
			if($opt['prefetch']||$opt['remote'])
			{
				$typeaheadLoad = "jQuery(document).ready(function() {

										var $jsname = new Bloodhound({
										datumTokenizer: Bloodhound.tokenizers.obj.whitespace('".$opt['data_value']."'),
										//identify: function(obj) { return obj.id; },
										queryTokenizer: Bloodhound.tokenizers.whitespace,
									";
				if($opt['prefetch'])
				{
					$typeaheadLoad .= "prefetch: {
							url:'".$opt['prefetch']."&token=".JSession::getFormToken()."',
							ttl:10000 // 10 seconds cache time - increase in production
							},";
				}

				$callback = "";
				if ($opt['emptyCallback']){
					$callback = ', transform: function(response) {if(response.length==0){'.$opt['emptyCallback'].'}; return response;} ';
				}
				else 	if ($opt['callback']){
					$callback = ', transform: function(response) {return '.$opt['callback'].'(response);} ';
				}


				$prepare = "";
				if ($opt['json']){
					$prepare = "
, prepare: function(query, settings) {
	       // replace wildcard with result of query -  does not work for some reason :(
                settings.url = settings.url.replace(settings.wildcard, encodeURIComponent(query));
                settings.dataType = 'json';
                settings.type = 'POST';
                settings.data = {json:" .$opt['json']. ", typeahead:query, token:'".JSession::getFormToken()."'};
                return settings;
            },
";

				}

				if($opt['remote'])
				{
					$typeaheadLoad .= "remote: {
										url: '".$opt['remote']."&token=".JSession::getFormToken()."&typeahead=PQRZYX',
										wildcard: 'PQRZYX'
										$callback
										$prepare
									},";
				}
			$typeaheadLoad .= "});\n";

			// clear local cache!
		//	$typeaheadLoad .= "$jsname.clear();$jsname.clearPrefetchCache();";

				$typeaheadLoad .= "jQuery(document).ready(function() {"
						. "jQuery('".$selector."').typeahead
									(
										{
											highlight: ".$opt['highlight'].",
											minLength: ".$opt['minLength'].",
											classNames : { menu: '" . $opt['menu'] . "'},
										},
										{
											name: '$jsname',
											display: '".$opt['data_value']."',
											limit:  ".$opt['limit'].",
											source: $jsname
										}
									);
							})\n";
				if($opt['field_selector'])
				{
					$typeaheadLoad .= "jQuery('".$selector."').on
										(
											'typeahead:select',
											function(ev,data)
											{
												jQuery('".$opt['field_selector']."').val(data.".$opt['data_id'].");
											}
										)\n";
				}
				$typeaheadLoad .= "});\n";
			}

			// Attach script to document
			JFactory::getDocument()->addScriptDeclaration($typeaheadLoad);

			// Set static array
			static::$loaded[__METHOD__][$selector] = true;
		}

		return;
	}
}
