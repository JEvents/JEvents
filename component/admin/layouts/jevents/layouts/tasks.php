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

$hastasks = false;
$activestatus = "";

if (isset($item->tasks))
{
	$statuses = array(
		"0"  => JText::_("COM_YOURSITES_TASKS_UNSTARTED",true),
		"-1" => JText::_("COM_YOURSITES_TASKS_FAILED",true),
		"1"  => JText::_("COM_YOURSITES_TASKS_RUNNING",true),
		"2"  => JText::_("COM_YOURSITES_TASKS_COMPLETED",true)
	);

	foreach ($statuses as $status => $statuslabel)
	{
		if (count($item->tasks[$status]))
		{
			$hastasks = true;
			$activestatus = 'status' . $item->id . "_" . $status;
			break;
		}
	}
}

if (!$hastasks)
{
	return;
}

$idsuffix = 'notitle';
if (!isset($displayData['skiptitle']))
{
	$idsuffix = 'withtitle';
?>
<fieldset class="adminform">
    <legend><?php echo JText::_('COM_YOURSITES_BACKGROUND_TASKS'); ?></legend>
	<?php
	}
    echo '<ul gsl-tab>';

	foreach ($statuses as $status => $statuslabel)
	{

		echo '<li><a href="#">' . $statuslabel . '</a></li>';
	}
	echo '</ul>';

echo '<ul class="gsl-switcher gsl-margin">';

foreach ($statuses as $status => $statuslabel)
{

		if (count($item->tasks[$status]))
		{
			?>
            <li>
            <table class="table">
                <thead>
                <tr>
                    <th>
						<?php echo JText::_('COM_YOURSITES_TASKS_NAME'); ?>
                    </th>
                    <th>
						<?php echo JText::_('COM_YOURSITES_CREATED_DATE'); ?>
                    </th>
                    <th>
						<?php echo JText::_('COM_YOURSITES_STARTED_DATE'); ?>
                    </th>
                    <th>
						<?php echo JText::_('COM_YOURSITES_FINISHED_DATE'); ?>
                    </th>
                </tr>
                </thead>
                <tbody>
				<?php
				foreach ($item->tasks[$status] as $task)
				{
					?>
                    <tr>
                        <td>
                            <strong><?php echo $task->task; ?></strong>
                        </td>
                        <td>
							<?php echo $task->created_time; ?>
                        </td>
                        <td>
							<?php echo $task->started_time; ?>
                        </td>
                        <td>
							<?php echo $task->finished_time; ?>
                        </td>
                    </tr>
					<?php
				}
				?>
                </tbody>
            </table>
            </li>
			<?php
		}
		else {
		    echo '<li> ' . JText::_('COM_YOURSITES_BG_TASK_NONE_FOUND') . '</li>';
        }
	}
   echo '</ul>';

    if (!isset($displayData['skiptitle']))
    {
	    ?>
        </fieldset>
	    <?php
    }
