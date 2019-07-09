<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

extract($displayData);

if (!$showBackups)
{
    return;
}
$params = JComponentHelper::getParams("com_yoursites");
$dateFormat = JText::_('DATE_FORMAT_LC5');

if ($showBackups && $item->coretype == 999 )
{
    ?>
    <td/>
    <?php
}
else
{
	?>
    <td class="<?php echo $showBackups ? "showbackups" : "hidebackups"; ?>">
		<?php if ($item->akeebaversion && $item->akeebaenabled)
		{
			if ($item->backup && $item->backup !== "[]")
			{
				$backup = json_decode($item->backup);

				$now         = new JDate('+0 seconds');
				$backupstart = new JDate($backup->backupstart);
				// how many days old is the backup?
				$backupAge = $backupstart->diff($now);

				// Use this to set the background colour of the backup
				$success = array(70, 165, 70);
				$danger  = array(189, 54, 47);

				$backupAge = $backupAge->d * 24 + $backupAge->h + $backupAge->i / 60;

				// old backup age defaults to 7 days
				$oldBackupAge = $params->get("oldbackup", 7 * 24);

				// for($d = 0; $d <= $oldBackupAge; $d++)
				//{
				//   $backupAge = $d ;
				if ($backupAge > $oldBackupAge)
				{
					$backupstyle = "style='background-color:rgb(189,54,47);'";
				}
				else
				{
					$colour = 'rgb(';
					for ($c = 0; $c < 3; $c++)
					{
						$colour .= $success[$c] + intval(($danger[$c] - $success[$c]) * $backupAge / $oldBackupAge);
						if ($c < 2)
						{
							$colour .= ",";
						}
						else
						{
							$colour .= ")";
						}
					}
					//     $backupstyle = "style='background-color:$colour;width:20px;height:20px;'";
					$backupstyle = "style='background-color:$colour;'";
				}
				//   echo "<div $backupstyle></div>";
				//}

				$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_AKEEBA_LAST_BACKUP_DATE', true). '"'
					. '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_AKEEBA_CLICK_TO_CREATE_BACKUP", true) . '" '
					. ' data-yspopoptions = \'{"mode" : " hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';

				// convert to timezone
				$startTime = new DateTime($backup->backupstart, $utcTimeZone);
				$startTime->setTimezone($tz);
				$backupstart = $startTime->format($dateFormat);
				?>
                <div class="amounts gsl-text-center ysbtn-group">

                    <a class="gsl-button gsl-button-small hasYsPopover gsl-button-danger" <?php echo $backupstyle; ?>
                       href="#" 
                       onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;createSiteBackups();return false;"
                       title="<?php echo JText::_("COM_YOURSITES_AKEEBA_LAST_BACKUP_DATE", false); ?>"
						<?php echo $tooltip; ?>
                    >
                                                    <span class="jversion">
                                                        <?php
                                                        echo str_replace(" ", "<br>", $backupstart);
                                                        ?>
                                                    </span>
                    </a><a class="gsl-button gsl-button-small gsl-button-primary hasYsTooltip backuplist" <?php echo $backupstyle; ?>
                       href="#" 
                       onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checkSiteBackups();return false;"
                       title="<?php echo JText::_("COM_YOURSITES_CHECK_SITE_BACKUPS", false); ?>"
                    >
                        <div class="icon-loop ">
                        </div>
                        <span class="backuplist">
                                        &nbsp;<br>&nbsp;
                                    </span>
                    </a><a class="gsl-button gsl-button-small gsl-button-primary hasYsTooltip backuplist" <?php echo $backupstyle; ?>
                       href="javascript:void(0);"
                       onclick=" return listItemTask('cb<?php echo $i; ?>', 'sites.listbackups')"
                       title="<?php echo JText::_("COM_YOURSITES_LIST_BACKUPS", false); ?>"
                    >
                        <div class="icon-menu-3 ">
                        </div>
                        <span class="backuplist">
                                        &nbsp;<br>&nbsp;
                                    </span>
                    </a><?php
                    if ($params->get("offerbackupdownloads", 0))
					{
						?><a class="gsl-button gsl-button-small gsl-button-primary hasYsTooltip backuplist" <?php echo $backupstyle; ?>
                           href="#" 
                           onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;downloadLatestBackup();return false;"
                           title="<?php echo JText::_("COM_YOURSITES_TRANSFER_LATEST_BACKUP", false); ?>"
                        >
                            <div class="icon-download ">
                            </div>
                            <span class="backuplist">&nbsp;<br>&nbsp;</span>
                        </a><?php
					}
					?>
                </div>
				<?php
			}
			else
			{
				$tooltip = ' data-yspoptitle = "' . \JText::_('COM_YOURSITES_AKEEBA_VERSION', true). '"'
					. '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_AKEEBA_CLICK_TO_CREATE_BACKUP", true) . '" '
					. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';

				?>
                <div class="amounts gsl-text-center ysbtn-group">
                    <a class="gsl-button gsl-button-small gsl-button-primary hasysPopover"
                       href="#" 
                       onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;createSiteBackups();return false;"
                       title="<?php echo JText::_("COM_YOURSITES_AKEEBA_VERSION", false); ?>"
						<?php echo $tooltip; ?>
                    >
                        <span class="icon-archive" aria-hidden="true"></span>
                        <span class="jversion">
                                          <?php echo $item->akeebaversion; ?><br>
                                    </span>
                    </a><a class="gsl-button gsl-button-small gsl-button-primary hasYsTooltip backuplist"
                       href="#" 
                       onclick="document.adminForm['cb<?php echo $i; ?>'].checked = true;checkSiteBackups();return false;"
                       title="<?php echo JText::_("COM_YOURSITES_CHECK_SITE_BACKUPS", false); ?>"
                    >
                        <div class="icon-loop "></div>
                        <span class="backuplist"><br></span>
                    </a></div>
				<?php
			}
		}
		else
		{
			if ($item->akeebaversion && !$item->akeebaenabled)
			{
				?><div class="center gsl-button gsl-button-small gsl-button-warning">
                    <span class="icon-info" aria-hidden="true"></span>
                    <span class="jversion"><?php echo JText::_("COM_YOURSITES_AKEEBA_NOT_ENABLED", false); ?></span>
                </div>
				<?php

			}
			// if we've not checked extensions then show the icon
			else if (!$item->totalextensions)
			{
				$tooltip = '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_AKEEBA_NOT_INSTALLED_INFO", true) . '" '
					. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';
				// NO do not use JS safe translation for title with tooltip o/w quotes are escaped twice!
				?><div class="center gsl-button gsl-button-small gsl-button-warning hasysPopover"
                     title="<?php echo JText::_("COM_YOURSITES_AKEEBA_NOT_INSTALLED_INFO", false); ?>"
				    <?php echo $tooltip; ?>
                >
                    <span class="icon-checkbox-unchecked" aria-hidden="true"></span>
                    <span class="jversion">
                                                    ?
                                                </span>
                </div><?php
			}
			else
			{
				$tooltip = '  data-yspopcontent = "' . \JText::_("COM_YOURSITES_AKEEBA_NOT_INSTALLED", true) . '" '
					. ' data-yspopoptions = \'{"mode" : "hover", "offset" : 20,"delayHide" : 200, "pos" : "top"}\'';

				?><div class="center gsl-button gsl-button-small gsl-button-warning  hasysPopover"
                       title="<?php echo JText::_("COM_YOURSITES_AKEEBA_NOT_INSTALLED", false); ?>"
				<?php echo $tooltip; ?>
                >
                    <span class="icon-checkbox-unchecked" aria-hidden="true"></span>
                </div><?php

			}
		}
		?>
    </td>
	<?php
}