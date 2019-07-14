<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: cpanel.php 3119 2011-12-20 14:34:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2019 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

$params  = ComponentHelper::getParams(JEV_COM_COMPONENT);

JEVHelper::stylesheet('jev_cp.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

?>

<div id="jevents" >
    <!-- CONTENT -->
    <div class="gsl-container gsl-container-expand">
        <div class="gsl-grid gsl-grid-divider gsl-grid-medium gsl-child-width-1-2 gsl-child-width-1-3@m gsl-child-width-1-4@l"
             gsl-grid>
            <div>
                <span class="gsl-text-small">
                        <span gsl-icon="icon:calendar" class="gsl-margin-small-right gsl-text-primary"></span>
                        <?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS");?>
                </span>
                <h2 class="gsl-heading-primary gsl-margin-remove gsl-text-primary">
                    <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=icalevent.list")?>">
	                    <?php echo JText::sprintf("COM_JEVENTS_FUTURE_EVENTS", $this->futureEvents);?>
                    </a>
                </h2>
                <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=icalevent.list")?>"
                   class="gsl-text-small gsl-text-success gsl-display-block">
                    <span class="gsl-text-success" gsl-icon="icon: history"></span>
	                <?php echo JText::sprintf("COM_JEVENTS_PAST_EVENTS", $this->pastEvents);?>
                </a>
            </div>
            <div>
                <span class="gsl-text-small">
                        <span gsl-icon="icon:calendar"
                              class="gsl-margin-small-right gsl-text-danger"></span>
                        <?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS_UNPUBLISHED");?>
                </span>
                <h2 class="gsl-heading-primary gsl-margin-remove gsl-text-primary">
                    <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=icalevent.list")?>">
	                    <?php echo $this->unpublishedEvents;?>
                    </a>
                </h2>
            </div>
            <div>
                <span class="gsl-text-small">
                        <span gsl-icon="icon:plus-circle" class="gsl-margin-small-right gsl-text-primary"></span>
                        <?php echo JText::_("COM_JEVENTS_NEW_EVENTS");?>
                </span>
                <h2 class="gsl-heading-primary gsl-margin-remove gsl-text-primary">
                    <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=icalevent.list")?>">
	                    <?php echo JText::sprintf("COM_JEVENTS_NEW_EVENTS_THIS_Week", $this->newEvents);?>
                    </a>
                </h2>
                <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=icalevent.list")?>"
                   class="gsl-text-small gsl-text-success gsl-display-block">
                    <span class="gsl-text-success" gsl-icon="icon: plus-circle"></span>
		            <?php echo JText::sprintf("COM_JEVENTS_NEW_EVENTS_THIS_MONTH", $this->newThisMonth);?>
                </a>
            </div>
            <div>
                <span class="gsl-text-small">
                   <span gsl-icon="icon:users" class="gsl-margin-small-right gsl-text-primary"></span>
                   <?php echo JText::_("COM_JEVENTS_UPCOMING_REGISTRATIONS");?>
                </span>
                <?php if ($this->newRegistrations >= 0) { ?>
                <h2 class="gsl-heading-primary gsl-margin-remove  gsl-text-primary">
                    <a href="<?php echo JRoute::_("index.php?option=com_rsvppro&task=sessions.list")?>">
                        <?php echo JText::sprintf("COM_JEVENTS_UPCOMING_REGISTRATIONS_THIS_WEEK", $this->upcomingAttendees);?>
                    </a>
                </h2>
                    <a href="<?php echo JRoute::_("index.php?option=com_jevents&task=icalevent.list")?>"
                       class="gsl-text-small gsl-text-success gsl-display-block">
                        <span class="gsl-text-success" gsl-icon="icon: users"></span>
		                <?php echo JText::sprintf("COM_JEVENTS_UPCOMING_REGISTRATIONS_THIS_MONTH", $this->upcomingAttendeesThisMonth);?>
                    </a>
                <?php }
                else {
                    ?>
	                <h2 class="gsl-heading-primary gsl-margin-remove  gsl-text-primary hasYsPopover"
                        data-yspoptitle="<?php echo JText::_('COM_JEVENTS_REQUIRES_RSVPPRO'); ?>"
                        data-yspopcontent="<?php echo JText::_("COM_JEVENTS_REQUIRES_RSVPPRO_DETAIL"); ?>"
                    ?>
                        <?php echo $this->newRegistrations >= 0 ? $this->newRegistrations : JText::_("COM_JEVENTS_NOT_INSTALLED");?>
                    </h2>
                    <?php
                }
                ?>
            </div>
        </div>
        <hr>
		<?php
		$params = JComponentHelper::getParams("com_jevents");
		if ($params->get("shownews", 1))
		{
			// Get RSS parsed object
			try
			{
				$rssurl = "https://www.jevents.net/blog?format=feed&type=rss";
				$feed   = new JFeedFactory;
				$rssDoc = $feed->getFeed($rssurl);
			}
			catch (Exception $e)
			{
				 // echo JText::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
			}

			if (!empty($rssDoc) && is_object($rssDoc))
			{
				$feed = $rssDoc;
				?>
                <div class="gsl-margin-small gsl-width-1-1 gsl-position-relative gsl-card gsl-card-default gsl-card-small gsl-card-hover"
                     style="padding:10px 55px 35px 55px;">
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <h4 class="gsl-width-auto">
                                <span gsl-icon="icon:tv; ratio : 2" class="gsl-margin-small-right gsl-text-primary"></span>
                                <?php echo JText::_("COM_JEVENTS_JEVENTS_NEWS");?>
                            </h4>
                        </div>
                    </div>
                    <div class="gsl-card-body" gsl-slider="autoplay:true; autoplay-interval:5000; pause-on-hover:true">
                        <ul class="ys_newsfeed gsl-slider-items gsl-child-width-1-1" style="width: calc(100% + 20px);">
							<?php for ($i = 0, $max = min(count($feed), 3); $i < $max; $i++) { ?>
								<?php
								$uri  = $feed[$i]->uri || !$feed[$i]->isPermaLink ? trim($feed[$i]->uri) : trim($feed[$i]->guid);
								$uri  = !$uri || stripos($uri, 'http') !== 0 ? $rssurl : $uri;
								$text = $feed[$i]->content !== '' ? trim($feed[$i]->content) : '';
								?>
                                <li style="padding:0 20px 0 10px">
									<?php if (!empty($uri)) : ?>
                                        <span class="feed-link">
						<a href="<?php echo htmlspecialchars($uri, ENT_COMPAT, 'UTF-8'); ?>" target="_blank">
						<?php echo trim($feed[$i]->title); ?></a></span>
									<?php else : ?>
                                        <span class="feed-link"><?php echo trim($feed[$i]->title); ?></span>
									<?php endif; ?>
                                    <div class="feed-item-description">
										<?php
										// Strip the images.
										$text = JFilterOutput::stripImages($text);
										$text = strip_tags($text);
										$text = JHtml::_('string.truncate', $text, 200);
										echo str_replace('&apos;', "'", $text);
										?>
                                    </div>
                                </li>
							<?php } ?>
                        </ul>

                        <a class="gsl-position-center-left gsl-position-small gsl-hidden-hover" href="#"
                           gsl-slidenav-previous gsl-slider-item="previous"></a>
                        <a class="gsl-position-center-right gsl-position-small gsl-hidden-hover" href="#" gsl-slidenav-next
                           gsl-slider-item="next"></a>

                        <ul class="gsl-slider-nav gsl-dotnav gsl-position-bottom-center gsl-padding-small"></ul>
                    </div>
                </div>
				<?php
			}
		}
		?>
        <div class="gsl-grid gsl-grid-medium" gsl-grid>

            <!-- panel -->
            <div class="gsl-width-1-2@l">
                <div class="gsl-card gsl-card-default gsl-card-small gsl-card-hover">
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <div class="gsl-width-auto"><h4><?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS");?></h4></div>
                        </div>
                    </div>
                    <div class="gsl-card-body">
                        <div class="chart-container">
                            <canvas id="chart1"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /panel -->
            <!-- panel -->
            <div class="gsl-width-1-1 gsl-width-1-2@l">
                <div class="gsl-card gsl-card-default gsl-card-small gsl-card-hover">
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <div class="gsl-width-auto"><h4><?php echo JText::_("COM_JEVENTS_NEW_EVENTS_CREATED_BY_DAY");?></h4></div>
                        </div>
                    </div>
                    <div class="gsl-card-body">
                        <div class="chart-container">
                            <canvas id="chart2"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /panel -->
            <!-- panel -->
            <div class="gsl-width-1-2@s gsl-width-1-2@l">
                <div class="gsl-card gsl-card-default gsl-card-small gsl-card-hover">
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <div class="gsl-width-auto"><h4><?php echo JText::_("COM_JEVENTS_UPCOMING_REGISTRATIONS_BY_EVENT");?></h4></div>
                        </div>
                    </div>
                    <div class="gsl-card-body">
                        <div class="chart-container">
                            <canvas id="chart3"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /panel -->
            <!-- panel -->
            <div class="gsl-width-1-2@s gsl-width-1-2@l">
                <div class="gsl-card gsl-card-default gsl-card-small gsl-card-hover">
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <div class="gsl-width-auto"><h4><?php echo JText::_("COM_JEVENTS_UPCOMING_EVENTS_BY_WEEK");?></h4></div>
                        </div>
                    </div>
                    <div class="gsl-card-body">
                        <div class="chart-container">
                            <canvas id="chart4"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /panel -->
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js"></script>
    <script type="text/javascript">
		// Chart 1
		// ========================================================================
		new Chart(document.getElementById('chart1'), {
			type: 'pie',
			data: {
				labels: ['<?php echo  implode("', '", $this->eventsByCat); ?>'],
				datasets: [
					{
						backgroundColor: ['<?php echo  implode("', '", $this->eventsByCatColours); ?>'],
						data: [<?php echo  implode(", ", $this->eventsByCatCounts); ?>],
					},
				],
			},
			options: {
				responsiveAnimationDuration: 500,
				legend: {
					display: true,
                    position: 'right'
				},
				animation: {
					duration: 2000
				},
				title: {
					display: false,
				},
			}
		});


		// Chart 2
		// ========================================================================
		new Chart(document.getElementById('chart2'), {
			type: 'bar',
			data: {
				labels: ["<?php echo JText::_("JEV_MONDAY");?>",
					"<?php echo JText::_("JEV_TUESDAY");?>",
					"<?php echo JText::_("JEV_WEDNESDAY");?>",
					"<?php echo JText::_("JEV_THURSDAY");?>",
					"<?php echo JText::_("JEV_FRIDAY");?>",
					"<?php echo JText::_("JEV_SATURDAY");?>",
					"<?php echo JText::_("JEV_SUNDAY");?>",
				],
				datasets: [
					{
						backgroundColor: "#39f",
						data: [<?php echo implode(', ',$this->eventCountsByDay);?> ],
					},
				],
			},
			options: {
				maintainAspectRatio: false,
				responsiveAnimationDuration: 500,
				legend: {
					display: false
				},
				animation: {
					duration: 2000
				},
				title: {
					display: false,
				},
			}
		});

		// Chart 3
		// ========================================================================
		new Chart(document.getElementById('chart3'), {
			type: 'bar',
			data: {
				labels: ['<?php echo  implode("', '", $this->newAttendeeEvents); ?>'],
				datasets: [
					{
						backgroundColor: "#39f",
						data: [<?php echo  implode(", ", $this->newAttendeeCounts); ?>],
					},
				],
			},
			options: {
				maintainAspectRatio: false,
				responsiveAnimationDuration: 500,
				legend: {
					display: false
				},
				animation: {
					duration: 2000
				},
				title: {
					display: false,
				},
			}
		});

		// Chart 4
		// ========================================================================
		new Chart(document.getElementById('chart4'), {
			type: 'bar',
			data: {
				labels: ['<?php echo  implode("', '", $this->eventCountByWeekLabels); ?>'],
				datasets: [
					{
						backgroundColor: "#39f",
						data: [<?php echo  implode(", ", $this->eventCountByWeek); ?>],
					},
				],
			},
			options: {
				maintainAspectRatio: false,
				responsiveAnimationDuration: 500,
				legend: {
					display: false
				},
				animation: {
					duration: 2000
				},
				title: {
					display: false,
				},
				scales: {
					xAxes: [{
						scaleLabel: {
							display: true,
							labelString: "<?php echo JText::_("COM_JEVENTS_COUNT_BY_WEEK_COMMENCING"); ?>"
						}
					}]
				}
			}
		});

    </script>

    <form action="<?php echo JRoute::_('index.php?option=com_jevents'); ?>" method="post" name="adminForm" id="adminForm">
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="redirecturl" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="form_submitted" value="1"/>
        <input type="hidden" name="baseurl" id="baseurl" value="<?php echo JURI::root(); ?>"/>
        <input type="hidden" name="listlayout" id="listlayout"  value=""/>
        <input type="hidden" id="ystscomponent" value="dashboard"/>
		<?php echo JHtml::_('form.token', array('id' => "tokenid")); ?>
    </form>
    <!-- /CONTENT -->
</div>
