/**
 * @version    CVS: JEVENTS_VERSION
 * @package    com_yoursites
 * @author     Geraint Edwards
 * @copyright  2017--JEVENTS_COPYRIGHT GWESystems Ltd
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

document.addEventListener('DOMContentLoaded', function() {
    // set container scope for code
    gslUIkit.container = document.getElementById('gslc');

    // reveal right top menu icons
    var navbar = document.querySelector('.gsl-navbar-right  .gsl-navbar-nav');
    if (navbar) {
        navbar.classList.remove('gsl-hidden');
    }

})

ys_popover(".hasYsPopover");

