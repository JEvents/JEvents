<?php defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

JEventsHelper::addSubmenu();
$this->sidebar = JHtmlSidebar::render();

HtmlHelper::_('behavior.core');
HtmlHelper::_('bootstrap.tooltip');

$mainspan = 10;
$fullspan = 12;

?>
<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
<?php endif; ?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div id="ysts-main-container">
        <div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
            <div id="jstools clearfix">
                <div class="js-stools-container-filters gsl-child-width-1-3@s gsl-child-width-1-5@m   gsl-grid" gsl-grid>
                    <?php
                    /*
                    if (count($this->languages) > 1)
                    { ?>
                                            <select name="filter_language" class="gsl-select" onchange="this.form.submit()">
                                                <option value=""><?php echo Text::_('JOPTION_SELECT_LANGUAGE'); ?></option>
                                            <?php echo HTMLHelper::_('select.options', HTMLHelper::_('contentlanguage.existing', true, true), 'value', 'text', $this->language); ?>
                                            </select>
                                            <?php
                    }
                     */
                    ?>
                    <div class="js-stools-field-filter gsl-first-column">
                        <?php if ($this->catids)
                    { ?>
                        <select name="filter_catid" class="gsl-select" onchange="this.form.submit()">
                            <option value=""><?php echo Text::_('JOPTION_SELECT_CATEGORY'); ?></option>
                            <?php echo $this->catids; ?>
                        </select>
                    </div>
                    <div class="js-stools-field-filter">
                    <?php } ?>
                        <select name="filter_layout_type" class="gsl-select" onchange="this.form.submit()">
                            <?php echo $this->addonoptions; ?>
                        </select>
                    </div>
                    <div class="js-stools-field-filter">
                        <select name="filter_published" class="gsl-select" onchange="this.form.submit()">
                            <option value=""><?php echo Text::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                            <?php echo HTMLHelper::_('select.options', HTMLHelper::_('jgrid.publishedOptions', array("trash" => 0, "archived" => 0, "all" => 0)), 'value', 'text', $this->filter_published, true); ?>
                        </select>
                    </div>
                </div>
            </div>

             <div class="mainlistblock">

                <div class="mainlist">
                    <table class="adminlist table table-striped">
                    <tbody>
                    <tr>
                        <th width="20" nowrap="nowrap">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </th>
                        <th class="title">
                            <?php echo Text::_('TITLE'); ?>
                        </th>
                        <th class="title">
                            <?php echo Text::_('NAME'); ?>
                        </th>
                        <?php
                        if (count($this->languages) > 1)
                        { ?>
                            <th class="center">
                                <?php echo Text::_('JGRID_HEADING_LANGUAGE'); ?>
                            </th>
                            <?php
                        }
                        if ($this->catids)
                        { ?>
                            <th class="center">
                                <?php echo Text::_('JCATEGORY'); ?>
                            </th>
                        <?php } ?>
                        <th width="10%" nowrap="nowrap" class="center"><?php echo Text::_('JEV_PUBLISHED'); ?></th>
                        <th width="5">
                            <?php echo Text::_('ID'); ?>
                        </th>
                    </tr>
                    <?php
                    $k = 0;
                    for ($i = 0, $n = count($this->items); $i < $n; $i++)
                    {
                        $row = &$this->items[$i];

                        if (strpos($row->name, "com_") === 0)
                        {
                            $lang  = Factory::getLanguage();
                            $parts = explode(".", $row->name);
                            $lang->load($parts[0]);
                        }
                        $link = Route::_('index.php?option=' . JEV_COM_COMPONENT . '&task=defaults.edit&id=' . $row->id);
                        ?>
                        <tr class="<?php echo "row$k"; ?>">
                            <td width="20">
                                <?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
                            </td>
                            <td>
                                        <span class="editlinktip hasTip"
                                              title="<?php echo Text::_('JEV_Edit_Layout'); ?>::<?php echo $this->escape(Text::_($row->title)); ?>">
                                            <a href="<?php echo $link; ?>">
                                        <?php echo $this->escape(Text::_($row->title)); ?></a>
                                        </span>
                            </td>
                            <td>
                                <?php echo $this->escape($row->name); ?>

                            </td>
                            <?php
                            if (count($this->languages) > 1)
                            { ?>
                                <td class="center">
                                    <?php echo $this->translationLinks($row);
                                    /*
                                    if ($row->language == '*'):
                                         echo Text::alt('JALL', 'language');
                                    else:
                                        echo $row->language_title ? $this->escape($row->language_title) : Text::_('JUNDEFINED');
                                    endif;
                                     */
                                    ?>
                                </td>
                            <?php } ?>
                            <?php if ($this->catids)
                            { ?>
                                <td class="center">
                                    <?php if ($row->catid == '0'): ?>
                                        <?php echo Text::alt('JALL', 'language'); ?>
                                    <?php else: ?>
                                        <?php echo $row->category_title ? $this->escape($row->category_title) : Text::_('JUNDEFINED'); ?>
                                    <?php endif; ?>
                                </td>
                            <?php } ?>

                            <td class="center">
                                <?php
                                $img = $row->state ? HTMLHelper::_('image', 'admin/tick.png', '', array('title' => ''), true) : HTMLHelper::_('image', 'admin/publish_x.png', '', array('title' => ''), true);
                                ?>
                                <a href="javascript: void(0);"
                                   onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','<?php echo $row->state ? 'defaults.unpublish' : 'defaults.publish'; ?>')"><?php echo $img; ?></a>
                            </td>
                            <td>
                                <?php echo $i + 1; ?>
                            </td>
                        </tr>
                        <?php
                        $k = 1 - $k;
                    }
                    ?>
                    <tr>
                        <th align="center" colspan="10"> </th>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>

            <input type="hidden" name="option" value="<?php echo JEV_COM_COMPONENT; ?>"/>
            <input type="hidden" name="task" value="defaults.list"/>
            <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </div>
    </div>
</form>