<?php
/**
 * @version    CVS: JEVENTS_VERSION
 * @package    com_jevents
 * @author     Geraint Edwards <yoursites@gwesystems.com>
 * @copyright  2016-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

// Do not do this in Internet Explorer 10 or lower (Note that MSIE 11 changed the app name to Trident)
if (GSLMSIE10)
{
	return;
}

echo GslHelper::renderVersion();
?>
            </div>

        </div>

    <!-- /CONTENT -->


    </div>

    <!-- OFFCANVAS -->
    <div id="offcanvas-left-panel" class="gsl-offcanvas"  data-gsl-offcanvas="flip: false; overlay: true; container: #gslc;" class="gsl-hidden">
        <div class="gsl-offcanvas-bar gsl-offcanvas-bar-animation gsl-offcanvas-slide">
            <button class="gsl-offcanvas-close gsl-close gsl-icon" type="button" data-gsl-close></button>
            <div class="offcanvas-content"></div>
        </div>
    </div>
    <!-- /OFFCANVAS -->

    <!-- OFFCANVAS -->
    <div id='right-panel-flip' class="gsl-offcanvas-flip gsl-padding-remove gsl-hidden">
        <div id="offcanvas-right-panel" class="gsl-offcanvas" gsl-offcanvas="mode: push; flip: true; overlay: true; container: #right-panel-flip;esc-close: true;bg-close: false">

            <div class="gsl-offcanvas-bar">
                <button class="gsl-offcanvas-close gsl-close gsl-close-flip  gsl-icon" type="button" gsl-close></button>
                <div class="offcanvas-content"></div>
            </div>

        </div>
    </div>
    <!-- /OFFCANVAS -->
    <div id="ysts_debug_messages" ></div>
</div>
<script>
	window.addEventListener('load', function () {
		if (document.getElementById('system-debug'))
        {
	        document.getElementById('ysts_debug_messages').appendChild(document.getElementById('system-debug'));
        }
	});
</script>
<script >
    // remove &#65279; non breaking white space and other joiners that may break the layout - could be used instead of regexp in jevents.php
    document.addEventListener('DOMContentLoaded',  function () {
        var gslc = document.getElementById('gslc');
        if (gslc)
        {
            if(gslc.previousSibling && gslc.previousSibling.nodeType == 3)
            {
                gslc.previousSibling.nodeValue = gslc.previousSibling.nodeValue.trim();
            }
        }
    });
</script>


