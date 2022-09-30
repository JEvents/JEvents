<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: cpanel.php 3119 2011-12-20 14:34:33Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Feed\FeedFactory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;

$params  = ComponentHelper::getParams(JEV_COM_COMPONENT);

?>

<div id="jevents" >
    <!-- CONTENT -->
    <div class="gsl-container gsl-container-expand">
        <div class="gsl-grid gsl-grid-divider gsl-grid-medium gsl-child-width-1-2 gsl-child-width-1-3@m gsl-child-width-1-4@l"    gsl-grid>
            <div>
                <span class="gsl-text-small">
                        <span gsl-icon="icon:calendar" class="gsl-margin-small-right gsl-text-primary"></span>
                        <?php echo Text::_("COM_JEVENTS_TOTAL_EVENTS");?>
                </span>
                <h2 class="gsl-heading-primary gsl-margin-remove gsl-text-primary gsl-h2">
                    <a href="<?php echo Route::_("index.php?option=com_jevents&task=icalevent.list")?>">
	                    <?php echo Text::sprintf("COM_JEVENTS_FUTURE_EVENTS", $this->futureEvents);?>
                    </a>
                </h2>
                <a href="<?php echo Route::_("index.php?option=com_jevents&task=icalevent.list")?>"
                   class="gsl-text-small gsl-text-success gsl-display-block">
                    <span class="gsl-text-success" gsl-icon="icon: history"></span>
	                <?php echo Text::sprintf("COM_JEVENTS_PAST_EVENTS", $this->pastEvents);?>
                </a>
            </div>
            <div>
                <span class="gsl-text-small">
                        <span gsl-icon="icon:calendar"
                              class="gsl-margin-small-right gsl-text-danger"></span>
                        <?php echo Text::_("COM_JEVENTS_TOTAL_EVENTS_UNPUBLISHED");?>
                </span>
                <h2 class="gsl-heading-primary gsl-margin-remove gsl-text-primary gsl-h2">
                    <a href="<?php echo Route::_("index.php?option=com_jevents&task=icalevent.list")?>">
	                    <?php echo $this->unpublishedEvents;?>
                    </a>
                </h2>
            </div>
            <div>
                <span class="gsl-text-small">
                        <span gsl-icon="icon:plus-circle" class="gsl-margin-small-right gsl-text-primary"></span>
                        <?php echo Text::_("COM_JEVENTS_NEW_EVENTS");?>
                </span>
                <h2 class="gsl-heading-primary gsl-margin-remove gsl-text-primary gsl-h2">
                    <a href="<?php echo Route::_("index.php?option=com_jevents&task=icalevent.list")?>">
	                    <?php echo Text::sprintf("COM_JEVENTS_NEW_EVENTS_THIS_Week", $this->newEvents);?>
                    </a>
                </h2>
                <a href="<?php echo Route::_("index.php?option=com_jevents&task=icalevent.list")?>"
                   class="gsl-text-small gsl-text-success gsl-display-block">
                    <span class="gsl-text-success" gsl-icon="icon: plus-circle"></span>
		            <?php echo Text::sprintf("COM_JEVENTS_NEW_EVENTS_THIS_MONTH", $this->newThisMonth);?>
                </a>
            </div>
            <div>
                <span class="gsl-text-small">
                   <span gsl-icon="icon: users;" class="gsl-margin-small-right gsl-text-primary"></span>
                   <?php echo Text::_("COM_JEVENTS_UPCOMING_REGISTRATIONS");?>
                </span>
                <?php if (PluginHelper::isEnabled("jevents", "jevrsvppro")) { ?>
                <h2 class="gsl-heading-primary gsl-margin-remove  gsl-text-primary gsl-h2">
                    <a href="<?php echo Route::_("index.php?option=com_rsvppro&task=sessions.list")?>">
                        <?php echo Text::sprintf("COM_JEVENTS_UPCOMING_REGISTRATIONS_THIS_WEEK", $this->upcomingAttendees);?>
                    </a>
                </h2>
                    <a href="<?php echo Route::_("index.php?option=com_jevents&task=icalevent.list")?>"
                       class="gsl-text-small gsl-text-success gsl-display-block">
                        <span class="gsl-text-success" gsl-icon="icon: users"></span>
		                <?php echo Text::sprintf("COM_JEVENTS_UPCOMING_REGISTRATIONS_THIS_MONTH", $this->upcomingAttendeesThisMonth);?>
                    </a>
                <?php }
                else if (file_exists(JPATH_PLUGINS . "/jevents/jevrsvppro/jevrsvppro.php"))
                {
	                ?>
	                <h2 class="gsl-heading-primary gsl-margin-remove  gsl-text-primary hasYsPopover gsl-h2"
	                    data-yspoptitle="<?php echo Text::_('COM_JEVENTS_REQUIRES_RSVPPRO_NOTENABLED'); ?>"
	                    data-yspopcontent="<?php echo Text::_("COM_JEVENTS_REQUIRES_RSVPPRO_NOTENABLED_DETAIL"); ?>"
	                    ?>
		                <?php echo Text::_("COM_JEVENTS_NOT_ENABLED");?>
	                </h2>
	                <?php
                }
                else {
                    ?>
	                <h2 class="gsl-heading-primary gsl-margin-remove  gsl-text-primary hasYsPopover gsl-h2"
                        data-yspoptitle="<?php echo Text::_('COM_JEVENTS_REQUIRES_RSVPPRO'); ?>"
                        data-yspopcontent="<?php echo Text::_("COM_JEVENTS_REQUIRES_RSVPPRO_DETAIL"); ?>"
                    ?>
                        <?php echo Text::_("COM_JEVENTS_NOT_INSTALLED");?>
                    </h2>
                    <?php
                }
                ?>
            </div>
        </div>
        <hr>
		<?php
		$params = ComponentHelper::getParams("com_jevents");
		if ($params->get("shownews", 1))
		{
			// Via Javascript
			$script = <<< SCRIPT
function decodeHtml(str)
{
    var map =
    {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#039;': "'"
    };
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
}

document.addEventListener('DOMContentLoaded', function() {
var RSS_URL = 'https://www.jevents.net/blog?format=feed&type=rss';
try {
fetch(RSS_URL)
  .then(response => response.text())
  .then(str => new window.DOMParser().parseFromString(str, "text/xml"))
  .then(data => {
    const items = data.querySelectorAll("item");

    let sliderItems = document.querySelector('.gsl-slider-items');
    let count = 0;
    items.forEach(el => {    
   	  if (count >= 3) return;	  

      let html = "";
      let link = el.querySelector('link'); 
      if (link)
        link = link.innerHTML;
      let title = el.querySelector('title');
      if (title) 
        title = decodeHtml(title.innerHTML);
      let description = el.querySelector('description').innerHTML;
      if (description) {
        let doc = new DOMParser().parseFromString(description, 'text/html');
        description = decodeHtml(doc.body.textContent || "");
        description = description.substring(0, 200);
      }
      html += '<li style="padding:0 20px 0 10px;">';
      html += '<span class="feed-link">';
	  html += '<a href="' + link + '" target="_blank">';
      html += title;
      html += '</a></span>';
      html += '<div class="feed-item-description">';
      html += description;
      html += '</div>';
      html += '</li>';
	  sliderItems.insertAdjacentHTML("beforeend", html);
	  count ++;
	  
    });
    console.log(data);
  })
 }
 catch (ex) 
 {
 }
 });
SCRIPT;
			Factory::getApplication()->getDocument()->addScriptDeclaration($script);
			?>
			<div class="gsl-margin-small gsl-width-1-1 gsl-position-relative gsl-card gsl-card-default gsl-card-small gsl-card-hover"
                     style="padding:10px 55px 35px 55px;">
	    <div class="gsl-card-header">
		    <div class="gsl-grid gsl-grid-small">
			    <h4 class="gsl-width-auto">
				    <span gsl-icon="icon:tv; ratio : 2" class="gsl-margin-small-right gsl-text-primary"></span>
				    <?php echo Text::_("COM_JEVENTS_JEVENTS_NEWS");?>
			    </h4>
		    </div>
	    </div>
	    <div class="gsl-card-body gsl-slider ys_newsfeed" gsl-slider="autoplay:true; autoplay-interval:5000; pause-on-hover:true">
		    <ul class=" gsl-slider-items gsl-grid gsl-child-width-1-1" style="width: calc(100% + 20px);">
		    </ul>

		    <a class="gsl-position-center-left gsl-position-small gsl-hidden-hover" href="#"
		       gsl-slidenav-previous gsl-slider-item="previous"></a>
		    <a class="gsl-position-center-right gsl-position-small gsl-hidden-hover" href="#" gsl-slidenav-next
		       gsl-slider-item="next"></a>

		    <ul class="gsl-slider-nav gsl-dotnav gsl-position-bottom-center gsl-padding-small"></ul>
	    </div>
    </div>
	<?php

			/*
			// Get RSS parsed object
			try
			{
				$rssurl = "https://www.jevents.net/blog?format=feed&type=rss";
				$feed   = new FeedFactory;
				$rssDoc = $feed->getFeed($rssurl);
			}
			catch (Exception $e)
			{
				 // echo Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
			}

			if (!empty($rssDoc) && is_object($rssDoc))
			{
				$feed = $rssDoc;
				?>
                <div class="gsl-margin-small gsl-width-1-1 gsl-position-relative gsl-card gsl-card-default gsl-card-small gsl-card-hover"
                     style="padding:10px 55px 35px 55px;">
                    <?php if ($params->get("shownews", 1)) { ?>
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <h4 class="gsl-width-auto">
                                <span gsl-icon="icon:tv; ratio : 2" class="gsl-margin-small-right gsl-text-primary"></span>
                                <?php echo Text::_("COM_JEVENTS_JEVENTS_NEWS");?>
                            </h4>
                        </div>
                    </div>
	                <?php } ?>
                    <div class="gsl-card-body gsl-slider ys_newsfeed" gsl-slider="autoplay:true; autoplay-interval:5000; pause-on-hover:true">
                        <ul class=" gsl-slider-items gsl-grid gsl-child-width-1-1" style="width: calc(100% + 20px);">
							<?php for ($i = 0, $max = min(count($feed), 3); $i < $max; $i++) { ?>
								<?php
								$uri  = $feed[$i]->uri || !$feed[$i]->isPermaLink ? trim($feed[$i]->uri) : trim($feed[$i]->guid);
								$uri  = !$uri || stripos($uri, 'http') !== 0 ? $rssurl : $uri;
								$text = $feed[$i]->content !== '' ? trim($feed[$i]->content) : '';
								?>
                                <li style="padding:0 20px 0 10px;">
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
										$text = OutputFilter::stripImages($text);
										$text = strip_tags($text);
										$text = HTMLHelper::_('string.truncate', $text, 200);
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
			*/
		}
		?>
        <div class="gsl-grid gsl-grid-medium gsl-grid" gsl-grid gsl-height-match="target: > div > .gsl-card">

            <!-- panel -->
            <div class="gsl-width-1-2@l" >
                <div class="gsl-card gsl-card-default gsl-card-small gsl-card-hover">
                    <div class="gsl-card-header">
                        <div class="gsl-grid gsl-grid-small">
                            <div class="gsl-width-auto"><h4><?php echo Text::_("COM_JEVENTS_TOTAL_EVENTS");?></h4></div>
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
                            <div class="gsl-width-auto"><h4><?php echo Text::_("COM_JEVENTS_NEW_EVENTS_CREATED_BY_DAY");?></h4></div>
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
                            <div class="gsl-width-auto"><h4><?php echo Text::_("COM_JEVENTS_UPCOMING_REGISTRATIONS_BY_EVENT");?></h4></div>
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
                            <div class="gsl-width-auto"><h4><?php echo Text::_("COM_JEVENTS_UPCOMING_EVENTS_BY_WEEK");?></h4></div>
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
				labels: <?php echo  json_encode($this->eventsByCat);?>,
				datasets: [
					{
						backgroundColor: ['<?php echo is_array($this->eventsByCatColours) ? implode("', '", $this->eventsByCatColours) : $this->eventsByCatColours ; ?>'],
						data: [<?php echo is_array($this->eventsByCatCounts) ? implode(", ", $this->eventsByCatCounts) : $this->eventsByCatCounts; ?>],
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
				labels: ["<?php echo Text::_("JEV_MONDAY");?>",
					"<?php echo Text::_("JEV_TUESDAY");?>",
					"<?php echo Text::_("JEV_WEDNESDAY");?>",
					"<?php echo Text::_("JEV_THURSDAY");?>",
					"<?php echo Text::_("JEV_FRIDAY");?>",
					"<?php echo Text::_("JEV_SATURDAY");?>",
					"<?php echo Text::_("JEV_SUNDAY");?>",
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
		<?php if (PluginHelper::isEnabled("jevents", "jevrsvppro")) { ?>
		new Chart(document.getElementById('chart3'), {
			type: 'bar',
			data: {
				labels: <?php echo  json_encode($this->attendeeCountsByEvent['title']);?>,
				datasets: [
					{
						backgroundColor: "#39f",
						data: [<?php echo  implode(", ", $this->attendeeCountsByEvent['count']); ?>],
					},
				],
                startrepeat: ['<?php echo  implode("', '", $this->attendeeCountsByEvent['start']); ?>'],
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
					yAxes: [{
						display: true,
						ticks: {
							suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
							precision: 0
						}
					}]
				},
				tooltips: {
					enabled: true,
					mode: 'single',
					callbacks: {
						label: function(tooltipItems, data) {
							return tooltipItems.yLabel + ' @ ' + data.startrepeat[tooltipItems.datasetIndex] ;
						}
					}
				},
			}
		});
		<?php } ?>
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
							labelString: "<?php echo Text::_("COM_JEVENTS_COUNT_BY_WEEK_COMMENCING"); ?>"
						}
					}],
					yAxes: [{
						display: true,
						ticks: {
							suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
							precision: 0
						}
					}]
				}
			}
		});

    </script>

    <form action="<?php echo Route::_('index.php?option=com_jevents'); ?>" method="post" name="adminForm" id="adminForm">
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="redirecturl" value=""/>
        <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
        <input type="hidden" name="form_submitted" value="1"/>
        <input type="hidden" name="baseurl" id="baseurl" value="<?php echo JURI::root(); ?>"/>
        <input type="hidden" name="listlayout" id="listlayout"  value=""/>
        <input type="hidden" id="ystscomponent" value="dashboard"/>
		<?php echo HTMLHelper::_('form.token', array('id' => "tokenid")); ?>
    </form>
    <!-- /CONTENT -->
</div>
