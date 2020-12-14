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

use Joomla\CMS\Layout\LayoutHelper;


$version = JEventsVersion::getInstance();
?>
    <footer class="gsl-section gsl-section-small gsl-text-center">
	    <?php echo LayoutHelper::render('jevents.translationcredits'); ?>
        <p class="gsl-text-small gsl-text-center"><a href="https://www.jevents.net"><?php echo $version->getLongVersion(); ?></a> : <span style="color:#999999; font-size:9px;"><?php echo $version->getShortCopyright(); ?></span></p>
    </footer>

