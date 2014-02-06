<?php
defined('_JEXEC') or die('Restricted access');

function FlatViewHelperFooter16($view)
{
	if (JRequest::getInt('pop', 0))
	{
		?>
		<div class="ev_noprint"><p align="center">
				<a href="#close" onclick="if (window.parent == window) {
									self.close();
								} else
									try {
										window.parent.SqueezeBox.close();
										return false;
									} catch (e) {
										self.close();
										return false;
									}" title="<?php echo JText::_('JEV_CLOSE'); ?>"><?php echo JText::_('JEV_CLOSE'); ?></a>
			</p></div>
		<?php
	}
	$view->loadHelper("JevViewCopyright");
	JevViewCopyright();
	?>
	</div>
	<?php
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger('onJEventsFooter');

	$task = JRequest::getString("jevtask");
	$view->loadModules("jevpostjevents");
	$view->loadModules("jevpostjevents_" . $task);
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	if (($params->get('flatscalable', 0) == 1 || $params->get("flatwidth", 905) == "scalable") && (($task == "month.calendar" && !$params->get('flatlistmonth', 0)) || ($task == "week.listevents" && $params->get('flattabularweek', 0)) ))
	{
		$baseurl = JURI::root();
		?>        
		<script type="text/javascript">
					var myCSS = false;
					var processedClones = false;
					function setJEventsSize() {

						var jeventsBody = $("jevents_body");
						var jeventsBodyParent = jeventsBody.getParent();
						var size = jeventsBodyParent.getSize();
						var narrow = false;
						if (!myCSS) {
							if (size.x < 485) {
								myCSS = Asset.css('<?php echo $baseurl;?>components/com_jevents/views/flat/assets/css/narrowscalable.css', {id: 'myStyle', title: 'myStyle'});
								narrow = true;
							}
						}
						else {
							if (size.x < 485) {
								myCSS.href = '<?php echo $baseurl;?>components/com_jevents/views/flat/assets/css/narrowscalable.css';
								narrow = true;
							}
							else {
								myCSS.href = '<?php echo $baseurl;?>components/com_jevents/views/flat/assets/css/scalable.css';
								narrow = false;
							}
						}
						if (narrow) {
							cloneEvents();
							var listrowblock = document.getElement(".jev_listrowblock");
							if (listrowblock) {
								listrowblock.style.display = "block";
							}
						}
						else {
							var listrowblock = document.getElement(".jev_listrowblock");
							if (listrowblock) {
								listrowblock.style.display = "none";
							}
							setOutOfMonthSize.delay(1000);
						}
					}
					function setOutOfMonthSize() {
						$$(".jev_dayoutofmonth").each(
								function(el) {
									if (el.getParent().hasClass("slots1")) {
										el.style.minHeight = "81px";
									}
									else {
										var psize = el.getParent().getSize();
										el.style.minHeight = psize.y + "px";
									}
								}, this);
					}
					function cloneEvents() {
						if (!processedClones) {
							processedClones = true;

							var myEvents = $$(".eventfull");
							// sort these to be safe!!
							myEvents.sort(function(a, b) {
								if (!a.sortval) {
									var aparentclasses = a.getParent().className.split(" ");
									for (var i = 0; i < aparentclasses.length; i++) {
										if (aparentclasses[i].indexOf("jevstart_") >= 0) {
											a.sortval = aparentclasses[i].replace("jevstart_", "");
										}
									}
								}
								if (!b.sortval) {
									var bparentclasses = b.getParent().className.split(" ");
									for (var i = 0; i < bparentclasses.length; i++) {
										if (bparentclasses[i].indexOf("jevstart_") >= 0) {
											b.sortval = bparentclasses[i].replace("jevstart_", "");
										}
									}
								}
								if (a.sortval == b.sortval) {
									var asiblings = a.getParent().childNodes;
									for (var i = 0; i < asiblings.length; i++) {
										if (asiblings[i].className && asiblings[i].className.indexOf("hiddendayname") >= 0) {
											return -1;
										}
									}
									var bsiblings = b.getParent().childNodes;
									for (var i = 0; i < bsiblings.length; i++) {
										if (bsiblings[i].className && bsiblings[i].className.indexOf("hiddendayname") >= 0) {
											return 1;
										}
									}
								}
								return (a.sortval < b.sortval) ? -1 : (a.sortval > b.sortval) ? 1 : 0;
								//return a.sortval>b.sortval;
							});

							if (myEvents.length == 0) {
								return;
							}
							var listrowblock = new Element('div', {'class': 'jev_listrowblock'});

							var event_legend_container = document.getElement(".event_legend_container");
							if (event_legend_container) {
								listrowblock.inject(event_legend_container, 'before');
							}
							else {
								var toprow = $("jev_maincal").getElement(".jev_toprow");
								listrowblock.inject(toprow, 'after');
								var clearrow = new Element('div', {'class': 'jev_clear'});
								clearrow.inject(listrowblock, 'after');
							}

							var listrow = new Element('div', {'class': 'jev_listrow'});
							var hasdaynames = false;
							myEvents.each(function(el) {
								if (!hasdaynames) {
									var dayname = el.getParent().getElement(".hiddendayname");
									if (dayname) {
										hasdaynames = true;
									}
								}
							});

							myEvents.each(function(el) {

								var dayname = el.getParent().getElement(".hiddendayname");
								if (dayname) {
									dayname.style.display = "block";
									dayname.inject(listrowblock, 'bottom');
								}
								if (dayname || !hasdaynames) {
									// really should be for each separate date!
									listrow = new Element('div', {'class': 'jev_listrow'});
									listrow.style.marginBottom = "10px";
									listrow.style.marginTop = "5px";
									listrow.inject(listrowblock, 'bottom');
								}

								var hiddentime = el.getParent().getElement(".hiddentime");
								hiddentime = hiddentime.getElement("a");
								hiddentime.removeClass("hiddentime");
								hiddentime.inject(listrow, 'bottom');

								var myClone = el.getParent().clone();
								myClone.addClass("jev_daywithevents");
								myClone.removeClass("jev_dayoutofmonth");
								myClone.removeClass("jevblocks0");
								myClone.removeClass("jevblocks1");
								myClone.removeClass("jevblocks2");
								myClone.removeClass("jevblocks3");
								myClone.removeClass("jevblocks4");
								myClone.removeClass("jevblocks5");
								myClone.removeClass("jevblocks6");
								myClone.removeClass("jevblocks7");
								myClone.style.height = "inherit";
								myClone.inject(listrow, 'bottom');

								var clearrow = new Element('div', {'class': 'jev_clear'});
								clearrow.inject(listrow, 'bottom');
							});
						}
					}
					window.addEvent("domready", setJEventsSize);
					// set load event too incase template sets its own domready trigger
					window.addEvent("load", setJEventsSize);
					window.addEvent("resize", setJEventsSize);
		</script>
		<?php
	}
	JEVHelper::componentStylesheet($view, "extra.css");
	jimport('joomla.filesystem.file');

	// Lets check if we have editted before! if not... rename the custom file.
	if (JFile::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
	{
		// It is definitely now created, lets load it!
		JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
	}
	
}
