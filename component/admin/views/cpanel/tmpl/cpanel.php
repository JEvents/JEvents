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
$version = JEventsVersion::getInstance();

JEVHelper::stylesheet('jev_cp.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

if (!empty($this->sidebar))
{
	?>
	<div id="j-sidebar-container" class="span2">

		<?php echo $this->sidebar; ?>

		<?php
		//Version Checking etc
		?>
		<div class="jev_version">
			<?php echo JText::sprintf('JEV_CURRENT_VERSION', Joomla\String\StringHelper::substr($version->getShortVersion(), 1)); ?>
		</div>
	</div>
	<?php
}
$mainspan = 10;
$fullspan = 12;
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
                <h1 class="gsl-heading-primary gsl-margin-remove gsl-text-primary">
                    <a href="<?php echo JRoute::_("index.php?option=com_yoursites&view=sites")?>">
						123
                    </a>
                </h1>
                <a href="<?php echo JRoute::_("index.php?option=com_yoursites&view=sites&filter[updatesrequired]=1")?>" class="gsl-text-small gsl-text-danger">
                    <span class="gsl-text-danger" gsl-icon="icon: bolt"></span>
					20
                </a>
            </div>
            <div>
            <span class="gsl-text-small">
                    <span gsl-icon="icon:calendar" class="gsl-margin-small-right gsl-text-primary"></span>
                    <?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS_UNPUBLISHED");?>
            </span>
                <h1 class="gsl-heading-primary gsl-margin-remove gsl-text-primary">
                    <a href="<?php echo JRoute::_("index.php?option=com_yoursites&view=extensions")?>">
						100
                    </a>
                </h1>
                <a href="<?php echo JRoute::_("index.php?option=com_yoursites&view=extensions&filter[status]=updateavailable&filter[blockupgrade]=0")?>" class="gsl-text-small gsl-text-danger">
                    <span class="gsl-text-danger" gsl-icon="icon: bolt"></span>
					20
                </a>
            </div>
            <div>
            <span class="gsl-text-small">
                    <span gsl-icon="icon:calendar" class="gsl-margin-small-right gsl-text-primary"></span>
                    <?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS_UNPUBLISHED");?>
                </span>
                <h1 class="gsl-heading-primary gsl-margin-remove gsl-text-primary">
                    <a href="<?php echo JRoute::_("index.php?option=com_yoursites&view=backups")?>">
						85
                    </a>
                </h1>
                <div class="gsl-text-small">
                    <span class="gsl-text-danger" gsl-icon="icon: bolt"></span>
					10
                    <span class="gsl-text-danger" gsl-icon="icon: bolt"></span>
					15
                </div>

            </div>
            <div class="gsl-visible@l">
            <span class="gsl-text-small">
                   <span gsl-icon="icon:calendar" class="gsl-margin-small-right gsl-text-primary"></span>
                   <?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS_NEW_THIS_WEEK");?>
               </span>
                <h1 class="gsl-heading-primary gsl-margin-remove  gsl-text-primary">
                    22
                </h1>
                <div class="gsl-text-small">
                    <span class="gsl-text-danger" gsl-icon="icon: bolt"></span>
					5
                </div>
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
            <div class="gsl-width-1-2@l">
                <div class="gsl-card gsl-card-default gsl-card-small gsl-card-hover">
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <div class="gsl-width-auto"><h4><?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS_UNPUBLISHED");?></h4></div>
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
            <div class="gsl-width-1-1 gsl-width-1-2@l">
                <div class="gsl-card gsl-card-default gsl-card-small gsl-card-hover">
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <div class="gsl-width-auto"><h4><?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS_NEW_THIS_WEEK");?></h4></div>
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
                            <div class="gsl-width-auto"><h4><?php echo JText::_("COM_JEVENTS_TOTAL_EVENTS_NEW_THIS_WEEK");?></h4></div>
                        </div>
                    </div>
                    <div class="gsl-card-body">
                        <div class="chart-container">
                            <canvas id="chart5"></canvas>
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
		var char1El = document.getElementById('chart1');
		new Chart(char1El, {
			type: 'bar',
			data: {
				labels: ["<?php echo JText::_("COM_YOURSITES_DASHBOARD_TOTAL_SITES");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_UP_TO_DATE");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_UPDATE_AVAILABLE");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_IS_IP");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_IS_DOWN");?>"],
				datasets: [
					{
						label: "Joomla",
						backgroundColor: "#39f",
						data: [<?php echo implode(', ', $sitedata[1]);?>]
					},
					<?php
					$params = JComponentHelper::getParams("com_yoursites");
					if ($params->get("supportwp", 0))
					{
					?>
					{
						label: "WordPress/ClassicPress",
						backgroundColor: "#895df6",
						data: [<?php echo implode(', ', $sitedata[2]);?>]
					}
					,
					{
						label: "Other",
						backgroundColor: "#3cba9f",
						data: [<?php echo implode(', ', $sitedata[999]);?>]
					}
					<?php
					}
					?>
				],
			},
			options: {
				maintainAspectRatio: false,
				responsiveAnimationDuration: 500,
				legend: {
					display: true
				},
				animation: {
					duration: 2000
				},
				title: {
					display: true,
					text: '<?php echo JText::_("COM_YOURSITES_DASHBOARD_SITES_SUMMARY_AND_STATUS");?>'
				},
			}
		});

		// Chart 2
		// ========================================================================
		var char2El = document.getElementById('chart2');

		new Chart(char2El, {
			type: 'bar',
			data: {
				labels: ["<?php echo JText::_("COM_YOURSITES_DASHBOARD_TOTAL_EXTENSIONS");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_UP_TO_DATE");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_UPDATE_AVAILABLE");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_UPDATES_BLOCKED");?>",
				],
				datasets: [
					{
						label: "Joomla",
						backgroundColor: "#39f",
						data: [<?php echo implode(', ', $extensiondata[1]);?>]
					},
					<?php
					$params = JComponentHelper::getParams("com_yoursites");
					if ($params->get("supportwp", 0))
					{
					?>
					{
						label: "WordPress/ClassicPress",
						backgroundColor: "#895df6",
						data: [<?php echo implode(', ', $extensiondata[2]);?>]
					}
					<?php
					}
					?>
				],
			},
			options: {
				maintainAspectRatio: false,
				responsiveAnimationDuration: 500,
				legend: {
					display: true
				},
				animation: {
					duration: 2000
				},
				title: {
					display: true,
					text: '<?php echo JText::_("COM_YOURSITES_DASHBOARD_EXTENSIONS_SUMMARY_AND_STATUS");?>'
				},
			}
		});


		// Chart 3
		// ========================================================================
		var char3El = document.getElementById('chart3');

		new Chart(char3El, {
			type: 'bar',
			data: {
				labels: ["<?php echo JText::_("COM_YOURSITES_DASHBOARD_COMPLETED_BACKUPS");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_FAILED_BACKUPS");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_BACKUPS_NO_INSTALLED");?>",
				],
				datasets: [
					{
						label: "Joomla",
						backgroundColor: "#39f",
						data: [<?php echo implode(', ', $backupsdata[1]);?>, <?php echo $akeebadata[1]; ?>]
					},
					<?php
					$params = JComponentHelper::getParams("com_yoursites");
					if ($params->get("supportwp", 0))
					{
					?>
					{
						label: "WordPress/ClassicPress",
						backgroundColor: "#895df6",
						data: [<?php echo implode(', ', $backupsdata[2]);?>, <?php echo $akeebadata[2]; ?>]
					}
					<?php
					}
					?>
				],
			},
			options: {
				maintainAspectRatio: false,
				responsiveAnimationDuration: 500,
				legend: {
					display: true
				},
				animation: {
					duration: 2000
				},
				title: {
					display: true,
					text: '<?php echo JText::_("COM_YOURSITES_DASHBOARD_BACKUPS_SUMMARY_AND_STATUS");?>'
				},
			}
		});

		// Chart 4
		// ========================================================================
		var char4El = document.getElementById('chart4');

		if (char4El) {
			new Chart(char4El, {
				type: 'bar',
				data: {
					labels: ["Completed Backups", "Failed Backups", "Sites Not Installed"],
					datasets: [
						{
							label: "Joomla",
							backgroundColor: "#39f",
							data: [<?php echo implode(', ', $backupsdata[1]);?>, <?php echo $akeebadata[1]; ?>]
						},
						<?php
						$params = JComponentHelper::getParams("com_yoursites");
						if ($params->get("supportwp", 0))
						{
						?>
						{
							label: "WordPress/ClassicPress",
							backgroundColor: "#895df6",
							data: [<?php echo implode(', ', $backupsdata[2]);?>, <?php echo $akeebadata[2]; ?>]
						}
						<?php
						}
						?>
					],
				},
				options: {
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500,
					legend: {
						display: true
					},
					animation: {
						duration: 2000
					},
					title: {
						display: true,
						text: '<?php echo JText::_("COM_YOURSITES_DASHBOARD_TASKS_SUMMARY_AND_STATUS");?>'
					},
				}
			});
		}

		// Chart 5
		// ========================================================================
		var char5El = document.getElementById('chart5');

		new Chart(char5El, {
			type: 'bar',
			data: {

				labels: ["<?php echo JText::_("COM_YOURSITES_DASHBOARD_TOTAL_TASKS");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_NOT_STARTED");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_FAILED");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_RUNNING");?>",
					"<?php echo JText::_("COM_YOURSITES_DASHBOARD_COMPLETED");?>",
				],

				datasets: [
					{
						label: "Joomla",
						backgroundColor: "#39f",
						data: [<?php echo implode(', ', $taskdata[1]);?>]
					},
					<?php
					$params = JComponentHelper::getParams("com_yoursites");
					if ($params->get("supportwp", 0))
					{
					?>
					{
						label: "WordPress/ClassicPress",
						backgroundColor: "#895df6",
						data: [<?php echo implode(', ', $taskdata[2]);?>]
					},
					{
						label: "Other",
						backgroundColor: "#3cba9f",
						data: [<?php echo implode(', ', $taskdata[999]);?>]
					}
					<?php
					}
					?>
				],
			},
			options: {
				maintainAspectRatio: false,
				responsiveAnimationDuration: 500,
				legend: {
					display: true
				},
				animation: {
					duration: 2000
				},
				title: {
					display: true,
					text: '<?php echo JText::_("COM_YOURSITES_DASHBOARD_TASKS_SUMMARY_AND_STATUS");?>'
				},
			}
		});

    </script>

    <form action="<?php echo JRoute::_('index.php?option=com_yoursites'); ?>" method="post" name="adminForm" id="adminForm">
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
